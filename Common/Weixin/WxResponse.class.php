<?php

namespace Common\Weixin;

class WxResponse
{
    public static function response($postObj, $content, $type)
    {
        switch ($type) {
            case WxConstants::MSGTYPE_TEXT:
                $result = self::responseText($postObj, $content);
                break;
            case WxConstants::MSGTYPE_IMAGE:
                $result = self::responseImage($postObj, $content);
                break;
            case WxConstants::MSGTYPE_VOICE:
                $result = self::responseVoice($postObj, $content);
                break;
            case WxConstants::MSGTYPE_NEWS:
                $result = self::responseNews($postObj, $content);
                break;
            default:
                $result = self::transmitService($postObj);
                break;
        }
        return $result;
    }

    //被动回复文本消息
    protected static function responseText($postObj, $reply, $flag = 0)
    {
        $textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[%s]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                    <FuncFlag>%d</FuncFlag>
                    </xml>";
        $msgType = WxConstants::MSGTYPE_TEXT;
        $result = sprintf($textTpl, $postObj->FromUserName, $postObj->ToUserName, time(), $msgType, $reply, $flag);
        return $result;
    }

    //被动回复图片消息
    protected static function responseImage($postObj, $media_id)
    {
        $media_id = trim($media_id);
        $picTpl = "<xml>
			  	   <ToUserName><![CDATA[%s]]></ToUserName>
			       <FromUserName><![CDATA[%s]]></FromUserName>
			  	   <CreateTime>%s</CreateTime>
			  	   <MsgType><![CDATA[%s]]></MsgType>
		      	   <Image>
			  	   <MediaId><![CDATA[%s]]></MediaId>
			  	   </Image>
			  	   </xml>";
        $msgType = WxConstants::MSGTYPE_IMAGE;
        $result = sprintf($picTpl, $postObj->FromUserName, $postObj->ToUserName, time(), $msgType, $media_id);
        return $result;
    }

    //被动回复语音消息
    protected static function responseVoice($postObj, $media_id)
    {
        $media_id = trim($media_id);
        $voiceTpl = "<xml>
			  	   <ToUserName><![CDATA[%s]]></ToUserName>
			       <FromUserName><![CDATA[%s]]></FromUserName>
			  	   <CreateTime>%s</CreateTime>
			  	   <MsgType><![CDATA[%s]]></MsgType>
		      	   <Voice>
			  	   <MediaId><![CDATA[%s]]></MediaId>
			  	   </Voice>
			  	   </xml>";
        $msgType = WxConstants::MSGTYPE_VOICE;
        $result = sprintf($voiceTpl, $postObj->FromUserName, $postObj->ToUserName, time(), $msgType, $media_id);
        return $result;
    }

    //被动回复图文消息
    protected static function responseNews($postObj, $newsArray)
    {
        $itemTpl = "<item>
                    <Title><![CDATA[%s]]></Title>
                    <Description><![CDATA[%s]]></Description>
                    <PicUrl><![CDATA[%s]]></PicUrl>
                    <Url><![CDATA[%s]]></Url>
                    </item>";
        $item_str = "";
        if (array_key_exists(0, $newsArray)) {
            foreach ($newsArray as $item) {
                $item_str .= sprintf($itemTpl, $item['title'], $item['description'], $item['picUrl'], $item['url']);
            }
        } else {
            $item_str .= sprintf($itemTpl, $newsArray['title'], $newsArray['description'], $newsArray['picUrl'], $newsArray['url']);
        }
        $newsTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[%s]]></MsgType>
                    <ArticleCount>%s</ArticleCount>
                    <Articles>
                    $item_str
                    </Articles>
                    <FuncFlag>1</FuncFlag>
                    </xml>";
        $msgType = WxConstants::MSGTYPE_NEWS;
        $result = sprintf($newsTpl, $postObj->FromUserName, $postObj->ToUserName, time(), $msgType, count($newsArray));
        return $result;
    }

    //将消息转发至多客服系统
    protected static function transmitService($postObj)
    {
        $xmlTpl = "<xml>
     			   <ToUserName><![CDATA[%s]]></ToUserName>
     			   <FromUserName><![CDATA[%s]]></FromUserName>
     			   <CreateTime>%s</CreateTime>
     			   <MsgType><![CDATA[transfer_customer_service]]></MsgType>
 				   </xml>";
        $result = sprintf($xmlTpl, $postObj->FromUserName, $postObj->ToUserName, time());
        return $result;
    }
}