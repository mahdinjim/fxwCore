<?php
namespace Acmtool\AppBundle\DependencyInjection;
use Acmtool\AppBundle\Entity\DeviceToken;
use Acmtool\AppBundle\Entity\AndroidPush;
Const FIREBASE_API_KEY="AAAAHKEjyvA:APA91bHGwPqs_dvyBTZ8rPWdJmd8BBgyS12j6HqzHpaXNwh4thfNGFmwar95M409WK3PMH8yXKCjwzAslgL6bnnn2dRgrqNSk96CxnBI5RR74oED_vDxmm_TY3yc1JAQbBbe_vwkH4-U";
class MobilePushNotificationService
{
	private $em;
	function __construct($doctrine) {
		$this->em = $doctrine->getEntityManager();
	}
	public function sendNotification($client,$message)
	{
		$clientUsers = $client->getUsers();
		$androidTokens = array();
		$iosTokens = array();
		$DevieTokens = $this->em->getRepository("AcmtoolAppBundle:DeviceToken")->findByUser($client->getCredentials());
		foreach ($DevieTokens as $key) {
			if($key->getDeviceid()!=null)
			{
				if($key->getOs() == "ios")
				{
					array_push($iosTokens, $key->getDeviceid());
				}
				else
				{
					array_push($androidTokens, $key->getDeviceid());
				}
			}
		}
		foreach ($clientUsers as $key) {
			 $DevieTokens = $this->em->getRepository("AcmtoolAppBundle:DeviceToken")->findByUser($key->getCredentials());
			 foreach ($DevieTokens as $key) {
				if($key->getDeviceid()!=null)
				{
					if($key->getOs() == "ios")
					{
						array_push($iosTokens, $key->getDeviceid());
					}
					else
					{
						array_push($androidTokens, $key->getDeviceid());
					}
				}
			}
		}
		if(count($iosTokens) > 0)
			$this->sendIOSNotif($iosTokens,$message);
		if(count($androidTokens) > 0)
			$this->sendAndroidNotif($androidTokens,$message);

	}
	private function sendIOSNotif($tokens,$message)
	{
		$body['aps'] = array(
			'alert' => $message,
			'sound' => 'default',
		);
		$passphrase = '';
		// Encode the payload as JSON
		$payload = json_encode($body);
		//open connection 
		$ctx = stream_context_create();
		stream_context_set_option($ctx, 'ssl', 'local_cert', __DIR__.'/../../../../web/certificates/pushnotifcert.pem');
		//stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
		$fp = stream_socket_client(
		  'ssl://gateway.push.apple.com:2195', $err,
		  $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

		if (!$fp)
		{
			echo "no connection";die();
		}
		foreach ($tokens as $key) {
			$msg = chr(0) . pack('n', 32) . pack('H*', $key) . pack('n', strlen($payload)) . $payload;
			fwrite($fp, $msg, strlen($msg));
		}
		// Close the connection to the server
		fclose($fp);
	}
	private function sendAndroidNotif($tokens,$message)
	{
		$push = new AndroidPush();
		$push->setMessage($message);
		$push->setIsBackground(false);
		
        $url = 'https://fcm.googleapis.com/fcm/send';
 
        $headers = array(
            'Authorization: key=' . FIREBASE_API_KEY,
            'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();
        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
 
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
 		foreach ($tokens as $key) {
 			$fields = array(
	            'to' => $key,
	            'data' => array('message' =>$message ,"background"=>false,"title"=>"flexy" ),
        	);
        	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
 			 $result = curl_exec($ch);
	        if ($result === FALSE) {
	            die('Curl failed: ' . curl_error($ch));
        	}
 		}
        curl_close($ch);
	}

}