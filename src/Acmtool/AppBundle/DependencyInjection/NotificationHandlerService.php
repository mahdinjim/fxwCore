<?php
namespace Acmtool\AppBundle\DependencyInjection;
use Acmtool\AppBundle\Entity\Log;
use Acmtool\AppBundle\Entity\LogAction;
//Const TIMEZONE="Europe/Berlin";
class NotificationHandlerService
{
	private $doctrine;
	private $emailService;
	private $em;
	private $intercomService;
	private $pushNotifier;
	function __construct($doctrine,$emailService,$intercomService,$pushNotifier) {
		$this->doctrine = $doctrine;
		$this->emailService = $emailService;
		$this->intercomService = $intercomService;
		$this->em=$this->doctrine->getEntityManager();
		$this->pushNotifier = $pushNotifier;
	}
	//update intercom last login info
	public function clientLoggedIn($client_email)
	{
		$attribute=array("lastLoginTime"=>time());
		$this->intercomService->addCustomAttribute($client_email,$attribute);
	}
	//Notify Team member and send him credentials
	public function teamMemberAdded($name,$email,$pwd)
	{
		$this->emailService->notifyAddedTeamMember($email,$pwd,$name);
	}
	//Notify client and send him credentials
	public function clientAdded($client,$pwd,$send)
	{
		if($send)
			$this->emailService->sendClientCreds($client,$pwd);
		$companyname=$client->getCompanyname();
		$country=$client->getAddress()->getCountry();
		$city=$client->getAddress()->getCity();
		$this->intercomService->createNewUser($client,$companyname,$country,$city);
	}
	//delete client from intercom when the client is deleted
	public function clientDeleted($client)
	{
		//$this->intercomService->deleteIntercomUser($client->getEmail());
	}
	public function clientInfoUpdated($client,$oldEmail)
	{
		$this->intercomService->updateUserEmail($client->getEmail(),$oldEmail);
	}
	//send client email
	//send keyaccount email
	public function termsAndConditionAccpeted()
	{

	}
	//send the client user email with his credentials
	//create a user in intercom
	public function clientUserAdded($client,$pwd,$send)
	{
		if($send)
			$this->emailService->sendClientCreds($client,$pwd);
		$companyname=$client->getCompanyname();
		$country=$client->getAddress()->getCountry();
		$city=$client->getAddress()->getCity();
		$this->intercomService->createNewUser($client,$companyname,$country,$city);
	}
	//send email to keyaccount
	//add to log project created
	//send email to admin to add team members
	public function projectCreated($creator,$project)
	{
		$log = new Log();
		$log->setText("Project ".$project->getName()." created");
		$log->setItem("project");
		$log->setAction(LogAction::PROJECTCREATED);
		$log->setCreationdate(new \DateTime("NOW",new \DateTimeZone(TIMEZONE)));
		$log->setUser($creator->getCredentials());
		$log->setProject($project);
		$this->em->persist($log);
		$this->em->flush();
		$keyaccount=$project->getOwner()->getKeyAccount();
		$client=$project->getOwner();
		$creator_name=$creator->getName();
		$admins=$this->getAdmins();
		foreach ($admins as $key) {
			$this->emailService->notifyProjectCreated($key->getEmail(),$key->getName(),$client,$project,$creator_name,false);
		}
		if($client->getReferencedBy() != null)
		{
			if($client->getReferencedBy()->getId() == $keyaccount->getCredentials()->getId())
				$isPartner = true;
			else
				$isPartner = false;
		}
		else
		{
			$isPartner = false;
		}
		
		$this->emailService->notifyProjectCreated($keyaccount->getEmail(),$keyaccount->getName(),$client,$project,$creator_name,$isPartner);
		$attribute=array("lastProjectCreated"=>time());
		$this->intercomService->addCustomAttribute($client->getEmail(),$attribute);
	}
	//send email to client
	public function projectContractPrepared()
	{

	}
	//send email to client with contract pdf
	//send email to keyaccount 
	//send email to admin
	public function projectContractAccepted()
	{

	}
	//send email to member telling him that he is assigned to project
	public function assignedToProject($project,$teamMember,$isleader=false)
	{
		$email=$teamMember->getEmail();
		$name=$teamMember->getName();
		$client_name=$project->getOwner()->getCompanyname();
		$this->emailService->notifyAssignedToProject($email,$project,$name,$client_name,$isleader);

	}
	//send email to teamleader informing him that he is assigned as teamleader
	public function teamLeaderAssigned($project,$teamMember)
	{
		$this->assignedToProject($project,$teamMember,true);
	}
	//log ticket created
	//send email to keyaccount
	//send email to teamleader
	//send email to client asking him to start ticket
	public function ticketCreated($ticket,$creator)
	{
		$project=$ticket->getProject();
		$client=$project->getOwner();
		$log = new Log();
		$log->setAction(LogAction::TICKETCREATED);
		$log->setText($ticket->getTitle()." added");
		$log->setItem("ticket");
		$log->setCreationdate(new \DateTime("NOW",new \DateTimeZone(TIMEZONE)));
		$log->setUser($creator->getCredentials());
		$log->setProject($project);
		$log->setTicket($ticket);
		$this->em->persist($log);
		$this->em->flush();
		$this->emailService->notifyClientDraftTicket($client,$ticket);
		$keyaccount=$client->getKeyAccount();
		$this->emailService->notifyTicketCreated($keyaccount->getEmail(),$ticket,$client->getCompanyname(),$project,$keyaccount->getName());
		if($project->getTeamleader())
		{
			$teamleader=$this->em->getRepository("AcmtoolAppBundle:Creds")->getUserByCreds($project->getTeamleader());
			$this->emailService->notifyTicketCreated($teamleader->getEmail(),$ticket,$client->getCompanyname(),$project,$teamleader->getName());
		}
		$attribute=array("lastTicketCreated"=>time());
		$this->intercomService->addCustomAttribute($client->getEmail(),$attribute);

	}
	//send email to teamleader asking him to add story
	//log ticket started
	public function ticketStarted($ticket,$creator)
	{
		$project=$ticket->getProject();
		$client=$project->getOwner();
		$log = new Log();
		$log->setAction(LogAction::TICKETSTARTED);
		$log->setText($ticket->getTitle()." started");
		$log->setItem("ticket");
		$log->setCreationdate(new \DateTime("NOW",new \DateTimeZone(TIMEZONE)));
		$log->setUser($creator->getCredentials());
		$log->setProject($project);
		$log->setTicket($ticket);
		$this->em->persist($log);
		$this->em->flush();
		$keyaccount=$client->getKeyAccount();
		$this->emailService->notifyTicketStarted($keyaccount->getEmail(),$ticket,$client->getCompanyname(),$project,$keyaccount->getName());
		if($project->getTeamleader())
		{
			$teamleader=$this->em->getRepository("AcmtoolAppBundle:Creds")->getUserByCreds($project->getTeamleader());
			$this->emailService->notifyTicketStarted($teamleader->getEmail(),$ticket,$client->getCompanyname(),$project,$teamleader->getName());
		}
	}
	//log ticket estimated
	//send email to client to accept estimation
	//send push notification
	public function ticketEstimated($ticket,$creator)
	{
		$project=$ticket->getProject();
		$client=$project->getOwner();
		$log = new Log();
		$log->setAction(LogAction::TICKETESTIMATED);
		$log->setText($ticket->getTitle()." estimated");
		$log->setItem("ticket");
		$log->setCreationdate(new \DateTime("NOW",new \DateTimeZone(TIMEZONE)));
		$log->setUser($creator->getCredentials());
		$log->setProject($project);
		$log->setTicket($ticket);
		$this->em->persist($log);
		$this->em->flush();
		$this->emailService->notifyClientEstimatedTicket($client,$ticket);
		$this->pushNotifier->sendNotification($client,$ticket->getTitle()." estimated (".$ticket->getEstimation()."h)");
	}
	//send email to client explaining that ticket in draft now
	//log ticket estimation rejected
	public function ticketEstimationRejected($ticket,$creator)
	{
		$project=$ticket->getProject();
		$client=$project->getOwner();
		$log = new Log();
		$log->setAction(LogAction::TICKETREJSTIMATION);
		$log->setText($ticket->getTitle()." re-edited");
		$log->setItem("ticket");
		$log->setCreationdate(new \DateTime("NOW",new \DateTimeZone(TIMEZONE)));
		$log->setUser($creator->getCredentials());
		$log->setProject($project);
		$log->setTicket($ticket);
		$this->em->persist($log);
		$this->em->flush();
		$this->emailService->notifyClientRejectEstimationTicket($client,$ticket);
	}
	//log estimation accepted
	//send email to teamleader to start production
	public function ticketEstimationAcepted($ticket,$creator)
	{
		$project=$ticket->getProject();
		$client=$project->getOwner();
		$log = new Log();
		$log->setAction(LogAction::TICKETACESTIMATION);
		$log->setText($ticket->getTitle()." estimation accepted");
		$log->setItem("ticket");
		$log->setCreationdate(new \DateTime("NOW",new \DateTimeZone(TIMEZONE)));
		$log->setUser($creator->getCredentials());
		$log->setProject($project);
		$log->setTicket($ticket);
		$this->em->persist($log);
		$this->em->flush();
		if($project->getTeamleader())
		{
			$teamleader=$this->em->getRepository("AcmtoolAppBundle:Creds")->getUserByCreds($project->getTeamleader());
			$this->emailService->notifyTicketEstimationAccepted($teamleader->getEmail(),$ticket,$client->getCompanyname(),$project,$teamleader->getName());
		}
	}
	//log that the ticket is in production
	//send an email to inform client that the ticket in production
	//send email to team members
	public function ticketInProduction($ticket,$creator)
	{
		$project=$ticket->getProject();
		$client=$project->getOwner();
		$log = new Log();
		$log->setAction(LogAction::TICKETINPROD);
		$log->setText($ticket->getTitle()." sent to production");
		$log->setItem("ticket");
		$log->setCreationdate(new \DateTime("NOW",new \DateTimeZone(TIMEZONE)));
		$log->setUser($creator->getCredentials());
		$log->setProject($project);
		$log->setTicket($ticket);
		$this->em->persist($log);
		$this->em->flush();
		$this->emailService->notifyClientTicketInProduction($client,$ticket);
		$members=array();
		$members=array_merge($members,$project->getDevelopers()->toArray(),$project->getTesters()->toArray(),$project->getDesigners()->toArray(),$project->getSysadmins()->toArray());
		foreach ($members as $key) {
			$this->emailService->notifyTicketinProduction($key->getEmail(),$ticket,$client->getCompanyname(),$project,$key->getName());
		}
		$this->pushNotifier->sendNotification($client,$ticket->getTitle()." in production");
	}
	//log that the ticket is in QA
	//inform teamleader that ticket in QA
	//inform the client that the ticket in QA
	public function ticketinQA($ticket,$creator)
	{
		$project=$ticket->getProject();
		$client=$project->getOwner();
		$log = new Log();
		$log->setAction(LogAction::TICKETINQA);
		$log->setText($ticket->getTitle()." in qa");
		$log->setItem("ticket");
		$log->setCreationdate(new \DateTime("NOW",new \DateTimeZone(TIMEZONE)));
		$log->setUser($creator->getCredentials());
		$log->setProject($project);
		$log->setTicket($ticket);
		$this->em->persist($log);
		$this->em->flush();
		$this->emailService->notifyClientTicketInQA($client,$ticket);
		if($project->getTeamleader())
		{
			$teamleader=$this->em->getRepository("AcmtoolAppBundle:Creds")->getUserByCreds($project->getTeamleader());
			$this->emailService->notifyTicketinQA($teamleader->getEmail(),$ticket,$client->getCompanyname(),$project,$teamleader->getName());
		}
		$this->pushNotifier->sendNotification($client,$ticket->getTitle()." in QA");
	}
	//log that ticket delivred
	//inform client about ticket delivred
	public function ticketDelivred($ticket,$creator)
	{
		$project=$ticket->getProject();
		$client=$project->getOwner();
		$log = new Log();
		$log->setAction(LogAction::TICKETDELIVRED);
		$log->setText($ticket->getTitle()." delivred");
		$log->setItem("ticket");
		$log->setCreationdate(new \DateTime("NOW",new \DateTimeZone(TIMEZONE)));
		$log->setUser($creator->getCredentials());
		$log->setProject($project);
		$log->setTicket($ticket);
		$this->em->persist($log);
		$this->em->flush();
		$this->emailService->notifyClientTicketDelivred($client,$ticket);
		$this->pushNotifier->sendNotification($client,$ticket->getTitle()." delivred, please accept it");
	}
	//log ticket accepted
	//inform teamleader ticket accepted
	//inform all developers ticket accepted
	public function ticketAccepted($ticket,$creator)
	{
		$project=$ticket->getProject();
		$client=$project->getOwner();
		$log = new Log();
		$log->setAction(LogAction::TICKETACCEPTED);
		$log->setText($ticket->getTitle()." accepted");
		$log->setItem("ticket");
		$log->setCreationdate(new \DateTime("NOW",new \DateTimeZone(TIMEZONE)));
		if($creator!=null)
			$log->setUser($creator->getCredentials());
		$log->setProject($project);
		$log->setTicket($ticket);
		$this->em->persist($log);
		$this->em->flush();
		$this->emailService->notifyClientTicketDone($client,$ticket);
		if($project->getTeamleader())
		{
			$teamleader=$this->em->getRepository("AcmtoolAppBundle:Creds")->getUserByCreds($project->getTeamleader());
			$this->emailService->notifyTicketAccepted($teamleader->getEmail(),$ticket,$client->getCompanyname(),$project,$teamleader->getName());
		}
		$members=array();
		$members=array_merge($members,$project->getDevelopers()->toArray(),$project->getTesters()->toArray(),$project->getDesigners()->toArray(),$project->getSysadmins()->toArray());
		foreach ($members as $key) {
			$this->emailService->notifyTicketAccepted($key->getEmail(),$ticket,$client->getCompanyname(),$project,$key->getName());
		}

	}
	//notify client that ticket will close soon
	public function ticketWillclose($ticket)
	{
		$project=$ticket->getProject();
		$client=$project->getOwner();
		$this->emailService->notifyClientReminder($client,$ticket);
		$this->pushNotifier->sendNotification($client,"remainder: ".$ticket->getTitle()." will be closed in one day");
	}
	//log story created
	public function storyCreated($ticket,$creator,$story)
	{
		$project=$ticket->getProject();
		$client=$project->getOwner();
		$log = new Log();
		$log->setAction(LogAction::STORYCREATED);
		$log->setText($story->getTitle()." created");
		$log->setItem("story");
		$log->setCreationdate(new \DateTime("NOW",new \DateTimeZone(TIMEZONE)));
		$log->setUser($creator->getCredentials());
		$log->setProject($project);
		$log->setTicket($ticket);
		$this->em->persist($log);
		$this->em->flush();
	}
	//log story estimated
	//see if all ticket is estimated and send email to teamleader
	public function storyEstimated($ticket,$creator,$story)
	{
		$project=$ticket->getProject();
		$client=$project->getOwner();
		$log = new Log();
		$log->setAction(LogAction::STORYESTIMATED);
		$log->setText($story->getTitle()." estimated");
		$log->setItem("story");
		$log->setCreationdate(new \DateTime("NOW",new \DateTimeZone(TIMEZONE)));
		$log->setUser($creator->getCredentials());
		$log->setProject($project);
		$log->setTicket($ticket);
		$this->em->persist($log);
		$this->em->flush();
		$estimation=0;
		$allestimated=true;
		foreach ($ticket->getTasks() as $key) {

			if($key->getId() != $story->getId() && $key->getEstimation()==null)
				$allestimated=false;
			if($key->getEstimation()!=null)
				$estimation+=$key->getEstimation();
		}
		if($allestimated)
		{
			if($project->getTeamleader())
			{
				$teamleader=$this->em->getRepository("AcmtoolAppBundle:Creds")->getUserByCreds($project->getTeamleader());
				$this->emailService->notifyTicketEstimated($teamleader->getEmail(),$ticket,$client->getCompanyname(),$project,$teamleader->getName(),$estimation);
			}
		}
	}
	//log that the story is started by developer
	public function storystarted($ticket,$creator,$story)
	{
		$project=$ticket->getProject();
		$client=$project->getOwner();
		$log = new Log();
		$log->setAction(LogAction::STORYSTARTED);
		$log->setText($story->getTitle()." started");
		$log->setItem("story");
		$log->setCreationdate(new \DateTime("NOW",new \DateTimeZone(TIMEZONE)));
		$log->setUser($creator->getCredentials());
		$log->setProject($project);
		$log->setTicket($ticket);
		$this->em->persist($log);
		$this->em->flush();
	}
	//log story finished
	//send email to team member if he didn't set the realtime
	public function storyFinished($ticket,$creator,$story)
	{
		$project=$ticket->getProject();
		$client=$project->getOwner();
		$log = new Log();
		$log->setAction(LogAction::STORYFINISHED);
		$log->setText($story->getTitle()." finished");
		$log->setItem("story");
		$log->setCreationdate(new \DateTime("NOW",new \DateTimeZone(TIMEZONE)));
		$log->setUser($creator->getCredentials());
		$log->setProject($project);
		$log->setTicket($ticket);
		$this->em->persist($log);
		$this->em->flush();
		if($story->getRealtime() == null)
		{
			$assignedto=null;
			if($story->getDeveloper()!=null)
				$assignedto=$story->getDeveloper();
			elseif($story->getDesigner()!=null)
				$assignedto=$story->getDesigner();
			elseif($story->getTester()!=null)
				$assignedto=$story->getTester();
			elseif($story->getSysadmin()!=null)
				$assignedto=$story->getSysadmin();
			if( $assignedto!=null)
			{
				$this->emailService->notifyStoryDone($assignedto->getEmail(),$ticket,$client->getCompanyname(),$project,$assignedto->getName(),$story->getTitle());
			}
		}
	}
	//log user has set the realtime
	public function storyRealtimeSet($ticket,$creator,$story)
	{
		$project=$ticket->getProject();
		$client=$project->getOwner();
		$log = new Log();
		$log->setAction(LogAction::STORYREALTIME);
		$log->setText($story->getTitle()." realtime set");
		$log->setItem("story");
		$log->setCreationdate(new \DateTime("NOW",new \DateTimeZone(TIMEZONE)));
		$log->setUser($creator->getCredentials());
		$log->setProject($project);
		$log->setTicket($ticket);
		$this->em->persist($log);
		$this->em->flush();
	}
	public function bugCreated($ticket,$creator,$bug,$isClient)
	{
		$project=$ticket->getProject();
		$client=$project->getOwner();
		$log = new Log();
		$log->setAction(LogAction::BUGCREATED);
		$log->setText($bug->getTitle()." created");
		$log->setItem("bug");
		$log->setCreationdate(new \DateTime("NOW",new \DateTimeZone(TIMEZONE)));
		$log->setUser($creator->getCredentials());
		$log->setProject($project);
		$log->setTicket($ticket);
		$this->em->persist($log);
		if($isClient)
		{
			if($project->getTeamleader())
			{
				$teamleader=$this->em->getRepository("AcmtoolAppBundle:Creds")->getUserByCreds($project->getTeamleader());
				$this->emailService->notifyBugCreated($teamleader->getEmail(),$ticket,$client->getCompanyname(),$project,$teamleader->getName(),$bug->getTitle());
			}
		}
	}
	//log bug accepted
	public function bugAccepted($ticket,$creator,$bug)
	{
		$project=$ticket->getProject();
		$client=$project->getOwner();
		$log = new Log();
		$log->setAction(LogAction::BUGACCEPTED);
		$log->setText($bug->getTitle()." accepted");
		$log->setItem("bug");
		$log->setCreationdate(new \DateTime("NOW",new \DateTimeZone(TIMEZONE)));
		$log->setUser($creator->getCredentials());
		$log->setProject($project);
		$log->setTicket($ticket);
		$this->em->persist($log);
	}
	public function bugRejected($ticket,$creator,$bug,$reason)
	{
		$project=$ticket->getProject();
		$client=$project->getOwner();
		$log = new Log();
		$log->setAction(LogAction::BUGREJECTED);
		$log->setText($bug->getTitle()." moved to ticket");
		$log->setItem("bug");
		$log->setCreationdate(new \DateTime("NOW",new \DateTimeZone(TIMEZONE)));
		$log->setUser($creator->getCredentials());
		$log->setProject($project);
		$log->setTicket($ticket);
		$this->em->persist($log);
		$this->emailService->notifyClientBugRejected($client,$bug,$reason);
	}
	public function bugDelivred($ticket,$creator)
	{
		$project=$ticket->getProject();
		$client=$project->getOwner();
		$log = new Log();
		$log->setAction(LogAction::BUGDELIVRED);
		$log->setText($ticket->getTitle()." bug delivred");
		$log->setItem("ticket");
		$log->setCreationdate(new \DateTime("NOW",new \DateTimeZone(TIMEZONE)));
		$log->setUser($creator->getCredentials());
		$log->setProject($project);
		$log->setTicket($ticket);
		$this->em->persist($log);
		$project=$ticket->getProject();
		$client=$project->getOwner();
		$this->emailService->notifyClientBugsDone($client,$ticket);
		$this->pushNotifier->sendNotification($client,"all bugs of ticket ".$ticket->getTitle()." are solved and delivred");
	}
	public function assignedToStory($ticket,$creator,$teamMember,$story)
	{
		$project=$ticket->getProject();
		$client=$project->getOwner();
		$log = new Log();
		$log->setAction(LogAction::STORYASSIGNED);
		$log->setText($teamMember->getName()." assigned to ".$story->getTitle());
		$log->setItem("story");
		$log->setCreationdate(new \DateTime("NOW",new \DateTimeZone(TIMEZONE)));
		$log->setUser($creator->getCredentials());
		$log->setProject($project);
		$log->setTicket($ticket);
		$this->em->persist($log);
		$this->emailService->notifyAssignedToStory($teamMember->getEmail(),$ticket,$client->getCompanyname(),$project,$teamMember->getName(),$story->getTitle());
	}
	//send email to client when new invoice created
	public function invoiceCreated($invoice)
	{

	}
	private function getAdmins() 
	{
		return $this->em->getRepository('AcmtoolAppBundle:Admin')->findAll();
	}
}