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
		$subject="Welcome to flexwork";
		$body="Welcome to flexwork\n\nDear ".$name." ".$surname."\nWelcome to flexwork your credential information are:\n"
			."Login: ".$login
			."\nPassword: ".$password;
		$this->sendEmail($email,$subject,$body);
	}
	public function notifyProjectCreated($emails,$projectname,$client_name){
		$subject="New project ".$projectname." created";
		$body="Hi, good news,\n\nNew project ".$projectname." has been created by ".$client_name.".";
		foreach ($emails as $email) {
			$this->sendEmail($email,$subject,$body);
		}
		
	}
	public function notifyTicketCreated($emails,$projectname,$ticket){
		$subject="New ticket ".$ticket." created";
		$body="Hi,\n\nNew ticket ".$ticket." has been created in the project ".$projectname.".";
		foreach ($emails as $email) {
			$this->sendEmail($email,$subject,$body);
		}
		
	}
	public function notifyTicketStarted($emails,$projectname,$ticket){
		$subject="Ticket ".$ticket." started";
		$body="Hi,\n\nTicket ".$ticket." has been started in the project ".$projectname.".";
		foreach ($emails as $email) {
			$this->sendEmail($email,$subject,$body);
		}
		
	}
	public function notifyTicketEstimated($emails,$projectname,$ticket,$company_name){
		$subject="Ticket ".$ticket." estimated";
		$body="Dear ".$company_name."\n\nTicket ".$ticket." has been estimated in the project ".$projectname."."
			."\n Please confirm the etimation";
		foreach ($emails as $email) {
			$this->sendEmail($email,$subject,$body);
		}
		
	}
	public function notifyTicketEstimationAccepted($email,$projectname,$ticket)
	{
		$subject="Ticket Estimation ".$ticket." accepted";
		$body="Hi,\n\nTicket estimation ".$ticket." has been accepted in the project ".$projectname."."
			."\n Please send it to production";
		$this->sendEmail($email,$subject,$body);
	}
	public function notifyTicketinProduction($emails,$projectname,$ticket,$company_name)
	{
		$subject="Ticket ".$ticket." is in production";
		$body="Dear ".$company_name."\n\nTicket ".$ticket." moved to production in the project ".$projectname.".";
		foreach ($emails as $email) {
			$this->sendEmail($email,$subject,$body);
		}
	}
	public function notifyTicketinQA($emails,$projectname,$ticket,$company_name)
	{
		$subject="Ticket ".$ticket." is in Q&A";
		$body="Dear ".$company_name."\n\nTicket ".$ticket." moved to Q&A in the project ".$projectname.".";
		foreach ($emails as $email) {
			$this->sendEmail($email,$subject,$body);
		}
	}
	public function notifyTicketDelivered($emails,$projectname,$ticket,$company_name)
	{
		$subject="Ticket ".$ticket." Delivered";
		$body="Dear ".$company_name."\n\nTicket ".$ticket." delivered in the project ".$projectname.".";
		foreach ($emails as $email) {
			$this->sendEmail($email,$subject,$body);
		}
	}
	public function notifyTicketAccepted($emails,$projectname,$ticket)
	{
		$subject="Ticket ".$ticket." Accepted";
		$body="Hi, good job\n\nTicket ".$ticket." accepted by the client in the project ".$projectname.".";
		foreach ($emails as $email) {
			$this->sendEmail($email,$subject,$body);
		}
	}
	public function notifyTicketRejected($emails,$projectname,$ticket)
	{
		$subject="Ticket ".$ticket." Accepted";
		$body="Hi, good job\n\nTicket ".$ticket." accepted by the client in the project ".$projectname.".";
		foreach ($emails as $email) {
			$this->sendEmail($email,$subject,$body);
		}
	}
	public function notifyAssignedToProject($email,$projectname,$name,$surname){
		$subject="You have been assigned to ".$projectname;
		$body="Dear ".$name." ".$surname."\n\nYou have been assigned to new project ".$projectname.".";
		$this->sendEmail($email,$subject,$body);
	}
	public function notifyTeamLeader($email,$projectname,$name,$surname){
		$subject="You have been assigned to ".$projectname." As Team leader";
		$body="Dear ".$name." ".$surname."\n\nYou have been assigned as team leader to new project ".$projectname.".";
		$this->sendEmail($email,$subject,$body);
	}
	public function notifyAssignedToStory($email,$projectname,$name,$surname,$storyname){
		$subject="You have been assigned to ".$storyname;
		$body="Dear ".$name." ".$surname."\n\nYou have been assigned to new story ".$storyname.".".
			"\nProject :".$projectname;
		$this->sendEmail($email,$subject,$body);
	}
	public function notifyStoryEstimated($email,$projectname,$storyname,$name,$surname,$time){
		$subject="Story ".$storyname." is estimated to ".$time."H";
		$body="Hello,\n\nThe story ".$storyname." has been estimated ".$time."H by ".$name." ".$surname
			."\nProject :".$projectname;
		$this->sendEmail($email,$subject,$body);
	}
	public function notifyStoryRealtime($email,$projectname,$storyname,$name,$surname,$time){
		$subject="Real time of the story ".$storyname." is delivered ".$time."H";
		$body="Hello,\n\nThe realtime of the story ".$storyname." has been delevired ".$time."H by ".$name." ".$surname
			."\nProject :".$projectname;
		$this->sendEmail($email,$subject,$body);
	}
	public function notifyStoryDone($email,$projectname,$storyname,$name,$surname){
		$subject="The story ".$storyname." finished";
		$body="Hello,\n\nThe story ".$storyname." finished by ".$name." ".$surname
			."\nProject :".$projectname;
		$this->sendEmail($email,$subject,$body);
	}

	private function sendEmail($email,$subject,$body)
	{
		$message =\Swift_Message::newInstance()
		->setSubject($subject)
		->setFrom("bb8@flexwork.io")
		->setTo($email)
		->setBody($body);
		
		//$isent=$this->mailer->send($message);
	}


}