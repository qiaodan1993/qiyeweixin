<?php

namespace own_application;

class OwnWeWorkMenu extends OwnWeWorkBaseInside
{
    //全局调用凭证
    private $access_token;

    private $create_menu_url = 'https://qyapi.weixin.qq.com/cgi-bin/menu/create?access_token=ACCESS_TOKEN&agentid=AGENTID';
    public function __construct($enterprise_auhtorization)
    {
        parent::__construct();
        $this->access_token = $this->getAccessToken();

        $this->agent_id = env('WEWORK_QIYE_AGENTID');
    }
    /**
     * 创建菜单
     *
     * @return void
     */
    public function createMenu()
    {
        $url = str_replace('ACCESS_TOKEN', $this->access_token, $this->create_menu_url);
        $url = str_replace('AGENTID', $this->agent_id, $url);

        $data = [
            "button" => [
                [
                    "type" => "click",
                    "name" => "今日歌曲",
                    "key" => "V1001_TODAY_MUSIC"
                ],
                [
                    "name" => "菜单",
                    "sub_button" => [
                        [
                            "type" => "view",
                            "name" => "搜索",
                            "url" => "http://www.soso.com/"
                        ],
                        [
                            "type" => "click",
                            "name" => "赞一下我们",
                            "key" => "V1001_GOOD"
                        ]
                    ]
                ]
            ]
        ];
        $post_data = json_encode($data);
        $ret = send_post($url, $post_data);
        dd($ret);
    }
}
