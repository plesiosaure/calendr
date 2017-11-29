<?php

class sendMail extends coreApp{

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function send($opt){

		require_once(APP.'/plugin/phpmailer/class.phpmailer.php');
		$mail = new PHPMailer();
		$mail->CharSet = 'UTF-8';

		$domain = $opt['domain'] ?: $_SERVER['HTTP_HOST'];

		$mail->SetFrom('ne-pas-repondre@'.$domain);
		$mail->ClearReplyTos();
		$mail->AddReplyTo('contact@'.$domain);

		// Destinataire
		$mail->AddAddress($opt['to']);

		// Copie
		if(is_array($opt['cc']) && sizeof($opt['cc']) > 0){
			foreach($opt['cc'] as $e){
				$mail->AddCC($e);
			}
		}

		// Copie cachee
		#$mail->AddBCC('bm@kappuccino.org');
		if(is_array($opt['bcc']) && sizeof($opt['bcc']) > 0){
			foreach($opt['bcc'] as $e){
				$mail->AddBCC($e);
			}
		}

		// Title
		$mail->Subject = $opt['title'];

		// Data
		$template = USER.'/mail/'.$opt['template'];
		if(is_array($opt['body']) && file_exists($template) && is_file($template)){
			$body = $this->helperReplace(file_get_contents($template), $opt['body']);
		}else{
			$body = $opt['body'];
		}

		$mail->AltBody = strip_tags($body);
		$mail->MsgHTML(preg_replace("/\\\\/", '', $body));

		if($opt['return'] === true) return $body;

		return $mail->Send();
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// $$message = https://mandrillapp.com/api/docs/messages.html
// ARRAY direct de mandrill
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function mandrill($opt){

		$config = array();
		$sent   = false;

		require_once APP.'/plugin/mailchimp-mandrill/src/Mandrill.php';
		require USER.'/config/config.php';

		try {
			$mandrill         = new Mandrill($config['mandrill']['key']);
			$template_content = array();
			$template_name    = $opt['template'];

			$message = $opt['message'];
			$result  = $mandrill->messages->sendTemplate($template_name, $template_content, $message, false, '', '');

			if($result[0]['status'] == 'sent'){
				$sent = true;
			}else{
				$sent = $result[0]['status'];
			}

		} catch(Mandrill_Error $e) {
			// Mandrill errors are thrown as exceptions
			#	echo 'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage();
			// A mandrill error occurred: Mandrill_Unknown_Subaccount - No subaccount exists with the id 'customer-123'
			#	throw $e;

			$sent = array('mandrill' => get_class($e), 'error' => $e->getMessage());
		}

		return $sent;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// $$message = https://mandrillapp.com/api/docs/messages.html
// ARRAY direct de mandrill
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function mandrillMail($opt){

		$config = array();
		$sent   = false;

		require_once APP.'/plugin/mailchimp-mandrill/src/Mandrill.php';
		require USER.'/config/config.php';

		try {
			$mandrill = new Mandrill($config['mandrill']['key']);
			$message  = $opt['message'];
			$result   = $mandrill->messages->send($message, false, '', '');

			if($result[0]['status'] == 'sent'){
				$sent = true;
			}else{
				$sent = $result[0]['status'];
			}

		} catch(Mandrill_Error $e) {
			// Mandrill errors are thrown as exceptions
			#	echo 'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage();
			// A mandrill error occurred: Mandrill_Unknown_Subaccount - No subaccount exists with the id 'customer-123'
			#	throw $e;

			$sent = array('mandrill' => get_class($e), 'error' => $e->getMessage());
		}

		return $sent;
	}

}
