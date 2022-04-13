<?php
namespace own_application;
class OwnWeWorkBaseInside
{
    //企业 corp_id
    private $corp_id;
    //企业 suite_secret
    private $corp_secret;

    //获取凭证
    private $get_access_token_url = 'https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=ID&corpsecret=SECRET';

    private $access_token;
    public function __construct()
    {
        //参数初始化
        $this->redis = app('redis');
        $this->corp_id = env('WEWORK_CORPID');
        $this->corp_secret = env('WEWORK_CORPSECRET');

        //suiteticket 必须在 suite token 之前
        $this->access_token = $this->getAccessToken();
    }


    //获取应用凭证 如果超过了两个小时 要重新获取
    protected function getAccessToken()
    {
        $url = str_replace('ID', $this->corp_id, $this->get_access_token_url);
        $url = str_replace('SECRET', $this->corp_secret, $url);
        $set_time = $this->redis->get('wework_inside_access_token_time'); //上一次获取suite_access_token时间戳
        $now_time = time();
        $access_token = '';
        if ($now_time - $set_time < 7000) {
            //suiteaccesstoken有效期7200秒 没有过期 从redis拿 过期了 请求微信 获取新的
            $access_token = $this->getAccessTokenByRedis();
        } else {
            $ret =  file_get_contents($url);
            $ret_ary = json_decode($ret, true);
            if ($ret_ary['access_token']) {
                $this->redis->set('wework_inside_access_token', $ret_ary['access_token']);
                $this->redis->set('wework_inside_access_token_time', time());
                $access_token = $ret_ary['access_token'];
            }
        }
        return $access_token;
    }

    //缓存 suite_access_token
    private function getAccessTokenByRedis()
    {
        return $this->redis->get('wework_inside_access_token');
    }
}
