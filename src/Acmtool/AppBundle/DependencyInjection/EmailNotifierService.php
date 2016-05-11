<?php

namespace Acmtool\AppBundle\DependencyInjection;

class EmailNotifierService
{
	private $mailer;
	function __construct($mailer) {
		$this->mailer = $mailer;
	}
	public function notifyAddedTeamMember($email,$password,$login,$name,$surname)
	{
		$message =\Swift_Message::newInstance()
		->setSubject("Welcome to Slack")
		->setFrom("noreply@flexwork.io")
		->setTo($email)
		->setBody('<h1>Welcome to flexwork</h1>
			<br/>
			Dear '.$name.' '.$surname.'<br/>
			welcome to flexwork your credential information are:</br>

			<b>Login:</b> '.$login.'<br/>
			<b>Password:</b>'.$password.'<br/>

			');
		
		$isent=$this->mailer->send($message);
		
	}


}