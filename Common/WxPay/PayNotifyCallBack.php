<?php

namespace Common\WxPay;

use Apps\Models\OrderModel;

class PayNotifyCallBack extends WxPayNotify
{
    //查询订单
    public function Queryorder($transaction_id)
    {
        $input = new WxPayOrderQuery();
        $input->SetTransaction_id($transaction_id);
        $result = WxPayApi::orderQuery($input);
        Log::DEBUG("query:" . json_encode($result));
        if (array_key_exists("return_code", $result)
            && array_key_exists("result_code", $result)
            && $result["return_code"] == "SUCCESS"
            && $result["result_code"] == "SUCCESS"
        ) {
            return true;
        }
        return false;
    }

    //重写回调处理函数
    public function NotifyProcess($data, &$msg)
    {
        //Log::DEBUG("call back:" . json_encode($data));
        if (!empty($data['coupon_count'])) {
            for ($i = 0; $i < $data['coupon_count']; $i++) {
                $data['coupon_detail'][$i] = array(
                    'coupon_batch_id_$' . $i = $data['coupon_batch_id_$' . $i],
                    'coupon_type_$' . $i = $data['coupon_type_$' . $i],
                    'coupon_id_$' . $i = $data['coupon_id_$' . $i],
                    'coupon_fee_$' . $i = $data['coupon_fee_$' . $i]
                );
            }
            $data['coupon_detail'] = json_encode($data['coupon_detail']);
        }
        $order = new OrderModel();
        $order->set($data);

        if (!array_key_exists("transaction_id", $data)) {
            $msg = "输入参数不正确";
            return false;
        }
        //查询订单，判断订单真实性
        if (!$this->Queryorder($data["transaction_id"])) {
            $msg = "订单查询失败";
            return false;
        }

        return true;
    }
}
