<?php
require_once 'Twilio-lib/Services/Twilio.php';

function SendCode($to_number, $code, $want_sms) {
	// Twilio REST API version
	$version = "2010-04-01";

	// Set our Account SID and AuthToken
	$sid = '...';
	$token = '...';
	
	// Instantiate a new Twilio Rest Client
	$client = new Services_Twilio($sid, $token, $version);

	try {
		if ($want_sms) {
			$message = "Your verification code is $code.";
			$from_number = "4155992671"; // With trial account, texts can only be sent from this number.
			$client->account->sms_messages->create($from_number, $to_number, $message);
		}
		else {
			$code = preg_replace('/./', '$0,,', $code);
			$message = "Your verification code is $code. Again, the code is $code.";
			$phonenumber = '1112223333';
			// Initiate a new outbound call
			$call = $client->account->calls->create(
				$phonenumber, // The number of the phone initiating the call
				$to_number, // The number of the phone receiving call
				'http://twimlets.com/message?Message=' . urlencode($message)
			);
		}
		return true;
	}
	catch (Exception $e)  {
		$error = $e->getMessage();
		return false;
	}
}
?>