<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class WxBizMsg
 * 跟微信交互的接口
 */
class Wechat extends BLH_Controller {
    public function __construct()
    {
        parent::__construct(false);
    }

	public function receive() {
	    // 记录微信请求过来的参数列表
        log_message('debug',sprintf('%s, GET:%s, POST:%s', __METHOD__, json_encode($_GET), json_encode($_POST)));

        $this->load->library('ErrorCode');
	    if (empty($_GET['timestamp']) || empty($_GET['nonce'])
            || empty($_GET['echostr']) || empty($_GET['signature'])) {
	        BLH_Utilities::outputError(ErrorCode::$ParseXmlError, ErrorCode::getMsg(ErrorCode::$ParseXmlError));
        }

        // 第三方发送消息给公众平台
        $timeStamp = isset($_GET['timestamp']) ? $_GET['timestamp'] : '';
        $nonce     = isset($_GET['nonce']) ? $_GET['nonce'] : '';
        $signature = isset($_GET['signature']) ? $_GET['signature'] : '';
        $echostr   = isset($_GET['echostr']) ? $_GET['echostr'] : 'unknow';

        // $text = "<xml><ToUserName><![CDATA[oia2Tj我是中文jewbmiOUlr6X-1crbLOvLw]]></ToUserName><FromUserName><![CDATA[gh_7f083739789a]]></FromUserName><CreateTime>{$timeStamp}</CreateTime><MsgType><![CDATA[video]]></MsgType><Video><MediaId><![CDATA[eYJ1MbwPRJtOvIEabaxHs7TX2D-HV71s79GUxqdUkjm6Gs2Ed1KF3ulAOA9H1xG0]]></MediaId><Title><![CDATA[testCallBackReplyVideo]]></Title><Description><![CDATA[testCallBackReplyVideo]]></Description></Video></xml>";

        $this->load->library('WxBizMsgCrypt');
        $wechatMsgCrypt = new WXBizMsgCrypt(WECHAT_TOKEN, WECHAT_AESKEY, WECHAT_APPID);
        // 校验签名
        $isVerify = $wechatMsgCrypt->checkSignature($timeStamp, $nonce, $signature);
        if ( ! $isVerify) {
            BLH_Utilities::outputError(ErrorCode::$ValidateSignatureError, ErrorCode::getMsg(ErrorCode::$ValidateSignatureError));
        }
        echo $echostr;
        exit;

        $encryptMsg = '';
        $errCode = $pc->encryptMsg($text, $timeStamp, $nonce, $encryptMsg);
        if ($errCode == 0) {
            print("加密后: " . $encryptMsg . "<br />\n");
        } else {
            print($errCode . "\n");
        }

        $xml_tree = new DOMDocument();
        $xml_tree->loadXML($encryptMsg);
        $array_e = $xml_tree->getElementsByTagName('Encrypt');
        $array_s = $xml_tree->getElementsByTagName('MsgSignature');
        $encrypt = $array_e->item(0)->nodeValue;
        $msg_sign = $array_s->item(0)->nodeValue;

        $format = "<xml><ToUserName><![CDATA[toUser]]></ToUserName><Encrypt><![CDATA[%s]]></Encrypt></xml>";
        $from_xml = sprintf($format, $encrypt);

        // 第三方收到公众号平台发送的消息
        $msg = '';
        $errCode = $pc->decryptMsg($msg_sign, $timeStamp, $nonce, $from_xml, $msg);
        if ($errCode == 0) {
            print("解密后: " . $msg . "\n");
        } else {
            print($errCode . "\n");
        }
	}
}
