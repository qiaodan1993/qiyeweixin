<?php

namespace third_application;

class OwnWeWorkBase
{
    //服务商方 suite_id
    private $suite_id;
    //服务商方 suite_secret
    private $suite_secret;
    //微信每十分钟会更新 通过redis进行获取
    private $suite_ticket;

    //获取第三方应用凭证url
    private $get_suite_token_url = 'https://qyapi.weixin.qq.com/cgi-bin/service/get_suite_token';

    //获取企业凭证
    private $get_access_token_url = 'https://qyapi.weixin.qq.com/cgi-bin/service/get_corp_token?suite_access_token=SUITE_ACCESS_TOKEN';

    public function __construct($wework_suite_id, $wework_suite_secret)
    {
        //参数初始化
        $this->file = 'suite_ticket.txt';
        $this->suite_id = $wework_suite_id;
        $this->suite_secret = $wework_suite_secret;

        //suiteticket 必须在 suite token 之前
        $this->suite_ticket = $this->getSuiteTicket();
        $this->suite_access_token = $this->getSuiteAccessToken();
    }


    //获取第三方应用凭证 如果超过了两个小时 要重新获取
    private function getSuiteAccessToken()
    {
        $set_time = $this->redis->get('wework_suite_access_token_time'); //上一次获取suite_access_token时间戳
        $now_time = time();
        $suite_access_token = '';
        if ($now_time - $set_time < 7000) {
            //suiteaccesstoken有效期7200秒 没有过期 从redis拿 过期了 请求微信 获取新的
            $suite_access_token = $this->getSuiteAccessTokenByRedis();
        } else {
            $data = [
                "suite_id" => $this->suite_id,
                "suite_secret" => $this->suite_secret,
                "suite_ticket" => $this->suite_ticket,
            ];
            $post_data = json_encode($data);
            $ret =  send_post($this->get_suite_token_url, $post_data);
            $ret_ary = json_decode($ret, true);
            if ($ret_ary['suite_access_token']) {
                $this->redis->set('wework_suite_access_token', $ret_ary['suite_access_token']);
                $this->redis->set('wework_suite_access_token_time', time());
                $suite_access_token = $ret_ary['suite_access_token'];
            }
        }
        return $suite_access_token;
    }

    protected function setSuiteTicket()
    {
        echo 444444;
        die;
    }
    //获取suite ticket 我方是存在redis  封装可以考虑存在其他地方
    private function getSuiteTicket()
    {
        $file_obj = new \access_file\AccessFile();
        return  $file_obj->getSuiteTicket();
    }

    //缓存 suite_access_token
    private function getSuiteAccessTokenByRedis()
    {
        return $this->redis->get('wework_suite_access_token');
    }


    /**
     * 获取企业调用凭证
     *
     * @param [type] $permanent_code
     * @return string
     */
    protected function getAccessToken($enterprise_auhtorization)
    {
        $access_token = '';
        if (date('Y-m-d H:i:s') < $enterprise_auhtorization->access_token_time) {
            //说明当前没有超过 企业凭证有效期 用老的就可以
            $access_token = $enterprise_auhtorization->access_token;
        } else {

            // 通过接口 获取新的企业凭证 更新到数据库 后期调用接口直接使用
            $url = str_replace('SUITE_ACCESS_TOKEN', $this->suite_access_token, $this->get_access_token_url);
            $info = json_decode($enterprise_auhtorization->info, true);
            $data = [
                "auth_corpid" => $info['auth_corp_info']['corpid'],
                "permanent_code" => $info['permanent_code'],
            ];
            $post_data = json_encode($data);
            $ret =  send_post($url, $post_data);
            $ret_ary = json_decode($ret, true);
            if ($ret_ary['access_token']) {
                $enterprise_auhtorization->access_token = $ret_ary['access_token'];
                $enterprise_auhtorization->access_token_time = date('Y-m-d H:i:s', (time() + $ret_ary['expires_in']));
                $enterprise_auhtorization->save();
            }
        }
        return $access_token;
    }

    /**
     * 获得授权方应用id  发送消息时等需要用到
     *
     * @return void
     */
    protected function getAgentId($enterprise_auhtorization, $name = '友客帮')
    {
        $enterprise_auhtorization_info_json = json_decode($enterprise_auhtorization->info, true);
        $agents = $enterprise_auhtorization_info_json['auth_info']['agent'];
        $agent_id = '';
        foreach ($agents as $agent) {
            if ($agent['name'] == $name) {
                $agent_id = $agent['agentid'];
            }
        }
        return $agent_id;
    }
}
