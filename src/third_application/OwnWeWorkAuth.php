<?php

namespace third_application;
//企业微信授权类

class OwnWeWorkAuth extends OwnWeWorkBase
{
    //获取企业永久授权码
    private $get_permanent_url = 'https://qyapi.weixin.qq.com/cgi-bin/service/get_permanent_code?suite_access_token=SUITE_ACCESS_TOKEN';

    /**
     * 获得企业永久授权码(因为临时授权码只能使用一次 根据官方推荐 以user_id为主键 存表)
     *
     * @return obj
     */
    public function savePermanentCode($auth_code)
    {
        $url = str_replace('SUITE_ACCESS_TOKEN', $this->suite_access_token, $this->get_permanent_url);
        $data = [
            'auth_code' => $auth_code,
        ];

        $post_data = json_encode($data);
        $ret =  send_post($url, $post_data);
        if ($ret) {
            $ret_json = json_decode($ret, true);
            //授权信息 存在更新 没有则创建一条 返回授权信息
            $enterprise_auhtorization = EnterpriseAuthorization::where('auth_corp_id', $ret_json['auth_corp_info']['corpid'])->first();
            if (!$enterprise_auhtorization) {
                $enterprise_auhtorization = new EnterpriseAuthorization;
            }
            $enterprise_auhtorization->auth_corp_id = $ret_json['auth_corp_info']['corpid'];
            $enterprise_auhtorization->user_id = $ret_json['auth_user_info']['userid'];
            $enterprise_auhtorization->access_token = $ret_json['access_token'];
            $enterprise_auhtorization->access_token_time = date('Y-m-d H:i:s', (time() + $ret_json['expires_in']));

            $enterprise_auhtorization->info = $ret;

            $res = $enterprise_auhtorization->save();
            if ($res) {
                return $enterprise_auhtorization;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
