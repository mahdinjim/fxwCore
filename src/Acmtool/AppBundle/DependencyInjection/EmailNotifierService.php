<?php
namespace Acmtool\AppBundle\DependencyInjection;
use Acmtool\AppBundle\Entity\EmailToken;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Acmtool\AppBundle\Entity\ClientLinks;
Const PERIOD=30;
Const TIMEZONE="Europe/Berlin";
class EmailNotifierService
{
	private $mailer;
	private $twig;
	private $doctrine;
	private $router;
	private $crfProvider;
	function __construct($mailer,$twig,$doctrine,$router,$crfProvider) {
		$this->mailer = $mailer;
		$this->twig=$twig;
		$this->doctrine=$doctrine;
		$this->router=$router;
		$this->crfProvider=$crfProvider;
	}
	public function notifyAddedTeamMember($email,$password,$name)
	{
		$today=new \DateTime("NOW",new \DateTimeZone(TIMEZONE));
		$date=$today->format("d.m.Y");
		$subject="Welcome to flexwork";
		$body=$this->twig->render(
					'EmailTemplates/team/access.html.twig',
					array('login'=>$email,'password'=>$password,"date"=>$date,"name"=>$name)
				);
		$this->sendEmail($email,$subject,$body);
	}
	public function notifyProjectCreated($email,$name,$client,$project,$creator,$isPartner=false){
		$today=new \DateTime("NOW",new \DateTimeZone(TIMEZONE));
		$subject="New project ".$project->getName()." created";
		$date=$today->format("d.m.Y");
		$link=ClientLinks::getClientProjectLink().$client->getId();
		$body=$this->twig->render(
					'EmailTemplates/team/newproject.html.twig',
					array('client_name'=>$client->getCompanyname(),
						'project_name'=>$project->getName(),
						"date"=>$date,
						"name"=>$name,
						"creator"=>$creator,
						"link"=>$link,
						"client_tel"=>$client->getPhonecode().$client->getTelnumber(),
						"isPartner"=>$isPartner)
				);
		$this->sendEmail($email,$subject,$body);
	}
	public function notifyTicketCreated($email,$ticket,$client_name,$project,$name){
		$today=new \DateTime("NOW",new \DateTimeZone(TIMEZONE));
		$subject="New ticket ".$ticket->getTitle()." created";
		$date=$today->format("d.m.Y");
		$link=ClientLinks::getTicketDetailLink($project->getDisplayId(),$ticket->getDiplayId());
		$body=$this->twig->render(
					'EmailTemplates/team/draft.html.twig',
					array('project_name'=>$project->getName(),
						"date"=>$date,
						"name"=>$name,
						"link"=>$link,
						"client_name"=>$client_name,
						"ticket_name"=>$ticket->getTitle())
				);
		$this->sendEmail($email,$subject,$body);
		
	}
	public function notifyTicketStarted($email,$ticket,$client_name,$project,$name){
		$subject="Ticket ".$ticket->getTitle()." started";
		$today=new \DateTime("NOW",new \DateTimeZone(TIMEZONE));
		$date=$today->format("d.m.Y");
		$link=ClientLinks::getTicketDetailLink($project->getDisplayId(),$ticket->getDiplayId());
		$body=$this->twig->render(
					'EmailTemplates/team/started.html.twig',
					array('project_name'=>$project->getName(),
						"date"=>$date,
						"name"=>$name,
						"link"=>$link,
						"client_name"=>$client_name,
						"ticket_name"=>$ticket->getTitle())
				);
		$this->sendEmail($email,$subject,$body);
		
	}
	public function notifyTicketEstimated($email,$ticket,$client_name,$project,$name,$estimation){
		$subject="Ticket ".$ticket->getTitle()." estimated";
		$today=new \DateTime("NOW",new \DateTimeZone(TIMEZONE));
		$date=$today->format("d.m.Y");
		$link=ClientLinks::getTicketDetailLink($project->getDisplayId(),$ticket->getDiplayId());
		$body=$this->twig->render(
					'EmailTemplates/team/estimated.html.twig',
					array('project_name'=>$project->getName(),
						"date"=>$date,
						"name"=>$name,
						"link"=>$link,
						"client_name"=>$client_name,
						"ticket_name"=>$ticket->getTitle(),
						"estimation"=>$estimation)
				);
		$this->sendEmail($email,$subject,$body);
		
	}
	public function notifyTicketEstimationAccepted($email,$ticket,$client_name,$project,$name)
	{
		$subject="Ticket Estimation ".$ticket->getTitle()." accepted";
		$today=new \DateTime("NOW",new \DateTimeZone(TIMEZONE));
		$date=$today->format("d.m.Y");
		$link=ClientLinks::getTicketDetailLink($project->getDisplayId(),$ticket->getDiplayId());
		$body=$this->twig->render(
					'EmailTemplates/team/est_accapted.html.twig',
					array('project_name'=>$project->getName(),
						"date"=>$date,
						"name"=>$name,
						"link"=>$link,
						"client_name"=>$client_name,
						"ticket_name"=>$ticket->getTitle())
				);
		$this->sendEmail($email,$subject,$body);
	}
	public function notifyTicketinProduction($email,$ticket,$client_name,$project,$name)
	{
		$subject="Ticket ".$ticket->getTitle()." is in production";
		$today=new \DateTime("NOW",new \DateTimeZone(TIMEZONE));
		$date=$today->format("d.m.Y");
		$link=ClientLinks::getTicketDetailLink($project->getDisplayId(),$ticket->getDiplayId());
		$body=$this->twig->render(
					'EmailTemplates/team/production.html.twig',
					array('project_name'=>$project->getName(),
						"date"=>$date,
						"name"=>$name,
						"link"=>$link,
						"client_name"=>$client_name,
						"ticket_name"=>$ticket->getTitle())
				);
		$this->sendEmail($email,$subject,$body);
	}
	public function notifyTicketinQA($email,$ticket,$client_name,$project,$name)
	{
		$subject="Ticket ".$ticket->getTitle()." is in QA";
		$today=new \DateTime("NOW",new \DateTimeZone(TIMEZONE));
		$date=$today->format("d.m.Y");
		$link=ClientLinks::getTicketDetailLink($project->getDisplayId(),$ticket->getDiplayId());
		$body=$this->twig->render(
					'EmailTemplates/team/qa.html.twig',
					array('project_name'=>$project->getName(),
						"date"=>$date,
						"name"=>$name,
						"link"=>$link,
						"client_name"=>$client_name,
						"ticket_name"=>$ticket->getTitle())
				);
		$this->sendEmail($email,$subject,$body);
	}
	public function notifyTicketAccepted($email,$ticket,$client_name,$project,$name)
	{
		$subject="Ticket ".$ticket->getTitle()." Accepted";
		$today=new \DateTime("NOW",new \DateTimeZone(TIMEZONE));
		$date=$today->format("d.m.Y");
		$link=ClientLinks::getTicketDetailLink($project->getDisplayId(),$ticket->getDiplayId());
		$body=$this->twig->render(
					'EmailTemplates/team/done.html.twig',
					array('project_name'=>$project->getName(),
						"date"=>$date,
						"name"=>$name,
						"link"=>$link,
						"client_name"=>$client_name,
						"ticket_name"=>$ticket->getTitle())
				);
		$this->sendEmail($email,$subject,$body);
	}
	public function notifyAssignedToProject($email,$project,$name,$client_name,$isleader){
		$subject="You have been assigned to ".$project->getName();
		$today=new \DateTime("NOW",new \DateTimeZone(TIMEZONE));
		$date=$today->format("d.m.Y");
		$link=ClientLinks::getProjectDetailLink().$project->getDisplayId();
		$body=$this->twig->render(
					'EmailTemplates/team/assign_project.html.twig',
					array('client_name'=>$client_name,
						'project_name'=>$project->getName(),
						"date"=>$date,
						"name"=>$name,
						"link"=>$link,
						"isleader"=>$isleader)
				);
		$this->sendEmail($email,$subject,$body);
	}
	public function notifyAssignedToStory($email,$ticket,$client_name,$project,$name,$storyname){
		$subject="You have been assigned to ".$storyname;
		$today=new \DateTime("NOW",new \DateTimeZone(TIMEZONE));
		$date=$today->format("d.m.Y");
		$link=ClientLinks::getTicketDetailLink($project->getDisplayId(),$ticket->getDiplayId());
		$body=$this->twig->render(
					'EmailTemplates/team/assignedstory.html.twig',
					array('project_name'=>$project->getName(),
						"date"=>$date,
						"name"=>$name,
						"link"=>$link,
						"client_name"=>$client_name,
						"ticket_name"=>$ticket->getTitle(),
						"story_name"=>$storyname)
				);
		$this->sendEmail($email,$subject,$body);
	}
	public function notifyStoryDone($email,$ticket,$client_name,$project,$name,$storyname){
		$subject="The story ".$storyname." finished";
		$today=new \DateTime("NOW",new \DateTimeZone(TIMEZONE));
		$date=$today->format("d.m.Y");
		$link=ClientLinks::getTicketDetailLink($project->getDisplayId(),$ticket->getDiplayId());
		$body=$this->twig->render(
					'EmailTemplates/team/storyfinished.html.twig',
					array('project_name'=>$project->getName(),
						"date"=>$date,
						"name"=>$name,
						"link"=>$link,
						"client_name"=>$client_name,
						"ticket_name"=>$ticket->getTitle(),
						"story_name"=>$storyname)
				);
		$this->sendEmail($email,$subject,$body);
	}
	public function notifyBugCreated($email,$ticket,$client_name,$project,$name,$storyname)
	{
		$subject="The bug ".$storyname." created";
		$today=new \DateTime("NOW",new \DateTimeZone(TIMEZONE));
		$date=$today->format("d.m.Y");
		$link=ClientLinks::getTicketDetailLink($project->getDisplayId(),$ticket->getDiplayId());
		$body=$this->twig->render(
					'EmailTemplates/team/bugcreated.html.twig',
					array('project_name'=>$project->getName(),
						"date"=>$date,
						"name"=>$name,
						"link"=>$link,
						"client_name"=>$client_name,
						"ticket_name"=>$ticket->getTitle(),
						"story_name"=>$storyname)
				);
		$this->sendEmail($email,$subject,$body);
	}
	private function sendEmail($email,$subject,$body)
	{
		$message =\Swift_Message::newInstance()
		->setSubject($subject)
		->setFrom(array("flexy@flexwork.io"=>"flexwork"))
		->setTo($email)
		->setBody($body,'text/html');
		
		$isent=$this->mailer->send($message);
	}
	public function notifyClientDraftTicket($client,$ticket)
	{
		//step 1 create variables 
		//step 2 create the action link
		//step 3 pass tha variable to the twig 
		//step 4 send email
		$client_email=$client->getEmail();
		$client_name=$client->getName();
		$ticket_id=$ticket->getDiplayId();
		$token=$this->createEmailToken($client->getCredentials());
		$link=$this->router->generate("_startticket",array('ticket_id' =>$ticket_id ,'token'=>$token->getTokendig()), UrlGeneratorInterface::ABSOLUTE_URL);
		$subject="Start ticket >> ".$ticket->getTitle()." #".$ticket->getDiplayId();
		$today=new \DateTime("NOW",new \DateTimeZone(TIMEZONE));
		$date=$today->format("d.m.Y");
		$message =\Swift_Message::newInstance()
		->setSubject($subject)
		->setFrom(array("flexy@flexwork.io"=>"flexwork"))
		->setTo($client_email)
		->setBody(
			$this->twig->render(
					'EmailTemplates/client/draft.html.twig',
					array('ticket'=>$ticket,'client'=>$client,"date"=>$date,'link'=>$link)
				),
				'text/html'
			);
		
		if($this->checkSendNotif($client->getCredentials(),$ticket->getProject()))
					$isent=$this->mailer->send($message);
		foreach ($client->getUsers() as $user) {
			$client->setName($user->getName());
			$message =\Swift_Message::newInstance()
				->setSubject($subject)
				->setFrom(array("flexy@flexwork.io"=>"flexwork"))
				->setTo($user->getEmail())
				->setBody(
					$this->twig->render(
					'EmailTemplates/client/draft.html.twig',
					array('ticket'=>$ticket,'client'=>$client,"date"=>$date,'link'=>$link)
					),
					'text/html'
				);
				if($this->checkSendNotif($client->getCredentials(),$ticket->getProject()))
					$isent=$this->mailer->send($message);
			}

	}
	public function notifyClientEstimatedTicket($client,$ticket)
	{
		//step 1 create variables 
		//step 2 create the action link
		//step 3 pass tha variable to the twig 
		//step 4 send email
		$client_email=$client->getEmail();
		$client_name=$client->getName();
		$ticket_id=$ticket->getDiplayId();
		$token=$this->createEmailToken($client->getCredentials());
		$today=new \DateTime("NOW",new \DateTimeZone(TIMEZONE));
		$date=$today->format("d.m.Y");
		$link=$this->router->generate("_acceptticketestimation",array('ticket_id' =>$ticket_id ,'token'=>$token->getTokendig()), UrlGeneratorInterface::ABSOLUTE_URL);
		$subject="Confirm ticket >> ".$ticket->getTitle()." #".$ticket->getDiplayId();
		$message =\Swift_Message::newInstance()
		->setSubject($subject)
		->setFrom(array("flexy@flexwork.io"=>"flexwork"))
		->setTo($client_email)
		->setBody(
			$this->twig->render(
					'EmailTemplates/client/estimation.html.twig',
					array('ticket'=>$ticket,'client'=>$client,"link"=>$link,"date"=>$date)
				),
				'text/html'
			);
		
		if($this->checkSendNotif($client->getCredentials(),$ticket->getProject()))
					$isent=$this->mailer->send($message);
		foreach ($client->getUsers() as $user) {
			$client->setName($user->getName());
			$message =\Swift_Message::newInstance()
				->setSubject($subject)
				->setFrom(array("flexy@flexwork.io"=>"flexwork"))
				->setTo($user->getEmail())
				->setBody(
					$this->twig->render(
					'EmailTemplates/client/estimation.html.twig',
					array('ticket'=>$ticket,'client'=>$client,"link"=>$link,"date"=>$date)
				),
				'text/html'
			);
				if($this->checkSendNotif($client->getCredentials(),$ticket->getProject()))
					$isent=$this->mailer->send($message);
			}

	}
	public function notifyClientTicketInProduction($client,$ticket)
	{
		//step 1 create variables 
		//step 2 create the action link
		//step 3 pass tha variable to the twig 
		//step 4 send email
		$client_email=$client->getEmail();
		$client_name=$client->getName();
		$ticket_id=$ticket->getDiplayId();
		$today=new \DateTime("NOW",new \DateTimeZone(TIMEZONE));
		$date=$today->format("d.m.Y");
		$subject="Ticket in production >> ".$ticket->getTitle()." #".$ticket->getDiplayId();
		$message =\Swift_Message::newInstance()
		->setSubject($subject)
		->setFrom(array("flexy@flexwork.io"=>"flexwork"))
		->setTo($client_email)
		->setBody(
			$this->twig->render(
					'EmailTemplates/client/production.html.twig',
					array('ticket'=>$ticket,'client'=>$client,"date"=>$date)
				),
				'text/html'
			);
		
		if($this->checkSendNotif($client->getCredentials(),$ticket->getProject()))
					$isent=$this->mailer->send($message);
		foreach ($client->getUsers() as $user) {
			$client->setName($user->getName());
			$message =\Swift_Message::newInstance()
				->setSubject($subject)
				->setFrom(array("flexy@flexwork.io"=>"flexwork"))
				->setTo($user->getEmail())
				->setBody(
					$this->twig->render(
					'EmailTemplates/client/production.html.twig',
					array('ticket'=>$ticket,'client'=>$client,"date"=>$date)
					),
					'text/html'
				);
				if($this->checkSendNotif($client->getCredentials(),$ticket->getProject()))
					$isent=$this->mailer->send($message);
			}

	}
	public function notifyClientTicketInQA($client,$ticket)
	{
		//step 1 create variables 
		//step 2 create the action link
		//step 3 pass tha variable to the twig 
		//step 4 send email
		$client_email=$client->getEmail();
		$client_name=$client->getName();
		$ticket_id=$ticket->getDiplayId();
		$today=new \DateTime("NOW",new \DateTimeZone(TIMEZONE));
		$date=$today->format("d.m.Y");
		$subject="Ticket in QA >> ".$ticket->getTitle()." #".$ticket->getDiplayId();
		$message =\Swift_Message::newInstance()
		->setSubject($subject)
		->setFrom(array("flexy@flexwork.io"=>"flexwork"))
		->setTo($client_email)
		->setBody(
			$this->twig->render(
					'EmailTemplates/client/qa.html.twig',
					array('ticket'=>$ticket,'client'=>$client,"date"=>$date)
				),
				'text/html'
			);
		
		if($this->checkSendNotif($client->getCredentials(),$ticket->getProject()))
					$isent=$this->mailer->send($message);
		foreach ($client->getUsers() as $user) {
			$client->setName($user->getName());
			$message =\Swift_Message::newInstance()
				->setSubject($subject)
				->setFrom(array("flexy@flexwork.io"=>"flexwork"))
				->setTo($user->getEmail())
				->setBody(
					$this->twig->render(
					'EmailTemplates/client/qa.html.twig',
					array('ticket'=>$ticket,'client'=>$client,"date"=>$date)
				),
				'text/html'
				);
				if($this->checkSendNotif($client->getCredentials(),$ticket->getProject()))
					$isent=$this->mailer->send($message);
			}

	}
	public function notifyClientTicketDelivred($client,$ticket)
	{
		//step 1 create variables 
		//step 2 create the action link
		//step 3 pass tha variable to the twig 
		//step 4 send email
		$client_email=$client->getEmail();
		$client_name=$client->getName();
		$ticket_id=$ticket->getDiplayId();
		$token=$this->createEmailToken($client->getCredentials());
		$today=new \DateTime("NOW",new \DateTimeZone(TIMEZONE));
		$date=$today->format("d.m.Y");
		$link=$this->router->generate("_acceptticketemail",array('ticket_id' =>$ticket_id ,'token'=>$token->getTokendig()), UrlGeneratorInterface::ABSOLUTE_URL);
		$subject="Confirm ticket >> ".$ticket->getTitle()." #".$ticket->getDiplayId();
		$message =\Swift_Message::newInstance()
		->setSubject($subject)
		->setFrom(array("flexy@flexwork.io"=>"flexwork"))
		->setTo($client_email)
		->setBody(
			$this->twig->render(
					'EmailTemplates/client/delivered.html.twig',
					array('ticket'=>$ticket,'client'=>$client,"link"=>$link,"date"=>$date)
				),
				'text/html'
			);
		
		if($this->checkSendNotif($client->getCredentials(),$ticket->getProject()))
					$isent=$this->mailer->send($message);
		foreach ($client->getUsers() as $user) {
			$client->setName($user->getName());
			$message =\Swift_Message::newInstance()
				->setSubject($subject)
				->setFrom(array("flexy@flexwork.io"=>"flexwork"))
				->setTo($user->getEmail())
				->setBody(
					$this->twig->render(
							'EmailTemplates/client/delivered.html.twig',
							array('ticket'=>$ticket,'client'=>$client,"link"=>$link,"date"=>$date)
						),
						'text/html'
					);
				if($this->checkSendNotif($client->getCredentials(),$ticket->getProject()))
					$isent=$this->mailer->send($message);
			}

	}
	public function notifyClientTicketDone($client,$ticket)
	{
		//step 1 create variables 
		//step 2 create the action link
		//step 3 pass tha variable to the twig 
		//step 4 send email
		$client_email=$client->getEmail();
		$client_name=$client->getName();
		$ticket_id=$ticket->getDiplayId();
		$today=new \DateTime("NOW",new \DateTimeZone(TIMEZONE));
		$date=$today->format("d.m.Y");
		$subject="Ticket is closed >> ".$ticket->getTitle()." #".$ticket->getDiplayId();
		$message =\Swift_Message::newInstance()
		->setSubject($subject)
		->setFrom(array("flexy@flexwork.io"=>"flexwork"))
		->setTo($client_email)
		->setBody(
			$this->twig->render(
					'EmailTemplates/client/done.html.twig',
					array('ticket'=>$ticket,'client'=>$client,"date"=>$date)
				),
				'text/html'
			);
		
		if($this->checkSendNotif($client->getCredentials(),$ticket->getProject()))
					$isent=$this->mailer->send($message);
		foreach ($client->getUsers() as $user) {
			$client->setName($user->getName());
			$message =\Swift_Message::newInstance()
				->setSubject($subject)
				->setFrom(array("flexy@flexwork.io"=>"flexwork"))
				->setTo($user->getEmail())
				->setBody(
					$this->twig->render(
					'EmailTemplates/client/done.html.twig',
					array('ticket'=>$ticket,'client'=>$client,"date"=>$date)
				),
				'text/html'
			);
				if($this->checkSendNotif($client->getCredentials(),$ticket->getProject()))
					$isent=$this->mailer->send($message);
			}

	}
	public function notifyClientBugsDone($client,$ticket)
	{
		$client_email=$client->getEmail();
		$client_name=$client->getName();
		$ticket_id=$ticket->getDiplayId();
		$token=$this->createEmailToken($client->getCredentials());
		$today=new \DateTime("NOW",new \DateTimeZone(TIMEZONE));
		$date=$today->format("d.m.Y");
		$link=$this->router->generate("_acceptticketemail",array('ticket_id' =>$ticket_id ,'token'=>$token->getTokendig()), UrlGeneratorInterface::ABSOLUTE_URL);
		$subject="Ticket bugs are solved >> ".$ticket->getTitle()." #".$ticket->getDiplayId();
		$message =\Swift_Message::newInstance()
		->setSubject($subject)
		->setFrom(array("flexy@flexwork.io"=>"flexwork"))
		->setTo($client_email)
		->setBody(
			$this->twig->render(
					'EmailTemplates/client/bugfinish.html.twig',
					array('ticket'=>$ticket,'client'=>$client,"link"=>$link,"date"=>$date)
				),
				'text/html'
			);
		
		if($this->checkSendNotif($client->getCredentials(),$ticket->getProject()))
					$isent=$this->mailer->send($message);
		foreach ($client->getUsers() as $user) {
			$client->setName($user->getName());
			$message =\Swift_Message::newInstance()
				->setSubject($subject)
				->setFrom(array("flexy@flexwork.io"=>"flexwork"))
				->setTo($user->getEmail())
				->setBody(
					$this->twig->render(
							'EmailTemplates/client/bugfinish.html.twig',
							array('ticket'=>$ticket,'client'=>$client,"link"=>$link,"date"=>$date)
						),
						'text/html'
					);
				if($this->checkSendNotif($client->getCredentials(),$ticket->getProject()))
					$isent=$this->mailer->send($message);
			}

	}
	public function notifyClientReminder($client,$ticket)
	{
		$client_email=$client->getEmail();
		$client_name=$client->getName();
		$ticket_id=$ticket->getDiplayId();
		$token=$this->createEmailToken($client->getCredentials());
		$today=new \DateTime("NOW",new \DateTimeZone(TIMEZONE));
		$date=$today->format("d.m.Y");
		$link=$this->router->generate("_acceptticketemail",array('ticket_id' =>$ticket_id ,'token'=>$token->getTokendig()), UrlGeneratorInterface::ABSOLUTE_URL);
		$subject="Reminder: 1 day left for acceptance >> ".$ticket->getTitle()." #".$ticket->getDiplayId();
		$message =\Swift_Message::newInstance()
		->setSubject($subject)
		->setFrom(array("flexy@flexwork.io"=>"flexwork"))
		->setTo($client_email)
		->setBody(
			$this->twig->render(
					'EmailTemplates/client/reminder.html.twig',
					array('ticket'=>$ticket,'client'=>$client,"link"=>$link,"date"=>$date)
				),
				'text/html'
			);
		
		if($this->checkSendNotif($client->getCredentials(),$ticket->getProject()))
					$isent=$this->mailer->send($message);
		foreach ($client->getUsers() as $user) {
			$client->setName($user->getName());
			$message =\Swift_Message::newInstance()
				->setSubject($subject)
				->setFrom(array("flexy@flexwork.io"=>"flexwork"))
				->setTo($user->getEmail())
				->setBody(
					$this->twig->render(
							'EmailTemplates/client/bugfinish.html.twig',
							array('ticket'=>$ticket,'client'=>$client,"link"=>$link,"date"=>$date)
						),
						'text/html'
					);
				if($this->checkSendNotif($client->getCredentials(),$ticket->getProject()))
					$isent=$this->mailer->send($message);
			}

	}
	public function notifyClientRejectEstimationTicket($client,$ticket)
	{
		$client_email=$client->getEmail();
		$client_name=$client->getName();
		$ticket_id=$ticket->getDiplayId();
		$token=$this->createEmailToken($client->getCredentials());
		$today=new \DateTime("NOW",new \DateTimeZone(TIMEZONE));
		$date=$today->format("d.m.Y");
		$link=$this->router->generate("_startticket",array('ticket_id' =>$ticket_id ,'token'=>$token->getTokendig()), UrlGeneratorInterface::ABSOLUTE_URL);
		$subject="Redit ticket >> ".$ticket->getTitle()." #".$ticket->getDiplayId();
		$message =\Swift_Message::newInstance()
		->setSubject($subject)
		->setFrom(array("flexy@flexwork.io"=>"flexwork"))
		->setTo($client_email)
		->setBody(
			$this->twig->render(
					'EmailTemplates/client/rejectestimation.html.twig',
					array('ticket'=>$ticket,'client'=>$client,"link"=>$link,"date"=>$date)
				),
				'text/html'
			);
		
		if($this->checkSendNotif($client->getCredentials(),$ticket->getProject()))
					$isent=$this->mailer->send($message);
		foreach ($client->getUsers() as $user) {
			$client->setName($user->getName());
			$message =\Swift_Message::newInstance()
				->setSubject($subject)
				->setFrom(array("flexy@flexwork.io"=>"flexwork"))
				->setTo($user->getEmail())
				->setBody(
					$this->twig->render(
					'EmailTemplates/client/rejectestimation.html.twig',
					array('ticket'=>$ticket,'client'=>$client,"link"=>$link,"date"=>$date)
				),
				'text/html'
			);
				if($this->checkSendNotif($client->getCredentials(),$ticket->getProject()))	
					$isent=$this->mailer->send($message);
			}

	}
	public function notifyClientBugRejected($client,$ticket,$reason)
	{
		$client_email=$client->getEmail();
		$client_name=$client->getName();
		$ticket_id=$ticket->getDiplayId();
		$token=$this->createEmailToken($client->getCredentials());
		$today=new \DateTime("NOW",new \DateTimeZone(TIMEZONE));
		$date=$today->format("d.m.Y");
		$link=$this->router->generate("_startticket",array('ticket_id' =>$ticket_id ,'token'=>$token->getTokendig()), UrlGeneratorInterface::ABSOLUTE_URL);
		$subject="Bug converted to ticket >> ".$ticket->getTitle()." #".$ticket->getDiplayId();
		$message =\Swift_Message::newInstance()
		->setSubject($subject)
		->setFrom(array("flexy@flexwork.io"=>"flexwork"))
		->setTo($client_email)
		->setBody(
			$this->twig->render(
					'EmailTemplates/client/bugrejected.html.twig',
					array('ticket'=>$ticket,'client'=>$client,"link"=>$link,"date"=>$date,"reason"=>$reason)
				),
				'text/html'
			);
		if($this->checkSendNotif($client->getCredentials(),$ticket->getProject()))
			$isent=$this->mailer->send($message);
		foreach ($client->getUsers() as $user) {
			$client->setName($user->getName());
			$message =\Swift_Message::newInstance()
				->setSubject($subject)
				->setFrom(array("flexy@flexwork.io"=>"flexwork"))
				->setTo($user->getEmail())
				->setBody(
					$this->twig->render(
					'EmailTemplates/client/bugrejected.html.twig',
					array('ticket'=>$ticket,'client'=>$client,"link"=>$link,"date"=>$date,"reason"=>$reason)
				),
				'text/html'
				);
				if($this->checkSendNotif($client->getCredentials(),$ticket->getProject()))
					$isent=$this->mailer->send($message);
			}

	}
	public function sendClientCreds($client,$password)
	{
		$client_email=$client->getEmail();
		$keyaccount=$client->getKeyaccount();
		$today=new \DateTime("NOW",new \DateTimeZone(TIMEZONE));
		$date=$today->format("d.m.Y");
		$subject="Welcome to flexwork";
		$message =\Swift_Message::newInstance()
		->setSubject($subject)
		->setFrom(array("flexy@flexwork.io"=>"flexwork"))
		->setTo(array($client_email))
		->setBcc($keyaccount->getEmail())
		->setBody(
			$this->twig->render(
					'EmailTemplates/client/access.twig.html',
					array('client'=>$client,"password"=>$password,"date"=>$date)
				),
				'text/html'
			);
		$isent=$this->mailer->send($message);
		
	}
	private function createEmailToken($user)
	{
		date_default_timezone_set(TIMEZONE);
		$today =new \DateTime("NOW",new \DateTimeZone(TIMEZONE));
        $expireDate=$today->add(new \DateInterval('P'.PERIOD.'D'));
		$token= new EmailToken();
		$token->setUser($user);
		$token->setExpirationdate($expireDate);
		$key=$this->createHashCode($today->format('Y-m-d H:i:s'));
		$token->setTokendig($key);
		$em=$this->doctrine->getEntityManager();
		$em->persist($token);
		$em->flush();
		return $token;
	}
	private function createHashCode($identifier)
	{
		$random=$identifier.$this->random_string(14);
		$csrfToken = $this->crfProvider->generateCsrfToken($random);
        return $csrfToken;
	}
	private function random_string($length) {
	    $key = '';
	    $keys = array_merge(range(0, 9), range('a', 'z'));

	    for ($i = 0; $i < $length; $i++) {
	        $key .= $keys[array_rand($keys)];
	    }

	    return $key;
	}
	private function checkSendNotif($user,$project)
	{
		$em=$this->doctrine->getEntityManager();
		$noNotifExist=$em->getRepository("AcmtoolAppBundle:NoNotif")->findOneBy(array("user"=>$user,"project"=>$project));
		if($noNotifExist)
			return false;
		else
			return true;
	}


}