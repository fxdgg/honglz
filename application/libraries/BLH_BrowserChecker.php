<?php
/**
 * BLH_BrowserChecker.php.
 * @package BLH_BrowserChecker
 */
/**
 * A detector to find out type, name or version of mmobile devices and browsers.
 * For those can't be identified, try to find out if it supports wap 1.0 or wap 
 * 2.0.
 * 
 * 2011/6/28 support to identify safari on ipad/iphone and chrome lite on android.
 *
 * Example:
 * 
 * <code>
 * <?php
 * $bc = new BLH_BrowserChecker();
 * if ($bc->isIOS()) {
 *     // ios compatible page
 * } else if ($bc->isAndroid()) {
 *     switch ($bc->getAndroidVersion()) {
 *     case "1.5":
 *         break;
 *     case "2.2":
 *         break;
 *     }
 * } else if ($bc->supportWap20()) {
 *     // wap 2.0 page
 * } else if ($bc->supportWap10()) {
 *     // wap 1.0 page
 * } else {
 *     print "what the hell are you>";
 * }
 * ?>
 * </code>
 * 
 * @package BLH_BrowserChecker
 */

class BLH_BrowserChecker {
    /*
     * @property-read wap 2.0 compatible user agent list
     * @access private
     */

    private static $agents = array(
        "opera", "nokia3230", "nokia6681",
        "nokia6600", "nokia6260", "nokia3230", "nokia3660", "nokia6670",
        "nokia6680", "nokian70", "nokian73", "nokian-gage",
        "nokian-gageqd", "unknown_lenovog900", "lenovo-p902",
        "lenovo-et960", "thomas", "eastcom-es1008a", "tcl-u8",
        "capitalg99", "sch-m309", "sharp-tq-gx2xsample", "soutec-sg2260",
        "cect a800", "cect a100", "e28-mpg1", "samsung-sgh-e348",
        "samsung-sgh-e358", "samsung-sgh-d508", "samsung-sgh-d608",
        "samsung-sgh-e568", "samsung-sgh-e628", "samsung-sgh-e648",
        "samsung-sgh-e728", "samsung-sgh-e738", "samsung-sgh-e758",
        "samsung-sgh-e888", "samsung-sgh-x628", "samsung-sgh-p408",
        "lg-c910", "panasonic-x200p", "panasonic-a500", "sie-sx1",
        "nec-n910", "nec-n8", "lenovoet180", "mot-c350v", "motorola-t33",
        "mot-e680", "mot-e1000", "compal-seville", "mitsu/1.3.a",
        "lge-lg6660", "tcl818", "tclmobo828", "capitel-c2688-unknown",
        "capitalc6828", "sec-sghs308", "sec-sghs300", "sec-sghc208",
        "sec-sghc200", "sec-sghc225", "sec-sghx120", "sec-sghx140",
        "samsung-sgh-x138", "cect-t800", "sharpgx22", "mot-a768",
        "mot-a780", "mot-v303", "mot-v500", "motorola a780", "mot-v870",
        "moto e398", "nec-n8", "nec-n800", "nec-n600", "g900/v1.0",
        "tcl-e767", "tcl-e777", "tcle787", "tcl-d668", "lgg850",
        "nokia7650", "nokia3650", "nokia7610", "panasonic-x700",
        "panasonic-x800", "panasonic-gad87", "panasonic-vs7",
        "panasonic-sa7", "panasonic-gd87", "kejian-k319", "kejian-k399",
        "mot-a860", "lge-lg8380", "lg-w800", "samsung-sgh-e728",
        "sec-sghp730", "sec-sgh738", "sec-sghp710", "sec-sghd428",
        "samsung-sgh-d428/tss", "samsung-sgh-d428/tss", "sec-schx809",
        "acs-nf/3.0 nec-c616", "acs-nf/3.0 nec-e616",
        "acs-nf/3.0 nec-c338", "acs-nf/3.0 nec-c228",
        "acs-nf/3.0 nec-c313", "mot-a835", "lg/u8120", "lg/u8130",
        "nokia6630", "mot-[v|c]975", "lg/u8380", "mot-a835", "lg/u8180",
        "nokia6680", "mozilla", "motorola a1000", "mitsu/2.0 ",
        "mot-e1000", "motorola-v360", "sonyericssonp802",
        "sonyericssonp800", "sonyericssonp910c", "sonyericssons700c",
        "sonyericssons700i", "sonyericssonk700c", "sonyericssonz800",
        "sonyericssonz1010", "sonyericsson-w800c", "sonyericssonm600i",
        "dopod535", "sharp-tqgx-a25", "panasonic-mx6", "capitalx950",
        "lenovo-v920", "sie-s65", "sie-s6c", "cect t868", "cect v80",
        "cect v180", "huawei-u626", "huawei-u636");
    private static $accepts_wap1_mtk = array(
        "image/vnd.wap.wbmp, image/gif, image/bmp, image/jpeg, image/png, audio/amr, audio/imelody, audio/midi, audio/wav, */*",
        "image/vnd.wap.wbmp, image/gif, image/bmp, image/jpeg, image/png, audio/amr, audio/imelody, audio/midi, audio/wav, */*, text/x-vcard, text/x-vcalendar",
        "image/vnd.wap.wbmp,image/gif,image/bmp,image/jpeg,image/png,audio/amr,audio/imelody,audio/midi,audio/wav,*/*",
        "image/vnd.wap.wbmp, image/gif, image/bmp, image/jpeg, audio/amr, audio/imelody, audio/midi, audio/wav, */*",
        "image/vnd.wap.wbmp, image/gif, image/bmp, image/jpeg, audio/amr, audio/imelody, audio/midi, audio/wav, */*, text/x-vcard, text/x-vcalendar",
        "text/html,application/xhtml+xml;;profile=http://www.wapforum.org/xhtml,application/vnd.wap.xhtml+xml,application/vnd.wap.wmlc,application/vnd.wap.wmlscriptc,text/vnd.wap.wml,image/vnd.wap.wbmp,image/gif,image/bmp,audio/imelody,audio/midi,audio/wav,application/vnd.wap.wtls-ca-certificate,application/x-x509-ca-cert,application/vnd.wap.hashed-certificate,application/vnd.wap.signed-certificate,*/*, image/vnd.wap.wbmp,image/gif,image/bmp,image/jpeg,image/png,audio/imelody,audio/midi,audio/wav",
        "image/vnd.wap.wbmp, image/gif, image/bmp, image/jpeg, image/png, audio/imelody, audio/midi, audio/wav, text/x-vcard, text/x-vcalendar",
        "image/vnd.wap.wbmp,image/gif,image/bmp,image/jpeg,audio/amr,audio/imelody,audio/midi,audio/wav,*/*",
        "image/vnd.wap.wbmp, image/gif, image/bmp, image/jpeg, image/png, audio/imelody, audio/midi, audio/wav",
        "application/xhtml+xml;;profile=http://www.wapforum.org/xhtml,application/vnd.wap.xhtml+xml,application/vnd.wap.wmlc,application/vnd.wap.wmlscriptc,text/vnd.wap.wml,image/vnd.wap.wbmp,image/gif,image/bmp,image/png,audio/amr,audio/imelody,audio/midi,audio/wav,application/vnd.wap.wtls-ca-certificate,application/x-x509-ca-cert,application/vnd.wap.hashed-certificate,application/vnd.wap.signed-certificate,*/*,text/html, image/vnd.wap.wbmp,image/gif,image/bmp,image/jpeg,image/png,audio/amr,audio/imelody,audio/midi,audio/wav,*/*"
    );
    private static $accepts_wap20_common = array(
        "application/vnd.wap.xhtml+xml",
        "application/xhtml+xml",
        "text/html"
    );
    private static $iphone_patterns = array(
        //"/\\(iphone;(.*?)\)/i",
        "/\(iphone;.*OS\s+(.*?);\s+.*\)/i",
        "/\\((iphone);.*OS\s+(.*?)\s+like\s+mac.*\)/i",
        "/\\(iphone\ssimulator;/i",
        "/\\(ipod;/i",
    );
    private static $ipad_patterns = array(
        //"/\\(ipad;(.*?)\)/i",
        "/\(ipad;.*OS\s+(.*?);\s+.*\)/i",
        "/\\((ipad);.*OS\s+(.*?)\s+like\s+mac.*\)/i",
        "/\\(ipad\ssimulator;/i",
    );
    private static $android_patterns = array(
        //"/adr\s*(.*?);/i",
        //"/android\s*(.*?);/i",
    	"/adr\s*(.*?);.*; (.*?)\)/i",
        "/android\s*(.*?);.*; (.*?)\)/i",
    	"/android\s*(.*?);/i",
        "/htc/i",
    );
    private static $ucweb_patterns = array(
        "/ucweb\s*(\d+?)\./i",
        "/\suc\s/i",
    );
    private static $accepts_wap1_ucweb = array("ucweb");
    private static $content_type_wap1 = "text/vnd.wap.wml";
    private static $content_type_wap2 = "text/html";
    private $useragent;
    private $accept;
//private $accept_content_type;
    private $supportwap10 = FALSE;
    private $supportwap20 = FALSE;
    private $supporthtml5 = FALSE;
    private $isios = FALSE;
    private $isiphone = FALSE;
    private $isipad = FALSE;
    private $iosver = NULL;
    private $isandroid = FALSE;
    private $androidver = NULL;
    private $mobile_type = NULL;//手机类型
    public $pt = NULL;
	
    /**
     * constructor
     * @access public
     */
    public function __construct() {
        if (isset($_GET["pt"])) {
            $this->pt = $_GET["pt"];
        } else if (array_key_exists("pt", $_COOKIE)) {
            $this->pt = $_COOKIE["pt"];
        }
        
        if (!isset($this->pt)) {
            $this->useragent = isset($_SERVER["HTTP_USER_AGENT"]) ? strtolower($_SERVER["HTTP_USER_AGENT"]) : NULL;
            $this->accept = isset($_SERVER["HTTP_ACCEPT"]) ? strtolower($_SERVER["HTTP_ACCEPT"]) : NULL;
            if (isset($_GET["UA"])) {
                $this->useragent = $_GET["UA"];
            }
            if (strlen($this->useragent) <= 0)
            {
            	$this->useragent = isset($_SERVER["HTTP_X_UCBROWSER_UA"]) ? strtolower($_SERVER["HTTP_X_UCBROWSER_UA"]) : NULL;
            }
            //$this->accept_content_type = isset($_SERVER["HTTP_USER_AGENT"]) ? strtolower($_SERVER["HTTP_USER_AGENT"]) : NULL;
            $this->proceed();
        } else {
            setcookie("pt", $this->pt, time() + 3600 * 24, "/");
            switch ($this->pt) {
                case 3://IOS
                    $this->isios = true;
                    $this->isiphone = true;
                    $this->supporthtml5 = true;
                    break;
				case 4 ://安卓、html5
					$this->isandroid = true;
					$this->supporthtml5 = true;
					break;
				case 2 :
					$this->supportwap20 = true;
					break;
				case 5 :
					$this->isios = true;
					$this->supporthtml5 = true;
					break;
            }
        }
    }

    private function proceed() {
        if (trim($this->useragent) == '') {
            $this->jumpWhiteList();
        }
        if (!$this->checkdevice()) {
            $this->checkwap10();
            $this->checkwap20();
        }
        $this->checkHtml5();
        $this->checkUC();
        $this->checkSymbian();
    }

    private function jumpWhiteList() {
        $url = sprintf("http://3g.sina.com.cn/game/internetsvc/white_res/get_ua.php?back_url=%s", urlencode(Utilities::getRequestFullUrl()));
        header("Location: " . $url);
        exit;
        //$this->useragent = "unknown";
    }

    private function checkSymbian() {
        $i = preg_match("/symbian/i", $this->useragent, $matches);
        if ($i > 0) {
            $this->supporthtml5 = FALSE;
            $this->supportwap20 = TRUE;
            $this->isios = FALSE;
            $this->isipad = FALSE;
            $this->isiphone = FALSE;
            $this->isandroid = FALSE;
            $this->mobile_type = 'symbian';
        }
    }

    private function checkUC() {
        foreach (self::$ucweb_patterns as $pattern) {
            $i = preg_match($pattern, $this->useragent, $matches);
            if ($i > 0) {
                $this->isandroid = FALSE;
                $this->isios = FALSE;
                $x= isset($matches[1]) ? $matches[1] : 0;
                $mainver = intval($x);
                if ($mainver >= 8) {
                    $this->supporthtml5 = FALSE;
                    $this->supportwap20 = TRUE;
                } else {
                    $this->supporthtml5 = FALSE;
                    $this->supportwap20 = TRUE;
                }
                break;
            }
        }
    }

    private function checkwap10() {
        if ($this->accept == NULL) {
// assume wap 1.0 support by unknown devices
            $this->supportwap10 = TRUE;
        } else {
// check if browser of MTK
            foreach (BLH_BrowserChecker::$accepts_wap1_mtk as $key => $value) {
                if (strstr($this->accept, $value)) {
                    $this->supportwap10 = TRUE;
                    break;
                }
            }
// check if ucweb browser
            if (!$this->supportwap10) {
                foreach (BLH_BrowserChecker::$accepts_wap1_ucweb as $key => $value) {
                    if (strstr($this->accept, $value)) {
                        $this->supportwap10 = TRUE;
                        break;
                    }
                }
            }
// check if browser of known wap 1.0 compatible devices 
            if (!$this->supportwap10) {
                foreach (BLH_BrowserChecker::$agents as $key => $value) {
                    if (strstr($this->useragent, $value)) {
                        $this->supportwap10 = TRUE;
                        break;
                    }
                }
            }
        }
    }

    private function checkwap20() {
        foreach (BLH_BrowserChecker::$accepts_wap20_common as $key => $value) {
            if (strstr($this->accept, $value)) {
                $this->supportwap20 = TRUE;
                break;
            }
        }
    }

    private function checkHtml5() {
        if ($this->isios || $this->isandroid) {
            $this->supporthtml5 = TRUE;
        } else {
            // check 3rd party browsers
            $patterns = array(
                "/mqqbrowser/i", // qq browser
                "/firefox/i",
                "/safari/i",
                "/seamonkey/i",
                "/msie/i",
                "/chrome/i",
                "/opera/i"
            );
            foreach ($patterns as $pattern) {
                $i = preg_match($pattern, $this->useragent);
                if ($i > 0) {
                    $this->supporthtml5 = !$this->checkOldSymbian();
                    return;
                }
            }
        }
    }

    private function checkOldSymbian() {
        if ($this->isios || $this->isandroid) {
            return FALSE;
        } else {
            $patterns = array(
                "/symbianos/i",
                "/symbian\s+os/i",
            );
            foreach ($patterns as $pattern) {
                $i = preg_match($pattern, $this->useragent);
                if ($i > 0)
                    return TRUE;
            }
            return FALSE;
        }
    }

    private function checkdevice() {
        $this->checkios();
        if ($this->isios())
            return TRUE;
        $this->checkandroid();
        if ($this->isandroid())
            return TRUE;
        return FALSE;
    }

    private function checkios() {
        $this->checkiphone();
        if (!$this->isiphone)
            $this->checkipad();
    }

    private function checkiphone() {
        foreach (BLH_BrowserChecker::$iphone_patterns as $ke => $pattern) {
            $i = preg_match($pattern, $this->useragent, $match);
            if ($i > 0) {
                $this->isios = TRUE;
                $this->isiphone = TRUE;
                $this->iosver = isset($match[2]) ? trim($match[2]) : NULL;
            	$this->mobile_type = isset($match[1]) ? trim($match[1]) : 'iphone';
                $this->supportwap10 = FALSE;
                $this->supportwap20 = TRUE;
                break;
            }
        }
    }

    private function checkipad() {
        foreach (BLH_BrowserChecker::$ipad_patterns as $ke => $pattern) {
            $i = preg_match($pattern, $this->useragent, $match);
            if ($i > 0) {
                $this->isios = TRUE;
                $this->isipad = TRUE;
                $this->iosver = isset($match[2]) ? trim($match[2]) : NULL;
            	$this->mobile_type = isset($match[1]) ? trim($match[1]) : 'ipad';
                $this->supportwap10 = FALSE;
                $this->supportwap20 = TRUE;
                break;
            }
        }
    }

    private function checkandroid() {
        foreach (BLH_BrowserChecker::$android_patterns as $ke => $pattern) {
            $match = array();
            $i = preg_match($pattern, $this->useragent, $match);
            if ($i > 0) {
                $this->isandroid = TRUE;
                $this->androidver = isset($match[1]) ? trim($match[1]) : NULL;
            	$this->mobile_type = isset($match[2]) ? trim($match[2]) : NULL;
                $this->supportwap10 = FALSE;
                $this->supportwap20 = TRUE;
                break;
            }
        }
    }

    /**
     * check if the client accept wap 1.0 page
     * @return boolean
     */
    public function supportWap1() {
        return $this->supportwap10;
    }

    /**
     * check if the client accept wap 2.0 page.
     * @return boolean
     */
    public function supportWap2() {
        return $this->supportwap20;
    }

    public function supportHtml5() {
        return $this->supporthtml5;
    }

    /**
     * check if it is a safari browser in ios device
     * @return boolean
     */
    public function isIOS() {
        return $this->isios;
    }

    /**
     * check if it is a safari browser in ios device
     * @return boolean
     */
    public function isIPhone() {
        return $this->isiphone;
    }

    /**
     * check if it is a safari browser in ios device
     * @return boolean
     */
    public function isIPad() {
        return $this->isipad;
    }

    /**
     * get ios version
     * @return string
     */
    public function getIOSVersion() {
        return $this->iosver;
    }

    /**
     * check if it's a chrome lite browser on android
     * @return boolean
     */
    public function isAndroid() {
        return $this->isandroid;
    }

    /**
     * get android version of android device
     * @return string
     */
    public function getAndroidVersion() {
        return $this->androidver;
    }
    
    //获取手机类型
    public function getMobileType() {
    	return $this->mobile_type;
    }

}