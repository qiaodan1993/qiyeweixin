<?php

namespace third_application;

class OwnWeWorkMessage extends OwnWeWorkBase
{
    //全局调用凭证
    private $access_token;

    private $send_message_url = 'https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token=ACCESS_TOKEN';
    public function __construct($enterprise_auhtorization)
    {
        parent::__construct();
        $this->access_token = $this->getAccessToken($enterprise_auhtorization);
        $this->agent_id = $this->getAgentId($enterprise_auhtorization);
    }
    /**
     * 发送文本消息
     *
     * @return void
     */
    public function sendTextMessage($content = '欢迎关注友客帮！')
    {
        $url = str_replace('ACCESS_TOKEN', $this->access_token, $this->send_message_url);
        $data = [
            "touser" => "@all",
            "msgtype" => "text",
            "agentid" => $this->agent_id,
            "text" => [
                "content" => $content,
            ],
        ];
        $post_data = json_encode($data);
        $ret = send_post($url, $post_data);
        return $ret;
    }

    /**
     * 发送图文消息
     *
     * @param [type] $agent_id
     * @return void
     */
    public function sendNewsMessage($text = '欢迎关注友客帮', $pic_url = 'https://ebaina.oss-cn-hangzhou.aliyuncs.com/res/avatar/noavatar_middle.gif')
    {
        $url = str_replace('ACCESS_TOKEN', $this->access_token, $this->send_message_url);
        $data = [
            "touser" => "@all",
            "msgtype" => "news",
            "agentid" => $this->agent_id,
            "news" => [
                "articles" => [
                    "title" => $text,
                    "url" => "https://www.baidu.com",
                    "picurl" => $pic_url,
                ],
            ],
        ];
        $post_data = json_encode($data);
        $ret = send_post($url, $post_data);
        logger($ret);
        return $ret;
    }
}
