<?php

namespace Acmtool\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Acmtool\AppBundle\Entity\TicketStatus;
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
		if($ticket)
		{
			$project=$em->getRepository("AcmtoolAppBundle:Project")->getProjectByLoggedUser($user,$ticket->getProject()->getDisplayId());
			if($project)
			{
				if($tikcet->getStatus()==TicketStatus::DRAFT)
				{
					$ticket->setStatus(TicketStatus::ESTIMATION);
					$ticket->setStarteddate(new \DateTime("UTC"));
					$em->flush();
					$emails=array();
            		array_push($emails, $project->getKeyaccount()->getEmail());
            		if($project->getTeamleader())
            			array_push($emails, $project->getTeamleader()->getLogin());
            		$this->get("acmtool_app.email.notifier")->notifyTicketStarted($emails,$project->getName(),$ticket->getTitle());
            		return $this->render('NotificationResponse/success.html.twig', array("message" => $categories));

				}
				return $this->render('NotificationResponse/error.html.twig', array("message" => "The ticket already started"));
			}
			return $this->render('NotificationResponse/error.html.twig', array("message" => "You don't have access to this project"));
		}
		return $this->render('NotificationResponse/error.html.twig', array("message" => "The tikcet don't exist"));

	}
	public function acceptTicketEstimationAction($ticket_id,$token)
	{
		//step 1 get the user 
		//step 2 get the project and verify if the user have access to it
		//step 3 test if the ticket is in estimation
		//step 4 if ticket in estimation change to waiting and send to congratulation page
		//step 5 if tikcet not in estimation state redirect to this estimation is already accepted
		$request = $this->get('request');
		$user=$this->get('security.context')->getToken()->getUser();
		$em = $this->getDoctrine()->getManager();	
		$ticket=$em->getRepository("AcmtoolAppBundle:Ticket")->findOneByDiplayId($ticket_id);
		if($ticket)
		{
			$project=$em->getRepository("AcmtoolAppBundle:Project")->getProjectByLoggedUser($user,$ticket->getProject()->getDisplayId());
			if($project)
			{
				if($tikcet->getStatus()==TicketStatus::ESTIMATION)
				{
					$ticket->setStatus(TicketStatus::WAITING);
					$ticket->setEstimateconfirmedddate(new \DateTime("UTC"));
					$em->flush();
					$emails=array();
		            array_push($emails, $project->getKeyaccount()->getEmail());
		            if($project->getTeamleader())
		            	array_push($emails, $project->getTeamleader()->getLogin());
		            $this->get("acmtool_app.email.notifier")->notifyTicketEstimationAccepted($emails,$project->getName(),$ticket->getTitle());
            		return $this->render('NotificationResponse/success.html.twig', array("message" => $categories));

				}
				return $this->render('NotificationResponse/error.html.twig', array("message" => "The ticket already started"));
			}
			return $this->render('NotificationResponse/error.html.twig', array("message" => "You don't have access to this project"));
		}
		return $this->render('NotificationResponse/error.html.twig', array("message" => "The tikcet don't exist"));

	}
}
