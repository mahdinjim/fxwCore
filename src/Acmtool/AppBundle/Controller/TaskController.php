<?php

namespace Acmtool\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Httpfoundation\Response;
use Acmtool\AppBundle\Entity\ConstValues;
use Acmtool\AppBundle\Entity\Task;
use Acmtool\AppBundle\Entity\Roles;
use Acmtool\AppBundle\Entity\TasksTypes;
use Acmtool\AppBundle\Entity\TicketStatus;

class TaskController extends Controller
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
        	if(isset($json->{'title'}) && isset($json->{"assignedTo"}) && isset($json->{"owner"}) && isset($json->{"description"}) && isset($json->{"ticket_id"}))
        	{
        		$ticket=$em->getRepository("AcmtoolAppBundle:Ticket")->findOneById($json->{"ticket_id"});
        		if($ticket)
        		{
        			$task=new Task();
        			$task->setTitle($json->{"title"});
        			$task->setDescription($json->{"description"});
        			$owner=$em->getRepository("AcmtoolAppBundle:TeamLeader")->findOneById($json->{"owner"});
        			$task->setOwner($owner);
        			$task->setTicket($ticket);
        			$displayid=$ticket->getDiplayId()."-".(count($ticket->getTasks())+1);
        			$task->setDisplayId($displayid);
        			$developerrole=Roles::Developer();
			        $testerrole=Roles::Tester();
			        $designerrole=Roles::Designer();
			        $sysadminrole=Roles::SysAdmin();
        			if($json->{"assignedTo"}->{"role"}==$developerrole["role"])
        			{
        				$assigned=$em->getRepository("AcmtoolAppBundle:Developer")->findOneById($json->{"assignedTo"}->{"id"});
        				$task->setDeveloper($assigned);

        			}
        			elseif($json->{"assignedTo"}->{"role"}==$testerrole["role"])
        			{
        				$assigned=$em->getRepository("AcmtoolAppBundle:Tester")->findOneById($json->{"assignedTo"}->{"id"});
        				$task->setTester($assigned);

        			}
        			elseif($json->{"assignedTo"}->{"role"}==$designerrole["role"])
        			{
        				$assigned=$em->getRepository("AcmtoolAppBundle:Designer")->findOneById($json->{"assignedTo"}->{"id"});
        				$task->setDesigner($assigned);

        			}
        			elseif($json->{"assignedTo"}->{"role"}==$sysadminrole["role"])
        			{
        				$assigned=$em->getRepository("AcmtoolAppBundle:SystemAdmin")->findOneById($json->{"assignedTo"}->{"id"});
        				$task->setSysadmin($assigned);

        			}
        			$task->setIsStarted(false);
        			$task->setisFinished(false);
        			$task->setStatus(TasksTypes::WAITING);
        			$em->persist($task);
	                $em->flush();
	                $response=new Response('Task created',200);
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
	public function updateAction()
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
        	if(isset($json->{'title'}) && isset($json->{"assignedTo"}) && isset($json->{"owner"}) && isset($json->{"description"}) && isset($json->{"task_id"}))
        	{
        		$task=$em->getRepository("AcmtoolAppBundle:Task")->findOneById($json->{"task_id"});
        		if($task)
        		{
        			$task->setTitle($json->{"title"});
        			$task->setDescription($json->{"description"});
        			$owner=$em->getRepository("AcmtoolAppBundle:TeamLeader")->findOneById($json->{"owner"});
        			$task->setOwner($owner);
        			$developerrole=Roles::Developer();
			        $testerrole=Roles::Tester();
			        $designerrole=Roles::Designer();
			        $sysadminrole=Roles::SysAdmin();
        			if($json->{"assignedTo"}->{"role"}==$developerrole["role"])
        			{
        				$assigned=$em->getRepository("AcmtoolAppBundle:Developer")->findOneById($json->{"assignedTo"}->{"id"});
        				$task->setDeveloper($assigned);

        			}
        			elseif($json->{"assignedTo"}->{"role"}==$testerrole["role"])
        			{
        				$assigned=$em->getRepository("AcmtoolAppBundle:Tester")->findOneById($json->{"assignedTo"}->{"id"});
        				$task->setTester($assigned);

        			}
        			elseif($json->{"assignedTo"}->{"role"}==$designerrole["role"])
        			{
        				$assigned=$em->getRepository("AcmtoolAppBundle:Designer")->findOneById($json->{"assignedTo"}->{"id"});
        				$task->setDesigner($assigned);

        			}
        			elseif($json->{"assignedTo"}->{"role"}==$sysadminrole["role"])
        			{
        				$assigned=$em->getRepository("AcmtoolAppBundle:SystemAdmin")->findOneById($json->{"assignedTo"}->{"id"});
        				$task->setSysadmin($assigned);

        			}
	                $em->flush();
	                $response=new Response('Task updated',200);
	                return $response;
        		}
        		else
        		{
        			$response=new Response('{"error":"'.ConstValues::INVALIDREQUEST.'"}',400);
	                $response->headers->set('Content-Type', 'application/json');
	                return $response;
        		}  
        	}
        }
	}
	public function listAction($ticket_id)
	{
		$em = $this->getDoctrine()->getManager();
		$ticket=$em->getRepository("AcmtoolAppBundle:Ticket")->findOneById($ticket_id);
		if($ticket)
		{
			$mess=array();
			$i=0;
			$developerrole=Roles::Developer();
	        $testerrole=Roles::Tester();
	        $designerrole=Roles::Designer();
	        $sysadminrole=Roles::SysAdmin();
			foreach ($ticket->getTasks() as $key) {
				$data=array("id"=>$key->getId(),"displayid"=>$key->getDisplayId(),"title"=>$key->getTitle(),"description"=>$key->getDescription(),"estimation"=>$key->getEstimation(),"realtime"=>$key->getRealtime(),"isstarted"=>$key->getIsStarted(),"finished"=>$key->getIsFinished());
				if($key->getDeveloper()!=null)
					$assignedto=array("id"=>$key->getDeveloper()->getId(),"name"=>$key->getDeveloper()->getName(),"surname"=>$key->getDeveloper()->getSurname(),"role"=>array("role"=>$developerrole["role"]));
				elseif($key->getDesigner()!=null)
					$assignedto=array("id"=>$key->getDesigner()->getId(),"name"=>$key->getDesigner()->getName(),"surname"=>$key->getDesigner()->getSurname(),"role"=>array("role"=>$designerrole["role"]));
				elseif($key->getTester()!=null)
					$assignedto=array("id"=>$key->getTester()->getId(),"name"=>$key->getTester()->getName(),"surname"=>$key->getTester()->getSurname(),"role"=>array("role"=>$testerrole["role"]));
				elseif($key->getSysadmin()!=null)
					$assignedto=array("id"=>$key->getSysadmin()->getId(),"name"=>$key->getSysadmin()->getName(),"surname"=>$key->getSysadmin()->getSurname(),"role"=>array("role"=>$sysadminrole["role"]));
				$data["assignto"]=$assignedto;
				$owner=array('id' =>$key->getOwner()->getId() ,"name"=>$key->getOwner()->getName(),"surname"=>$key->getOwner()->getSurname() );
				$mess[$i]=$data;
				$i++;
			}
			$res=new Response();
            $res->setStatusCode(200);
	        $res->headers->set('Content-Type', 'application/json');
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
	public function setEstimationAction()
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
        	if(isset($json->{"task_id"}) && isset($json->{"estimation"}))
        	{
        		$task=$em->getRepository("AcmtoolAppBundle:Task")->findOneById($json->{"task_id"});
        		if($task){
	        		$task->setEstimation(floatval($json->{"estimation"}));
	        		$em->flush();
	        		$response=new Response('Estimation set',200);
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
	public function setRealtimeAction()
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
        	if(isset($json->{"task_id"}) && isset($json->{"realtime"}))
        	{
        		$task=$em->getRepository("AcmtoolAppBundle:Task")->findOneById($json->{"task_id"});
        		if($task){
	        		$task->setRealtime(floatval($json->{"realtime"}));
	        		$em->flush();
	        		$response=new Response('realtime set',200);
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
	public function startTaskAction($task_id)
	{
		$em = $this->getDoctrine()->getManager();
		$task=$em->getRepository("AcmtoolAppBundle:Task")->findOneById($task_id);
		if($task)
		{
			$task->setIsStarted(true);
			$em->flush();
	        $response=new Response('Task started',200);
		    return $response;

		}
		else
		{
			$response=new Response('{"error":"'.ConstValues::INVALIDREQUEST.'"}',400);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
		}  
	}
	public function finishTaskAction($task_id)
	{
		$em = $this->getDoctrine()->getManager();
		$task=$em->getRepository("AcmtoolAppBundle:Task")->findOneById($task_id);
		if($task)
		{
			$task->setisFinished(true);
			$ticket=$task->getTicket();
			$done=true;
			foreach ($ticket->getTasks() as $key) {
				if($key->getId()!=$task_id)
				{
					if(!$key->getIsFinished())
						$done=false;
				}
			}
			if($done)
			{
				$ticket->setStatus(TicketStatus::TESTING);
			}
			$mess=array("done"=>$done);
			$em->flush();
	        $response=new Response(json_encode($mess),200);
		    return $response;

		}
		else
		{
			$response=new Response('{"error":"'.ConstValues::INVALIDREQUEST.'"}',400);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
		}  
	}
	public function deleteAction($task_id)
	{
		$em = $this->getDoctrine()->getManager();
		$task=$em->getRepository("AcmtoolAppBundle:Task")->findOneById($task_id);
		if($task)
		{
			$em->remove($task);
			$em->flush();
			$response=new Response('Task deleted',200);
	        return $response;
		}
		else
		{
			$response=new Response('{"error":"'.ConstValues::INVALIDREQUEST.'"}',400);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
		}
	}
}