<?php

namespace Acmtool\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class EmailNotifController extends Controller
{
	public function startTicketAction($ticket_id,$token)
	{
		//step 1 get the user 
		//step 2 get the project and verify if the user have access to it
		//step 3 test if the ticket is in draft
		//step 4 if ticket in draft change to start send congratulation page
		//step 5 if tikcet not in draft state redirect to this ticket already accepted
		$request = $this->get('request');
		$user=$this->get('security.context')->getToken()->getUser();
		$em = $this->getDoctrine()->getManager();
		$ticket=$em->getRepository("AcmtoolAppBundle:Ticket")->findOneByDiplayId($ticket_id);
		$project=$em->getRepository("AcmtoolAppBundle:Project")->getProjectByLoggedUser($user,$ticket->getProject()->getDisplayId());

	}
}
