function jsApiCall() {
    WeixinJSBridge.invoke(
        'getBrandWCPayRequest',
        jsApi,
        function (res) {
            WeixinJSBridge.log(res.err_msg);
            if (res.err_msg == "get_brand_wcpay_request:ok") {
                // message: "微信支付成功!"
                sendOrder(order);
                WeixinJSBridge.call('closeWindow');
            } else {
                // message: "已取消微信支付!"
                if (showdiv_display = document.getElementById('toast').style.display == 'none') {//如果show是隐藏的
                    document.getElementById('toast').style.display = 'block';//show的display属性设置为block（显示）
                }
            }
        }
    );
}

function sendOrder(order_str) {
    var order = eval('(' + order_str + ')');
    xmlhttp = new XMLHttpRequest();
    xmlhttp.open("GET", "index.php?c=action&client=" + order.client + "&expert=" + order.expert + '&time=' + order.time, true);
    xmlhttp.send();
}

function returnFailureUrl(url) {
    window.location.replace(url);
}

function callpay() {
    if (typeof WeixinJSBridge == "undefined") {
        if (document.addEventListener) {
            document.addEventListener('WeixinJSBridgeReady', jsApiCall(), false);
        } else if (document.attachEvent) {
            document.attachEvent('WeixinJSBridgeReady', jsApiCall());
            document.attachEvent('onWeixinJSBridgeReady', jsApiCall());
        }
    } else {
        jsApiCall();
    }
}
