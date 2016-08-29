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
use Acmtool\AppBundle\Entity\TaskTypes;
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
        	if(isset($json->{"title"}) && isset($json->{"description"}) && isset($json->{"project_id"}) && isset($json->{"createdby"}))
        	{
        		$user=$this->get("security.context")->getToken()->getUser();
                $project=$em->getRepository("AcmtoolAppBundle:Project")->getProjectByLoggedUser($user,$json->{"project_id"});
        		if($project){
        			$ticket=new Ticket();
        			$ticket->setProject($project);
        			$ticket->setDescription($json->{"description"});
        			$ticket->setTitle($json->{"title"});
        			
        			$ticket->setStatus(TicketStatus::DRAFT);
        			$ticket->setCreatedBy($json->{"createdby"});
        			$project->AddTicket($ticket);
        			$format = 'Y-m-d';
                    $creationdate = new \DateTime('UTC');
                    $ticket->setCreationDate($creationdate);
                    $ticket->setDiplayId("-1");
        			$em->persist($ticket);
	                $em->flush();
	                $project_id=$project->getId();
        			if($project_id<10){
        				$project_id='00'.$project_id;
        			}
        			elseif ($project_id>=10 && $project_id<100) {
        				$project_id='0'.$project_id;
        			}
        			$ticketCount=$ticket->getId();
        			if($ticketCount<10){
        				$ticketCount="00".$ticketCount;
        			}
        			elseif ($ticketCount>=10 && $ticketCount<100) {
        				$ticketCount="0".$ticketCount;
        			}
        			$displayid=$project_id.$ticketCount;
        			$ticket->setDiplayId($displayid);
        			$em->flush();
	                $emails=array();
	                array_push($emails, $project->getKeyaccount()->getEmail());
	                if($project->getTeamleader())
	                	array_push($emails, $project->getTeamleader()->getLogin());
	                $this->get("acmtool_app.email.notifier")->notifyTicketCreated($emails,$project->getName(),$ticket->getTitle());
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
        	if(isset($json->{"title"}) && isset($json->{"description"}) && isset($json->{"ticket_id"}))
        	{
        		$ticket=$em->getRepository("AcmtoolAppBundle:Ticket")->findOneByDiplayId($json->{"ticket_id"});
        		$user=$this->get("security.context")->getToken()->getUser();
                $project=$em->getRepository("AcmtoolAppBundle:Project")->getProjectByLoggedUser($user,$ticket->getProject()->getDisplayId());
        		if($ticket && $project){
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
		$user=$this->get("security.context")->getToken()->getUser();
        $project=$em->getRepository("AcmtoolAppBundle:Project")->getProjectByLoggedUser($user,$project_id);
		if($project)
		{
			$i=0;
			$tickets=array();
            foreach ($project->getTickets() as $key) {
                $tickets[$i]=array("id"=>$key->getDiplayId(),"displayId"=>$key->getDiplayId(),
                    "title"=>$key->getTitle(),"estimation"=>$key->getEstimation(),
                    "status"=>$key->getStatus(),"description"=>$key->getDescription(),"createdby"=>$key->getCreatedBy(),"creationdate"=>date_format($key->getCreationdate(), 'Y-m-d'),"realtime"=>$key->getRealtime());
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
					 $assignedto=null;
					if($task->getDeveloper()!=null)
                        $assignedto=array("id"=>$task->getDeveloper()->getId(),"name"=>$task->getDeveloper()->getName(),"surname"=>$task->getDeveloper()->getSurname(),"role"=>array("role"=>$developerrole["role"]));
                    elseif($task->getDesigner()!=null)
                        $assignedto=array("id"=>$task->getDesigner()->getId(),"name"=>$task->getDesigner()->getName(),"surname"=>$task->getDesigner()->getSurname(),"role"=>array("role"=>$designerrole["role"]));
                    elseif($task->getTester()!=null)
                        $assignedto=array("id"=>$task->getTester()->getId(),"name"=>$task->getTester()->getName(),"surname"=>$task->getTester()->getSurname(),"role"=>array("role"=>$testerrole["role"]));
                    elseif($task->getSysadmin()!=null)
                        $assignedto=array("id"=>$task->getSysadmin()->getId(),"name"=>$task->getSysadmin()->getName(),"surname"=>$task->getSysadmin()->getSurname(),"role"=>array("role"=>$sysadminrole["role"]));
                    if( $assignedto!=null)
						$data["assignto"]=$assignedto;
	                if($task->getType()==null)
                    	$data["type"]=TaskTypes::$BACKEND['type'];
	                else
	                    $data["type"]=$task->getType();
	                if($task->getIsAccepted()===null)
                    	$data["accepted"]=true;
                	else
                    	$data["accepted"]=$task->getIsAccepted();
					$tasks[$j]=$data;
					if($task->getIsFinished())
						$finishedTasks++;
					$j++;
				}
				$tickets[$i]["finishedtasks"]=$finishedTasks;
				$tickets[$i]["taskscount"]=$tasksnumber;
				$tickets[$i]["tasks"]=$tasks;
				$tickets[$i]["open"]=false;
				$tickets[$i]["billed"]=false;
				$tickets[$i]["payed"]=false;
				if($key->getIsPayed())
				{
					$tickets[$i]["payed"]=true;
				}
				elseif($key->getIsBilled())
				{
					$tickets[$i]["billed"]=true;
				}
				else
					$tickets[$i]["open"]=true;
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
		$ticket=$em->getRepository("AcmtoolAppBundle:Ticket")->findOneByDiplayId($ticket_id);
		$user=$this->get("security.context")->getToken()->getUser();
        $project=$em->getRepository("AcmtoolAppBundle:Project")->getProjectByLoggedUser($user,$ticket->getProject()->getDisplayId());
		if($ticket && $project)
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
		$ticket=$em->getRepository("AcmtoolAppBundle:Ticket")->findOneByDiplayId($ticket_id);
		$user=$this->get("security.context")->getToken()->getUser();
        $project=$em->getRepository("AcmtoolAppBundle:Project")->getProjectByLoggedUser($user,$ticket->getProject()->getDisplayId());
		if($ticket && $project)
		{
			$isadmin=$this->get('security.context')->isGranted("ROLE_ADMIN");
			if($project->getTeamleader())
            	$isTeamLeader=($project->getTeamleader()->getId()==$this->get('security.context')->getToken()->getUser()->getCredentials()->getID());
            else
            	$isTeamLeader=false;
            if($isadmin || $isTeamLeader)
            {
            	$ticket->setStatus(TicketStatus::GOPRODUCTION);
				$estimation=0;
				$i=0;
				foreach ($ticket->getTasks() as $key) {
					$estimation+=$key->getEstimation();
				}
				$ticket->setEstimation($estimation);
				$ticket->setEstimateddate(new \DateTime("UTC"));
				$mess=array("estimation"=>$estimation);
				$em->flush();
				$emails=array();
				//Todo:Add client email to notification email
            	array_push($emails, $project->getKeyaccount()->getEmail());
            	$this->get("acmtool_app.email.notifier")->notifyTicketEstimated($emails,$project->getName(),$ticket->getTitle(),$project->getOwner()->getCompanyname());
				$res=new Response();
		        $res->setStatusCode(200);
		        $res->setContent(json_encode($mess));
		        return $res;
            }
            else
            	return new Response(403);
			
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
		$ticket=$em->getRepository("AcmtoolAppBundle:Ticket")->findOneByDiplayId($ticket_id);
		$user=$this->get("security.context")->getToken()->getUser();
        $project=$em->getRepository("AcmtoolAppBundle:Project")->getProjectByLoggedUser($user,$ticket->getProject()->getDisplayId());
		if($ticket && $project)
		{
			$isadmin=$this->get('security.context')->isGranted("ROLE_ADMIN");
			if($project->getTeamleader())
            	$isTeamLeader=($project->getTeamleader()->getId()==$this->get('security.context')->getToken()->getUser()->getCredentials()->getID());
            else
            	$isTeamLeader=false;
            if($isadmin || $isTeamLeader)
            {
            	$ticket->setStatus(TicketStatus::PRODUCTION);
				$ticket->setProductiondate(new \DateTime("UTC"));
				$estimation=0;
				$em->flush();
				$emails=array();
				//Todo:Add client email to notification email
            	array_push($emails, $project->getKeyaccount()->getEmail());
            	$this->get("acmtool_app.email.notifier")->notifyTicketinProduction($emails,$project->getName(),$ticket->getTitle(),$project->getOwner()->getCompanyname());
				$res=new Response();
		        $res->setStatusCode(200);
		        $res->setContent("Ticket in production");
		        return $res;
            }
            else
            	return new Response(403);
			
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
		$ticket=$em->getRepository("AcmtoolAppBundle:Ticket")->findOneByDiplayId($ticket_id);
		$user=$this->get("security.context")->getToken()->getUser();
        $project=$em->getRepository("AcmtoolAppBundle:Project")->getProjectByLoggedUser($user,$ticket->getProject()->getDisplayId());
		if($ticket && $project)
		{
			$project=$ticket->getProject();
			$isadmin=$this->get('security.context')->isGranted("ROLE_ADMIN");
			if($project->getTeamleader())
            	$isTeamLeader=($project->getTeamleader()->getId()==$this->get('security.context')->getToken()->getUser()->getCredentials()->getID());
            else
            	$isTeamLeader=false;
            if($isadmin || $isTeamLeader)
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
					$ticket->setDeliverydate(new \DateTime("UTC"));
					$mess=array("realtime"=>$realtime);
					$em->flush();
					$emails=array();
					//Todo:Add client email to notification email
            		array_push($emails, $project->getKeyaccount()->getEmail());
            		$this->get("acmtool_app.email.notifier")->notifyTicketDelivered($emails,$project->getName(),$ticket->getTitle(),$project->getOwner()->getCompanyname());
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
            	return new Response(403);
			
			
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
		$ticket=$em->getRepository("AcmtoolAppBundle:Ticket")->findOneByDiplayId($ticket_id);
		$user=$this->get("security.context")->getToken()->getUser();
        $project=$em->getRepository("AcmtoolAppBundle:Project")->getProjectByLoggedUser($user,$ticket->getProject()->getDisplayId());
		if($ticket && $project)
		{
			$ticket->setStatus(TicketStatus::ESTIMATION);
			$ticket->setStarteddate(new \DateTime("UTC"));
			$em->flush();
			$emails=array();
            array_push($emails, $project->getKeyaccount()->getEmail());
            if($project->getTeamleader())
            	array_push($emails, $project->getTeamleader()->getLogin());
            $this->get("acmtool_app.email.notifier")->notifyTicketStarted($emails,$project->getName(),$ticket->getTitle());
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
		$ticket=$em->getRepository("AcmtoolAppBundle:Ticket")->findOneByDiplayId($ticket_id);
		$user=$this->get("security.context")->getToken()->getUser();
        $project=$em->getRepository("AcmtoolAppBundle:Project")->getProjectByLoggedUser($user,$ticket->getProject()->getDisplayId());
		if($ticket && $project)
		{
			$ticket->setStatus(TicketStatus::WAITING);
			$ticket->setEstimateconfirmedddate(new \DateTime("UTC"));
			$em->flush();
			$emails=array();
            array_push($emails, $project->getKeyaccount()->getEmail());
            if($project->getTeamleader())
            	array_push($emails, $project->getTeamleader()->getLogin());
            $this->get("acmtool_app.email.notifier")->notifyTicketEstimationAccepted($emails,$project->getName(),$ticket->getTitle());
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
		$ticket=$em->getRepository("AcmtoolAppBundle:Ticket")->findOneByDiplayId($ticket_id);
		$user=$this->get("security.context")->getToken()->getUser();
        $project=$em->getRepository("AcmtoolAppBundle:Project")->getProjectByLoggedUser($user,$ticket->getProject()->getDisplayId());
		if($ticket && $project)
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
		$ticket=$em->getRepository("AcmtoolAppBundle:Ticket")->findOneByDiplayId($ticket_id);
		$user=$this->get("security.context")->getToken()->getUser();
        $project=$em->getRepository("AcmtoolAppBundle:Project")->getProjectByLoggedUser($user,$ticket->getProject()->getDisplayId());
		if($ticket && $project)
		{
			$ticket->setStatus(TicketStatus::DONE);
			$ticket->setFinisheddate(new \DateTime("UTC"));
			$em->flush();
			$emails=array();
			array_push($emails, $project->getKeyaccount()->getEmail());
	        if($project->getTeamleader())
	            array_push($emails, $project->getTeamleader()->getLogin());
	        $this->get("acmtool_app.email.notifier")->notifyTicketAccepted($emails,$project->getName(),$ticket->getTitle());
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
        		$ticket=$em->getRepository("AcmtoolAppBundle:Ticket")->findOneByDiplayId($json->{"ticket_id"});
        		$user=$this->get("security.context")->getToken()->getUser();
        		$project=$em->getRepository("AcmtoolAppBundle:Project")->getProjectByLoggedUser($user,$ticket->getProject()->getDisplayId());
				if($ticket && $project)
				{
					$ticket->setStatus(TicketStatus::REJECT);
					$ticket->setRejecteddate(new \DateTime("UTC"));
					$ticket->setRejectionmessage($json->{"message"});
					$em->flush();
					$emails=array();
					array_push($emails, $project->getKeyaccount()->getEmail());
	        		if($project->getTeamleader())
	            		array_push($emails, $project->getTeamleader()->getLogin());
	        		$this->get("acmtool_app.email.notifier")->notifyTicketRejected($emails,$project->getName(),$ticket->getTitle());
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
	public function markAsBilledAction($ticket_id)
	{
		$em = $this->getDoctrine()->getManager();
		$ticket=$em->getRepository("AcmtoolAppBundle:Ticket")->findOneByDiplayId($ticket_id);
		$user=$this->get("security.context")->getToken()->getUser();
        $project=$em->getRepository("AcmtoolAppBundle:Project")->getProjectByLoggedUser($user,$ticket->getProject()->getDisplayId());
        if($ticket && $project)
        {
        	$ticket->setIsBilled(true);
        	$ticket->setIsPayed(false);
        	$em->flush();
        	$res=new Response();
	        $res->setStatusCode(200);
	        $res->setContent("Ticket billed");
	        return $res;

        }
        else
		{
			$response=new Response('{"error":"'.ConstValues::INVALIDREQUEST.'"}',400);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
		}
	}
	public function markAsPayedAction($ticket_id)
	{
		$em = $this->getDoctrine()->getManager();
		$ticket=$em->getRepository("AcmtoolAppBundle:Ticket")->findOneByDiplayId($ticket_id);
		$user=$this->get("security.context")->getToken()->getUser();
        $project=$em->getRepository("AcmtoolAppBundle:Project")->getProjectByLoggedUser($user,$ticket->getProject()->getDisplayId());
        if($ticket && $project)
        {
        	$ticket->setIsBilled(true);
        	$ticket->setIsPayed(true);
        	$em->flush();
        	$res=new Response();
	        $res->setStatusCode(200);
	        $res->setContent("Ticket payed");
	        return $res;

        }
        else
		{
			$response=new Response('{"error":"'.ConstValues::INVALIDREQUEST.'"}',400);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
		}
	}
	public function markAsOpenAction($ticket_id)
	{
		$em = $this->getDoctrine()->getManager();
		$ticket=$em->getRepository("AcmtoolAppBundle:Ticket")->findOneByDiplayId($ticket_id);
		$user=$this->get("security.context")->getToken()->getUser();
        $project=$em->getRepository("AcmtoolAppBundle:Project")->getProjectByLoggedUser($user,$ticket->getProject()->getDisplayId());
        if($ticket && $project)
        {
        	$ticket->setIsBilled(false);
        	$ticket->setIsPayed(false);
        	$em->flush();
        	$res=new Response();
	        $res->setStatusCode(200);
	        $res->setContent("Ticket open");
	        return $res;

        }
        else
		{
			$response=new Response('{"error":"'.ConstValues::INVALIDREQUEST.'"}',400);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
		}
	}
	public function markManyAsBilledAction()
	{
		$request = $this->get('request');
		$message = $request->getContent();
		$em = $this->getDoctrine()->getManager();
        $result = $this->get('acmtool_app.validation.json')->validate($message);
        $user=$this->get("security.context")->getToken()->getUser();
        if(!$result["valid"])
            return $result['response'];
        else
        {
        	$json=$result['json'];
        	if(isset($json->{"tickets"}))
        	{
        		foreach ($json->{"tickets"} as $key) {
        			$ticket=$em->getRepository("AcmtoolAppBundle:Ticket")->findOneByDiplayId($key);
        			$project=$em->getRepository("AcmtoolAppBundle:Project")->getProjectByLoggedUser($user,$ticket->getProject()->getDisplayId());
        			if($ticket && $project)
			        {
			        	$ticket->setIsBilled(true);
			        	$ticket->setIsPayed(false);
			        }
        		}
        		$em->flush();
	        	$res=new Response();
		        $res->setStatusCode(200);
		        $res->setContent("Ticket open");
		        return $res;
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