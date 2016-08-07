<?php

namespace Apps\Weixin\Controllers;

use Common\Libs\Functions;
use Common\Weixin\WxConstants;
use Config\WxConfig;

class WaiterController extends CommonController
{
    protected function getActionController(){
        return new WaiterActionController();
    }

    protected function handleText(array $user_info, $postObj)
    {
        $data = $this->analyzeContent(trim($postObj->Content));
        if ($data) {
            $action = $this->getActionController();
            $result = $action->doAction($data);
        } else {
            $result = $this->autoReply(array('msg_type' => WxConstants::MSGTYPE_TEXT, 'keyword' => Functions::clearPunctuation($postObj->Content)), $user_info);
        }
        return $result;
    }

    protected function handleImage(array $user_info, $postObj)
    {
        return array('type' => WxConstants::MSGTYPE_TEXT, 'content' => trim($postObj->MediaId));
    }

    protected function handleVoice(array $user_info, $postObj)
    {
        $result = array('type' => WxConstants::MSGTYPE_TEXT, 'content' => trim($postObj->MediaId));
        if ($postObj->Recognition) {
            $badword = $this->wordFilter($postObj->Recognition);
            if ($badword) {
                $result = array('type' => WxConstants::MSGTYPE_TEXT, 'content' => $badword);
            }
        }
        return $result;
    }

    //分析输入内容
    protected function analyzeContent($content)
    {
        $text = WxConfig::COMMAND_TEXT;
        $image = $text . WxConfig::COMMAND_IMAGE;
        $voice = $text . WxConfig::COMMAND_VOICE;
        $pattern = '/^([A-Za-z0-9]+)(\\' . $image . '|\\' . $voice . '|\\' . $text . ')(.*)$/';
        if (preg_match($pattern, $content, $data)) {
            switch ($data[2]) {
                case $image:
                    $result = ['action' => 'sendimage', 'argument' => ['touserid' => intval($data[1]), 'mediaid' => $data[3]]];
                    break;
                case $voice:
                    $result = ['action' => 'sendvoice', 'argument' => ['touserid' => intval($data[1]), 'mediaid' => $data[3]]];
                    break;
                default:
                    if (is_numeric($data[1])) {
                        $result = $this->wordFilter($data[3]);
                        if (empty($result)) {
                            $result = ['action' => 'sendtext', 'argument' => ['touserid' => intval($data[1]), 'content' => $data[3]]];
                        }
                    } else {
                        $logfile = LOG_DIR . DIRECTORY_SEPARATOR . 'waiter_errors.log';
                        if (!file_exists($logfile)) fopen($logfile, "w");
                        error_log(date("[Y-m-d H:i:s]") . json_encode($data) . "\n", 3, $logfile);
                        $result = ['action' => $data[1], 'argument' => empty($data[3]) ? null : $data[3]];
                    }
                    break;
            }
        } else {
            $result = false;
        }
        return $result;
    }

    //不良信息过滤
    protected function wordFilter($message)
    {
        $dict = array(
            '个人微信号',
            '妈的', '丫的', '妈蛋', '生殖器', '阴茎', '阴道', '性交', '同性恋', '操你',
            '混蛋', '傻逼', '你妹的', '找死'
        );
        $message = Functions::clearPunctuation($message);
        $badword = '';
        foreach ($dict as $f) {
            if ($message == $f || stripos($message, $f)) $badword .= '【' . $f . '】，';
        }
        return empty($badword) ? false : '您发送的消息含有敏感词汇' . $badword . '不能发送给顾客，请您注意!';
    }
}