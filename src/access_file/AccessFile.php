<?php

namespace access_file;

class AccessFile
{
    public function __construct()
    {
        $this->suite_ticket_file = 'suite_ticket_file.text';
    }
    /**
     * 从文件中获取suiteTicket
     *
     * @return void
     */
    public function getSuiteTicket()
    {
        if (!file_exists($this->suite_ticket_file)) {
            return false;
        }
        $content = file_get_contents($this->suite_ticket_file);
        $suite_ticket = json_decode($content, true);
        return $suite_ticket;
    }

    /**
     * 向文件写入accessTicket
     *
     * @return void
     */
    public function setSuiteTicket($suite_ticket)
    {

        $suite_ticket_file = fopen($this->suite_ticket_file, "w");
        $text = json_encode([
            'suite_ticket' => $suite_ticket,
        ]);
        $res = fwrite($suite_ticket_file, $text);
        var_dump($res);
        fclose($suite_ticket_file);
        return true;
    }
}
