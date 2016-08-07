<?php

namespace Apps\Html\Controllers;

use Common\WxPay\CLogFileHandler;
use Common\WxPay\Log;
use Common\WxPay\PayNotifyCallBack;

class NotifyController
{
    public function start()
    {
        //初始化日志
        $logHandler = new CLogFileHandler(WXPAY_LOG_DIR. DIRECTORY_SEPARATOR . date('Y-m-d') . '.log');
        $log = Log::Init($logHandler, 15);
        Log::DEBUG("begin notify: notify");
        $notify = new PayNotifyCallBack();
        $notify->Handle(false);
    }
}