<?php
/*
This file is part of iCE Hrm.

iCE Hrm is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

iCE Hrm is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with iCE Hrm. If not, see <http://www.gnu.org/licenses/>.

------------------------------------------------------------------

Original work Copyright (c) 2012 [Gamonoid Media Pvt. Ltd]  
Developer: Thilina Hasantha (thilina.hasantha[at]gmail.com / facebook.com/thilinah)
 */

use Aws\Ses\SesClient;

abstract class EmailSender{
	var $settings = null;
	public function __construct($settings){
		$this->settings	= $settings;	
	}
	
	public function sendEmail($subject, $toEmail, $template, $params){
		
		$body = $template;	

		foreach($params as $k=>$v){
			$body = str_replace("#_".$k."_#", $v, $body);	
		}
		$fromEmail = "ICE Hrm <".$this->settings->getSetting("Email: Email From").">";
		
		
		//Convert to an html email
		$emailBody = file_get_contents(APP_BASE_PATH.'/templates/email/emailBody.html');
		
		$emailBody = str_replace("#_emailBody_#", $body, $emailBody);
		
		$user = new User();
		$user->load("username = ?",array('admin'));
		
		if(empty($user->id)){
			$users = $user->Find("user_level = ?",array('Admin'));
			$user = $users[0];
		}
		
		$emailBody = str_replace("#_adminEmail_#", $user->email, $emailBody);
		$emailBody = str_replace("#_url_#", CLIENT_BASE_URL, $emailBody);
		
		$this->sendMail($subject, $emailBody, $toEmail, $fromEmail, $user->email);
	}	
	
	protected  abstract function sendMail($subject, $body, $toEmail, $fromEmail, $replyToEmail = null);
	
	public function sendResetPasswordEmail($emailOrUserId){
		$user = new User();
		$user->Load("email = ?",array($emailOrUserId));	
		if(empty($user->id)){
			$user = new User();
			$user->Load("username = ?",array($emailOrUserId));
			if(empty($user->id)){
				return false;
			}
		}
		
		$params = array();
		//$params['user'] = $user->first_name." ".$user->last_name;
		$params['url'] = CLIENT_BASE_URL;
		
		$newPassHash = array();
		$newPassHash["CLIENT_NAME"] = CLIENT_NAME;
		$newPassHash["oldpass"] = $user->password;
		$newPassHash["email"] = $user->email;
		$newPassHash["time"] = time();
		$json = json_encode($newPassHash);
		
		$encJson = AesCtr::encrypt($json, $user->password, 256);
		$encJson = urlencode($user->id."-".$encJson);
		$params['passurl'] = CLIENT_BASE_URL."service.php?a=rsp&key=".$encJson;
		
		$emailBody = file_get_contents(APP_BASE_PATH.'/templates/email/passwordReset.html');
		
		$this->sendEmail("[ICE Hrm] Password Change Request", $user->email, $emailBody, $params);
		return true;
	}
	
}


class SNSEmailSender extends EmailSender{
	var $ses = null;
	public function __construct($settings){
		parent::__construct($settings);
		$arr = array(
				'key'    => $this->settings->getSetting('Email: Amazon SNS Key'),
				'secret' => $this->settings->getSetting('Email: Amazone SNS Secret'),
				'region' => AWS_REGION
		);
		//$this->ses = new AmazonSES($arr);
		$this->ses = SesClient::factory($arr);
	}
	
	protected  function sendMail($subject, $body, $toEmail, $fromEmail, $replyToEmail = null) {
		
		if(empty($replyToEmail)){
			$replyToEmail = $fromEmail;
		}
		
		error_log("Sending email to: ".$toEmail."/ from: ".$fromEmail);

        $toArray = array('ToAddresses' => array($toEmail),
        				'CcAddresses' => array(),
        				'BccAddresses' => array());
        $message = array( 
	        'Subject' => array(
	            'Data' => $subject,
	            'Charset' => 'UTF-8'
	        ),
	        'Body' => array(
	            'Html' => array(
	                'Data' => $body,
	                'Charset' => 'UTF-8'
	            )
	        )
    	);
    	
    	//$response = $this->ses->sendEmail($fromEmail, $toArray, $message);
    	$response = $this->ses->sendEmail(
    		array(
    			'Source'=>$fromEmail, 
    			'Destination'=>$toArray, 
    			'Message'=>$message,
    			'ReplyToAddresses' => array($replyToEmail),
    			'ReturnPath' => $fromEmail
    		)
    	);
    	
    	error_log("SES Response:".print_r($response,true));
    	
    	return $response;
    	
    }
}


class SMTPEmailSender extends EmailSender{
	
	public function __construct($settings){
		parent::__construct($settings);
	}
	
	protected  function sendMail($subject, $body, $toEmail, $fromEmail, $replyToEmail = null) {
		
		if(empty($replyToEmail)){
			$replyToEmail = $fromEmail;
		}

		error_log("Sending email to: ".$toEmail."/ from: ".$fromEmail);
		
		$host = $this->settings->getSetting("Email: SMTP Host");
		$username = $this->settings->getSetting("Email: SMTP User");
		$password = $this->settings->getSetting("Email: SMTP Password");
		$port = $this->settings->getSetting("Email: SMTP Port");
		
		if(empty($port)){
			$port = '25';
		}
		
		if($this->settings->getSetting("Email: SMTP Authentication Required") == "0"){
			$auth = array ('host' => $host,
     		'auth' => false);	
		}else{
			$auth = array ('host' => $host,
     		'auth' => true,
     		'username' => $username,
			'port' => $port,		
     		'password' => $password);	
		}
		
		
		$smtp = Mail::factory('smtp',$auth);

		$headers = array ('MIME-Version' => '1.0',
  		'Content-type' => 'text/html',
  		'charset' => 'iso-8859-1',
  		'From' => $fromEmail,
  		'To' => $toEmail,
  		'Reply-To' => $replyToEmail,
   		'Subject' => $subject);
		
		
		$mail = $smtp->send($toEmail, $headers, $body);
		
		
		return true;
    }
}


class PHPMailer extends EmailSender{

	public function __construct($settings){
		parent::__construct($settings);
	}

	protected  function sendMail($subject, $body, $toEmail, $fromEmail, $replyToEmail = null) {
		
		if(empty($replyToEmail)){
			$replyToEmail = $fromEmail;
		}

		error_log("Sending email to: ".$toEmail."/ from: ".$fromEmail);

		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'From: '.$fromEmail. "\r\n";
		$headers .= 'ReplyTo: '.$replyToEmail. "\r\n";
		$headers .= 'IceHrm-Mailer: PHP/' . phpversion();

		// Mail it
		$res = mail($toEmail, $subject, $body, $headers);

		error_log("PHP mailer result : ".$res);

		return true;
	}
}