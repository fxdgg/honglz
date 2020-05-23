<?php
//defined('MAILCONFPATH') OR define('MAILCONFPATH', APPPATH . 'config/send_mail.conf');
include_once APPPATH . 'libraries/BLH_PhpMailer.php';
class BLH_SinaMail {

    private $SMTPServer;
    private $UserName;
    private $Password;
    private $From;
    public $CharSet = "GBK";

    function __construct($UserName, $Password, $From, $FromName, $SMTPServer='smtp.126.com') {
        $this->SMTPServer = $SMTPServer;
        $this->UserName = $UserName;
        $this->Password = $Password;
        $this->From = $From;
        $this->FromName = $FromName;
        /*if (($fp = fopen(MAILCONFPATH, "r"))) {
            while (($line = fgets($fp, 1024))) {
                $line = trim(chop($line));
        if ($line[0] != "#") {
                    list($k, $v) = explode("=", $line);
                    $key = trim($k);
                    $value = trim($v);
                    if (!strcmp($key, "SMTPServer")) {
                        $this->SMTPServer = $value;
                    } else if (!strcmp($key, "UserName")) {
                        $this->UserName = $value;
                    } else if (!strcmp($key, "Password")) {
                        $this->Password = $value;
                    } else if (!strcmp($key, "From")) {
                        $this->From = $value;
                    } else if (!strcmp($key, "FromName")) {
                        $this->FromName = $value;
                    }
                }
            }
            fclose($fp);
        }*/
    }

    public function send($subject, $mailList, $body, $attachment, $SMTPDebug = FALSE) {
        $mail = new BLH_PhpMailer();
        $mail->IsSMTP();                       // set mailer to use SMTP
        $mail->SMTPAuth = true;                // turn on SMTP authentication
        $mail->Host = $this->SMTPServer;   // specify main and backup server
        $mail->Username = $this->UserName;     // SMTP username
        $mail->Password = $this->Password;     // SMTP password
        $mail->From = $this->From;
        $mail->FromName = $this->FromName;    // "SNGGROUP";//"KJAVAGROUP";
        $mail->SMTPDebug = $SMTPDebug;    // Debug

        $mail->IsHTML(true);                   // set email format to HTML
        $mail->CharSet = $this->CharSet;            // set mail charset
        $mail->WordWrap = 80;                  // set word wrap to 80 characters

        $mail->Subject = $subject;            // mail subject
        foreach ($mailList as $k => $v) {
            $mail->AddAddress($v);              // name is optional
        }
        if (!empty($body)) {
            $mail->Body = $body;               // set mail body
        }
        if(is_array($attachment)){
            foreach ($attachment as $value){
                $mail->AddAttachment($value);
            }
        }elseif (!empty($attachment)) {
            $mail->AddAttachment($attachment);  // add attachments
        }

        if ($mail->Send()) {
            return true;
        } else {
            return false;
        }
    }

    public function useSysSendMail($to, $subject, $attachment) {
        $uuencode = '/usr/bin/uuencode';
        $mail = '/bin/mail';
        $sendmail = $uuencode . ' ' . $attachment . ' ' . $attachment . '|' . $mail . ' -s "' . $subject . '" -iIn ' . $to;
        $res = `$sendmail`;
        return str_replace("\n", '', $res);
    }

}
?>

