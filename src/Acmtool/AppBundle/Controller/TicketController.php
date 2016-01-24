<?php

namespace Acmtool\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Httpfoundation\Response;
use Acmtool\AppBundle\Entity\Ticket;
use Acmtool\AppBundle\Entity\TicketStatus;
use Acmtool\AppBundle\Entity\TicketType;
use Acmtool\AppBundle\Entity\ConstValues;
use Acmtool\AppBundle\Entity\Roles;
class TicketController extends Controller
{
	public function createAction()
	{
		$request = $this->get('request');
		$message = $request->getContent();
		$em = $this->getDoctrine()->getManager();
        $result = $this->get('acmtool_app.validation.json')->validate($message);
        if(!$result["valid"])
            return $result['response'];
        else
        {
        	$json=$result['json'];
        	if(isset($json->{"type"}) && isset($json->{"title"}) && isset($json->{"description"}) && isset($json->{"project_id"}) && isset($json->{"createdby"}))
        	{
        		$project=$em->getRepository("AcmtoolAppBundle:project")->findOneById($json->{"project_id"});
        		if($project){
        			$ticket=new Ticket();
        			$ticket->setType($json->{"type"});
        			$ticket->setProject($project);
        			$ticket->setDescription($json->{"description"});
        			$ticket->setTitle($json->{"title"});
        			$project_id=$project->getId();
        			if($project_id<10){
        				$project_id=$project_id."00";
        			}
        			elseif ($project_id>=10 && $project_id<100) {
        				$project_id=$project_id."0";
        			}
        			$ticketCount=count($project->getTickets())+1;
        			if($ticketCount<10){
        				$ticketCount=$ticketCount."00";
        			}
        			elseif ($ticketCount>=10 && $ticketCount<100) {
        				$ticketCount=$ticketCount."0";
        			}
        			$displayid=$project_id.$ticketCount;
        			$ticket->setDiplayId($displayid);
        			$ticket->setStatus(TicketStatus::DRAFT);
        			$ticket->setCreatedBy($json->{"createdby"});
        			$project->AddTicket($ticket);
        			$format = 'Y-m-d';
                    $creationdate = new \DateTime('UTC');
                    $ticket->setCreationDate($creationdate);
        			$em->persist($ticket);
	                $em->flush();
	                $response=new Response('Ticket created',200);
	                return $response;
        		}
        		else
        		{
        			$response=new Response('{"error":"'.ConstValues::INVALIDREQUEST.'"}',400);
	                $response->headers->set('Content-Type', 'application/json');
	                return $response;
        		}     		

        	}
        	else
        	{
        		$response=new Response('{"error":"'.ConstValues::INVALIDREQUEST.'"}',400);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
        	}
        	

        }
	}
	public function updateTicketAction()
	{
		$request = $this->get('request');
		$message = $request->getContent();
		$em = $this->getDoctrine()->getManager();
        $result = $this->get('acmtool_app.validation.json')->validate($message);
        if(!$result["valid"])
            return $result['response'];
        else
        {
        	$json=$result['json'];
        	if(isset($json->{"type"}) && isset($json->{"title"}) && isset($json->{"description"}) && isset($json->{"ticket_id"}))
        	{
        		$ticket=$em->getRepository("AcmtoolAppBundle:Ticket")->findOneById($json->{"ticket_id"});
        		if($ticket){
        			$ticket->setType($json->{"type"});
        			$ticket->setDescription($json->{"description"});
        			$ticket->setTitle($json->{"title"});
        			$em->flush();
	                $response=new Response('Ticket updated',200);
	                return $response;
        		}
        		else
        		{
        			$response=new Response('{"error":"'.ConstValues::INVALIDREQUEST.'"}',400);
	                $response->headers->set('Content-Type', 'application/json');
	                return $response;
        		}     		

        	}
        	else
        	{
        		$response=new Response('{"error":"'.ConstValues::INVALIDREQUEST.'"}',400);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
        	}
        	

        }
	}
	public function ticketListAction($project_id)
	{
		$em = $this->getDoctrine()->getManager();
		$project=$em->getRepository("AcmtoolAppBundle:Project")->findOneById($project_id);
		if($project)
		{
			$i=0;
			$tickets=array();
            foreach ($project->getTickets() as $key) {
                $tickets[$i]=array("id"=>$key->getId(),"displayId"=>$key->getDiplayId(),
                    "title"=>$key->getTitle(),"estimation"=>$key->getEstimation(),
                    "status"=>$key->getStatus(),"type"=>$key->getType(),"description"=>$key->getDescription(),"createdby"=>$key->getCreatedBy(),"creationdate"=>date_format($key->getCreationdate(), 'Y-m-d'),"realtime"=>$key->getRealtime());
                if($key->getStatus()==TicketStatus::REJECT)
                {
                	$tickets[$i]["rejectionmessage"]=$key->getRejectionmessage();
                }
                $tasks=array();
				$j=0;
				$developerrole=Roles::Developer();
		        $testerrole=Roles::Tester();
		        $designerrole=Roles::Designer();
		        $sysadminrole=Roles::SysAdmin();
		        $tasksnumber=count($key->getTasks());
		        $finishedTasks=0;
				foreach ($key->getTasks() as $task) {
					$data=array("id"=>$task->getId(),"displayid"=>$task->getDisplayId(),"title"=>$task->getTitle(),"description"=>$task->getDescription(),"estimation"=>$task->getEstimation(),"realtime"=>$task->getRealtime(),"isstarted"=>$task->getIsStarted(),"finished"=>$task->getIsFinished());
					if($task->getDeveloper()!=null)
                        $assignedto=array("id"=>$task->getDeveloper()->getId(),"name"=>$task->getDeveloper()->getName(),"surname"=>$task->getDeveloper()->getSurname(),"role"=>array("role"=>$developerrole["role"]));
                    elseif($task->getDesigner()!=null)
                        $assignedto=array("id"=>$task->getDesigner()->getId(),"name"=>$task->getDesigner()->getName(),"surname"=>$task->getDesigner()->getSurname(),"role"=>array("role"=>$designerrole["role"]));
                    elseif($task->getTester()!=null)
                        $assignedto=array("id"=>$task->getTester()->getId(),"name"=>$task->getTester()->getName(),"surname"=>$task->getTester()->getSurname(),"role"=>array("role"=>$testerrole["role"]));
                    elseif($task->getSysadmin()!=null)
                        $assignedto=array("id"=>$task->getSysadmin()->getId(),"name"=>$task->getSysadmin()->getName(),"surname"=>$task->getSysadmin()->getSurname(),"role"=>array("role"=>$sysadminrole["role"]));
					$data["assignto"]=$assignedto;
					$owner=array('id' =>$task->getOwner()->getId() ,"name"=>$task->getOwner()->getName(),"surname"=>$task->getOwner()->getSurname() );
					$tasks[$j]=$data;
					if($task->getIsFinished())
						$finishedTasks++;
					$j++;
				}
				$tickets[$i]["finishedtasks"]=$finishedTasks;
				$tickets[$i]["taskscount"]=$tasksnumber;
				$tickets[$i]["tasks"]=$tasks;
				$i++;
            }
            $res=new Response();
            $res->setStatusCode(200);
	        $res->headers->set('Content-Type', 'application/json');
	        $res->setContent(json_encode($tickets));
	        return $res;
		}
		else
		{
			$response=new Response('{"error":"'.ConstValues::INVALIDREQUEST.'"}',400);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
		}
	}
	public function deleteTicketAction($ticket_id)
	{
		$em = $this->getDoctrine()->getManager();
		$ticket=$em->getRepository("AcmtoolAppBundle:Ticket")->findOneById($ticket_id);
		if($ticket)
		{
			$em->remove($ticket);
			$em->flush();
			$response=new Response('Ticket deleted',200);
	        return $response;
		}
		else
		{
			$response=new Response('{"error":"'.ConstValues::INVALIDREQUEST.'"}',400);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
		}
	}
	public function sendToClientAction($ticket_id)
	{
		$em = $this->getDoctrine()->getManager();
		$ticket=$em->getRepository("AcmtoolAppBundle:Ticket")->findOneById($ticket_id);
		if($ticket)
		{
			$ticket->setStatus(TicketStatus::GOPRODUCTION);
			$estimation=0;
			$i=0;
			foreach ($ticket->getTasks() as $key) {
				$estimation+=$key->getEstimation();
			}
			$ticket->setEstimation($estimation);
			$mess=array("estimation"=>$estimation);
			$em->flush();
			$res=new Response();
	        $res->setStatusCode(200);
	        $res->setContent(json_encode($mess));
	        return $res;
		}
		else
		{
			$response=new Response('{"error":"'.ConstValues::INVALIDREQUEST.'"}',400);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
		}
	}
	public function sendToProdAction($ticket_id)
	{
		$em = $this->getDoctrine()->getManager();
		$ticket=$em->getRepository("AcmtoolAppBundle:Ticket")->findOneById($ticket_id);
		if($ticket)
		{
			$ticket->setStatus(TicketStatus::PRODUCTION);
			$estimation=0;
			$em->flush();
			$res=new Response();
	        $res->setStatusCode(200);
	        $res->setContent("Ticket in production");
	        return $res;
		}
		else
		{
			$response=new Response('{"error":"'.ConstValues::INVALIDREQUEST.'"}',400);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
		}
	}
	public function deliverToClientAction($ticket_id)
	{
		$em = $this->getDoctrine()->getManager();
		$ticket=$em->getRepository("AcmtoolAppBundle:Ticket")->findOneById($ticket_id);
		if($ticket)
		{
			$done=true;
			$realtime=0;
			foreach ($ticket->getTasks() as $key) {
				if($key->getRealtime()==null)
					$done=false;
				else
					$realtime+=$key->getRealtime();
			}
			if($done){
				$ticket->setStatus(TicketStatus::ACCEPT);
				$ticket->setRealtime($realtime);
				$mess=array("realtime"=>$realtime);
				$em->flush();
				$res=new Response();
		        $res->setStatusCode(200);
		        $res->setContent(json_encode($mess));
		        return $res;
			}
			else
			{
				$response=new Response('{"error":"'.ConstValues::INVALIDREQUEST.'"}',400);
            	$response->headers->set('Content-Type', 'application/json');
            	return $response;
			}
			
		}
		else
		{
			$response=new Response('{"error":"'.ConstValues::INVALIDREQUEST.'"}',400);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
		}
	}
	public function startEstimationAction($ticket_id)
	{
		$em = $this->getDoctrine()->getManager();
		$ticket=$em->getRepository("AcmtoolAppBundle:Ticket")->findOneById($ticket_id);
		if($ticket)
		{
			$ticket->setStatus(TicketStatus::ESTIMATION);
			$em->flush();
			$res=new Response();
	        $res->setStatusCode(200);
	        $res->setContent("Ticket moved to estimation");
	        return $res;
		}
		else
		{
			$response=new Response('{"error":"'.ConstValues::INVALIDREQUEST.'"}',400);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
		}
	}
	public function acceptEstimationAction($ticket_id)
	{
		$em = $this->getDoctrine()->getManager();
		$ticket=$em->getRepository("AcmtoolAppBundle:Ticket")->findOneById($ticket_id);
		if($ticket)
		{
			$ticket->setStatus(TicketStatus::WAITING);
			$em->flush();
			$res=new Response();
	        $res->setStatusCode(200);
	        $res->setContent("Ticket moved to waiting for production");
	        return $res;
		}
		else
		{
			$response=new Response('{"error":"'.ConstValues::INVALIDREQUEST.'"}',400);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
		}
	}
	public function rejectEstimationAction($ticket_id)
	{
		$em = $this->getDoctrine()->getManager();
		$ticket=$em->getRepository("AcmtoolAppBundle:Ticket")->findOneById($ticket_id);
		if($ticket)
		{
			$ticket->setStatus(TicketStatus::ESTIMATION);
			$em->flush();
			$res=new Response();
	        $res->setStatusCode(200);
	        $res->setContent("Estimation rejected");
	        return $res;
		}
		else
		{
			$response=new Response('{"error":"'.ConstValues::INVALIDREQUEST.'"}',400);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
		}
	}
	public function acceptTicketAction($ticket_id){
		$em = $this->getDoctrine()->getManager();
		$ticket=$em->getRepository("AcmtoolAppBundle:Ticket")->findOneById($ticket_id);
		if($ticket)
		{
			$ticket->setStatus(TicketStatus::DONE);
			$em->flush();
			$res=new Response();
	        $res->setStatusCode(200);
	        $res->setContent("Ticket Accepted");
	        return $res;
		}
		else
		{
			$response=new Response('{"error":"'.ConstValues::INVALIDREQUEST.'"}',400);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
		}
	}
	public function rejectTicketAction()
	{
		$request = $this->get('request');
		$message = $request->getContent();
		$em = $this->getDoctrine()->getManager();
        $result = $this->get('acmtool_app.validation.json')->validate($message);
        if(!$result["valid"])
            return $result['response'];
        else
        {
        	$json=$result['json'];
        	if(isset($json->{"ticket_id"}) && isset($json->{"message"}))
        	{
        		$ticket=$em->getRepository("AcmtoolAppBundle:Ticket")->findOneById($json->{"ticket_id"});
				if($ticket)
				{
					$ticket->setStatus(TicketStatus::REJECT);
					$ticket->setRejectionmessage($json->{"message"});
					$em->flush();
					$res=new Response();
			        $res->setStatusCode(200);
			        $res->setContent("Ticket Rjected");
			        return $res;
				}
				else
				{
					$response=new Response('{"error":"'.ConstValues::INVALIDREQUEST.'"}',400);
		            $response->headers->set('Content-Type', 'application/json');
		            return $response;
				}
        	}
        	else
			{
				$response=new Response('{"error":"'.ConstValues::INVALIDREQUEST.'"}',400);
	            $response->headers->set('Content-Type', 'application/json');
	            return $response;
			}
        }
	}
	public function getTicketTypesAction()
	{
		$types=array(TicketType::All(),TicketType::Feature(),TicketType::Bug(),TicketType::Concept(),TicketType::Design());
		$res=new Response();
        $res->setStatusCode(200);
        $res->headers->set('Content-Type', 'application/json');
        $res->setContent(json_encode($types));
        return $res;
	}
}