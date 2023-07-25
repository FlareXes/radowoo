<?php

defined('BASEPATH') OR exit('No direct script access allowed');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class SendMail
{

    public $mail;

    public function __construct()
    {
		$this->mail = new PHPMailer; 
		$this->mail->isSMTP(); 
        
        $this->mail->Host = get_admin_setting('smtp_host');
        $this->mail->Port = get_admin_setting('smtp_port'); 
        $this->mail->SMTPSecure = trim(get_admin_setting('smtp_crypto')); 
        // $this->mail->Host = "tls://smtp.gmail.com";
        // $this->mail->Port = 587; 
        // $this->mail->SMTPSecure = "tls"; 
        $this->mail->Username = trim(get_admin_setting('smtp_user'));
        $this->mail->Password = trim(get_admin_setting('smtp_pass'));
        $this->mail->SMTPAuth = true; 
        $this->mail->Debugoutput = 'html'; 
        $this->mail->CharSet = 'UTF-8';       
        
        $this->mail->Mailer = "SMTP";

    }

    public function sendTo($mail_to, $subject, $recipet_name, $message) 
    {
       $this->mail->setFrom(get_admin_setting('site_email'),get_admin_setting('site_name'));

        $this->mail->addAddress($mail_to, $recipet_name);
        $this->mail->isHTML(true); 
        $this->mail->Subject = $subject;
        $this->mail->Body = $message;
        //$this->mail->SMTPDebug  = 3; 

        if (!$this->mail->send()) 
        {
           
            log_message('error', 'Mailer Error: ' . $this->mail->ErrorInfo);
            //p($this->mail->ErrorInfo);
            return false;
        }
        return true;
    }

    public function sendTo_with_attachment($mail_to, $subject, $recipet_name, $message,$attachment_array = array()) 
    {
        $this->mail->clearAttachments();
        if($attachment_array)
        {
            foreach ($attachment_array as $attachment_arr) 
            {
                $this->mail->addAttachment($attachment_arr['path'], $attachment_arr['name']);
            }
        }
       $this->mail->setFrom(get_admin_setting('site_email'),get_admin_setting('site_name'));

        $this->mail->addAddress($mail_to, $recipet_name);
        $this->mail->isHTML(true); 
        $this->mail->Subject = $subject;
        $this->mail->Body = $message;
        //$this->mail->SMTPDebug  = 3; 
        if (!$this->mail->send()) 
        {
           
            log_message('error', 'Mailer Error: ' . $this->mail->ErrorInfo);
            //p($this->mail->ErrorInfo);
            return false;
        }
        return true;
    }

}
