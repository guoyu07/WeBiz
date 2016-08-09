<?php

namespace Apps\Html\Controllers;

use Common\Log\Log;
use Common\WxPay\PayNotifyCallBack;

class NotifyController
{
    public function start()
    {
        //初始化日志
        $log = Log::Init(WXPAY_LOG_DIR. DIRECTORY_SEPARATOR . date('Y-m-d') . '.log', 15);
        Log::DEBUG("begin notify: notify");
        $notify = new PayNotifyCallBack();
        $notify->Handle(false);
    }
}