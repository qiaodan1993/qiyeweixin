
<?php
require "../vendor/autoload.php";

$smsg_ary = [
    'SuiteId' => 'ww49174a28ef4da2d5',
    'AuthCode' => 'eIQrq53LBRyVEFXIr8PO8kPzFTkQUqIzQkiIA-kaCZkVnpczH--p4Xm258zW9odM3CM5a1MLEN4QmXoUG3IjOyMvj_x0zp_j354KxXckvSY',
    'InfoType' => 'suite_ticket',
    'TimeStamp' => '1649314770',
    'SuiteTicket' => 555666,
];
$WEWORK_SUITE_ID = 1111;
$WEWORK_SUITE_SECRET = 2222;




switch ($smsg_ary['InfoType']) {
        //管理员添加了应用 给用户推送消息
    case 'create_auth':
        include_once("../app/Http/Controllers/own_weworkapi_php/OwnWeWorkAuth.class.php");
        include_once("../app/Http/Controllers/own_weworkapi_php/OwnWeWorkMessage.class.php");
        dd(__FILE__);
        $base_obj = new \OwnWeWorkAuth(env('WEWORK_SUITE_ID'), env('WEWORK_SUITE_SECRET'));
        $enterprise_auhtorization = $base_obj->savePermanentCode($smsg_ary['AuthCode']);
        if (!$enterprise_auhtorization) {
            break;
        }

        $message_obj = new \OwnWeWorkMessage($enterprise_auhtorization);
        $message_obj->sendNewsMessage();
        break;
        //管理员删除了应用 
    case 'cancle_auth':
        break;
        //微信suite_ticket 回调
    case 'suite_ticket':
        //suite_ticket 存储 微信十分钟请求一次
        $base_obj = new \access_file\AccessFile();
        $enterprise_auhtorization = $base_obj->setSuiteTicket($smsg_ary['SuiteTicket']);
        echo 123;
        die;
}







echo 123;
die;
$Test = new Qiye\Test();
$Test->test(); // 