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
use Acmtool\AppBundle\Entity\Realtime;
use Acmtool\AppBundle\Entity\WorkedHours;
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
        	if(isset($json->{'title'}) && isset($json->{"assignedTo"}) && isset($json->{"description"}) && isset($json->{"ticket_id"}))
        	{
        		$ticket=$em->getRepository("AcmtoolAppBundle:Ticket")->findOneById($json->{"ticket_id"});
        		if($ticket)
        		{
        			$task=new Task();
        			$task->setTitle($json->{"title"});
        			$task->setDescription($json->{"description"});
        			$task->setCreationdate(new \DateTime("UTC"));
        			$owner=$ticket->getProject()->getTeamLeader();
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
        	if(isset($json->{'title'}) && isset($json->{"assignedTo"})  && isset($json->{"description"}) && isset($json->{"task_id"}))
        	{
        		$task=$em->getRepository("AcmtoolAppBundle:Task")->findOneById($json->{"task_id"});
        		if($task)
        		{
        			$task->setTitle($json->{"title"});
        			$task->setDescription($json->{"description"});
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
				$workedhours=0;
				foreach ($key->getRealtimes() as $realtime) {
					$workedhours+=$realtime->getTime();
				}
				$data=array("id"=>$key->getId(),"displayid"=>$key->getDisplayId(),"title"=>$key->getTitle(),"description"=>$key->getDescription(),"estimation"=>$key->getEstimation(),"realtime"=>$key->getRealtime(),"isstarted"=>$key->getIsStarted(),"finished"=>$key->getIsFinished(),"workedhours"=>$workedhours);
				if($key->getDeveloper()!=null)
					$assignedto=array("id"=>$key->getDeveloper()->getId(),"name"=>$key->getDeveloper()->getName(),"surname"=>$key->getDeveloper()->getSurname(),"role"=>array("role"=>$developerrole["role"]));
				elseif($key->getDesigner()!=null)
					$assignedto=array("id"=>$key->getDesigner()->getId(),"name"=>$key->getDesigner()->getName(),"surname"=>$key->getDesigner()->getSurname(),"role"=>array("role"=>$designerrole["role"]));
				elseif($key->getTester()!=null)
					$assignedto=array("id"=>$key->getTester()->getId(),"name"=>$key->getTester()->getName(),"surname"=>$key->getTester()->getSurname(),"role"=>array("role"=>$testerrole["role"]));
				elseif($key->getSysadmin()!=null)
					$assignedto=array("id"=>$key->getSysadmin()->getId(),"name"=>$key->getSysadmin()->getName(),"surname"=>$key->getSysadmin()->getSurname(),"role"=>array("role"=>$sysadminrole["role"]));
				if( $assignedto!=null)
					$data["assignto"]=$assignedto;
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
	        		$task->setEstimateddate(new \DateTime("UTC"));
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
        	if(isset($json->{"task_id"}))
        	{
        		$task=$em->getRepository("AcmtoolAppBundle:Task")->findOneById($json->{"task_id"});
        		if($task){
        			$total=0;
        			foreach ($task->getRealtimes() as $key ) {
        				$total+=$key->getTime();
        			}
	        		$task->setRealtime(floatval($total));
	        		$task->setRtsetdate(new \DateTime("UTC"));
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
	public function addRealtimeAction()
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
        			$realtime=new Realtime();
        			$now=new \DateTime('UTC');
        			$realtime->setDate($now);
        			$realtime->setTime(floatval($json->{"realtime"}));
        			$realtime->setTask($task);
        			$task->addRealtime($realtime);
        			$worked=new WorkedHours();
        			$worked->setYear($now->format('Y'));
        			$worked->setMonth($now->format('m'));
        			$worked->setDay($now->format('d'));
        			$worked->setHour($now->format('H'));
        			$worked->setMinutes($now->format('i'));
        			$worked->setDayOfTheWeek($now->format('w'));
        			$worked->setWorkedhour(floatval($json->{"realtime"}));
                    $worked->setWeek($this->weekOfMonth($now));
        			$user=null;
        			if($task->getDeveloper()!=null)
						$user=$task->getDeveloper();
					elseif($task->getDesigner()!=null)
						$user=$task->getDesigner();
					elseif($task->getTester()!=null)
						$user=$task->getTester();
					elseif($task->getSysadmin()!=null)
						$user=$task->getSysadmin();
					if($user!=null)
					{
						$worked->setUser($user->getCredentials());
						$worked->setReference($realtime);
						$realtime->setWorkedHours($worked);
						$em->persist($worked);
					}
        			$em->persist($realtime);
	        		$em->flush();
	        		$response=new Response('realtime added',200);
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
	public function deleteRealtimeAction($realtime_id)
	{
		$em = $this->getDoctrine()->getManager();
		$realtime=$em->getRepository("AcmtoolAppBundle:Realtime")->findOneById($realtime_id);
		if($realtime)
		{
			$em->remove($realtime);
			$em->flush();
			$response=new Response('Realtime deleted',200);
	        return $response;
		}
		else
		{
			$response=new Response('{"error":"'.ConstValues::INVALIDREQUEST.'"}',400);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
		}
	}
	public function updateRealtimeAction()
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
        	if(isset($json->{"realtime_id"}) && isset($json->{"realtime"}))
        	{
        		$realtime=$em->getRepository("AcmtoolAppBundle:Realtime")->findOneById($json->{"realtime_id"});
        		if($realtime){
        			$realtime->setTime(floatval($json->{"realtime"}));
        			$worked=$realtime->getWorkedHours();
        			$worked->setWorkedhour(floatval($json->{"realtime"}));
	        		$em->flush();
	        		$response=new Response('realtime upadted',200);
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
	public function getRealtimesAction($task_id)
	{
		$em = $this->getDoctrine()->getManager();
		$task=$em->getRepository("AcmtoolAppBundle:Task")->findOneById($task_id);
		if($task)
		{
			$mess=array();
			$i=0;
			$total=0;
			foreach ($task->getRealtimes() as $key) {
				$editable=$this->ifToday($key->getDate());
				$data=array("id"=>$key->getId(),"date"=>date_format($key->getDate(), 'Y-m-d'),"time"=>$key->getTime(),"editable"=>$editable);
				$mess[$i]=$data;
				$total+=$key->getTime();
				$i++;
			}
			$res=array("total"=>$total,"realtimes"=>$mess);
			$response=new Response(json_encode($res),200);
	        return $response;
		}
		else
		{
			$response=new Response('{"error":"'.ConstValues::INVALIDREQUEST.'"}',400);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
		}
	}
	public function startTaskAction($task_id)
	{
		$em = $this->getDoctrine()->getManager();
		$task=$em->getRepository("AcmtoolAppBundle:Task")->findOneById($task_id);
		if($task)
		{
			$task->setIsStarted(true);
			$task->setStarteddate(new \DateTime("UTC"));
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
			$task->setFinishdate(new \DateTime("UTC"));
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
				$ticket->setTestingdate(new \DateTime("UTC"));
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
	private function ifToday($date)
	{
		$today=new \DateTime("UTC");
		$diff=$today->diff($date)->format('d');
		return ($diff<1);

	}
    private function weekOfMonth($date) {
        //Get the first day of the month.
        $d = new \DateTime('first day of this month');
        //Apply above formula.
        return intval($date->format("W")) - intval($d->format("W"));
    }
}