<?php

namespace Acmtool\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Httpfoundation\Response;
use Acmtool\AppBundle\Entity\ConstValues;
use Acmtool\AppBundle\Entity\Task;
use Acmtool\AppBundle\Entity\Roles;
use Acmtool\AppBundle\Entity\TaskTypes;
use Acmtool\AppBundle\Entity\TicketStatus;
use Acmtool\AppBundle\Entity\Realtime;
use Acmtool\AppBundle\Entity\WorkedHours;
use Acmtool\AppBundle\Entity\Ticket;

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
        	if(isset($json->{"type"}) && isset($json->{'title'}) && isset($json->{"assignedTo"}) && isset($json->{"description"}) && isset($json->{"ticket_id"}))
        	{
        		$ticket=$em->getRepository("AcmtoolAppBundle:Ticket")->findOneByDiplayId($json->{"ticket_id"});
                $user=$this->get("security.context")->getToken()->getUser();
                $project=$em->getRepository("AcmtoolAppBundle:Project")->getProjectByLoggedUser($user,$ticket->getProject()->getDisplayId());
        		if($ticket && $project)
        		{
        			$task=new Task();
        			$task->setTitle($json->{"title"});
        			$task->setDescription($json->{"description"});
                    $task->setType($json->{'type'});
                    if($json->{'type'}==TaskTypes::BUG["type"])
                        $task->setIsAccepted(false);
                    else
                         $task->setIsAccepted(true);
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
                    $assigned=null;
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
        			$em->persist($task);
	                $em->flush();
                    if($assigned)
                    {
                        $project=$ticket->getProject();
                        $this->get("acmtool_app.email.notifier")->notifyAssignedToStory($assigned->getEmail(),$project->getName(),$assigned->getName(),$assigned->getSurname(),$task->getTitle());
                    }
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
        	if(isset($json->{"type"}) && isset($json->{'title'}) && isset($json->{"assignedTo"})  && isset($json->{"description"}) && isset($json->{"task_id"}))
        	{
        		$task=$em->getRepository("AcmtoolAppBundle:Task")->findOneById($json->{"task_id"});
        		if($task)
        		{
                    $oldassigned=null;
                    if($task->getDeveloper()!=null){
                        $oldassigned=$task->getDeveloper();
                        $task->setDeveloper(null);
                    }
                    if($task->getDesigner()!=null){
                        $oldassigned=$task->getDesigner();
                        $task->setDesigner(null);
                    }
                    if($task->getTester()!=null){
                        $oldassigned=$task->getTester();
                        $task->setTester(null);
                    }
                    if($task->getSysadmin()!=null){
                        $oldassigned=$task->getSysadmin();
                        $task->setSysadmin(null);
                    }
                    $task->setType($json->{'type'});
                    if($json->{'type'}==TaskTypes::$BUG["type"])
                        $task->setIsAccepted(false);
                    else
                         $task->setIsAccepted(true);
        			$task->setTitle($json->{"title"});
        			$task->setDescription($json->{"description"});
                    $task->setIsFe($json->{"frontend"});
                    $task->setIsBe($json->{"backend"});
        			$developerrole=Roles::Developer();
			        $testerrole=Roles::Tester();
			        $designerrole=Roles::Designer();
			        $sysadminrole=Roles::SysAdmin();
                    $assigned=null;
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
                    if($assigned)
                    {
                        if($oldassigned->getEmail()!=$assigned->getEmail()){
                            $ticket=$task->getTicket();
                            $project=$ticket->getProject();
                            $this->get("acmtool_app.email.notifier")->notifyAssignedToStory($assigned->getEmail(),$project->getName(),$assigned->getName(),$assigned->getSurname(),$task->getTitle());
                        }
                    }
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
		$ticket=$em->getRepository("AcmtoolAppBundle:Ticket")->findOneByDiplayId($ticket_id);
        $user=$this->get("security.context")->getToken()->getUser();
        $project=$em->getRepository("AcmtoolAppBundle:Project")->getProjectByLoggedUser($user,$ticket->getProject()->getDisplayId());
		if($ticket && $project)
		{
			$i=0;
			$developerrole=Roles::Developer();
	        $testerrole=Roles::Tester();
	        $designerrole=Roles::Designer();
	        $sysadminrole=Roles::SysAdmin();
            $mess=array("id"=>$ticket->getDiplayId(),"displayId"=>$ticket->getDiplayId(),
                    "title"=>$ticket->getTitle(),"estimation"=>$ticket->getEstimation(),
                    "status"=>$ticket->getStatus(),"type"=>$ticket->getType(),"description"=>$ticket->getDescription(),"createdby"=>$ticket->getCreatedBy(),"creationdate"=>date_format($ticket->getCreationdate(), 'Y-m-d'),"realtime"=>$ticket->getRealtime());
            $mess["tasks"]=array();
            $mess["finishedtasks"]=0;
            $mess["taskscount"]=count($ticket->getTasks());
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
                if($key->getIsFe()!=null)
                {
                    $data["frontend"]=$key->getIsFe();
                }
                if($key->getIsBe()!=null)
                {
                    $data["backend"]=$key->getIsBe();
                }
                if($key->getType()==null)
                    $data["type"]=TaskTypes::$BACKEND['type'];
                else
                    $data["type"]=$key->getType();
                if($key->getIsAccepted()===null)
                    $data["accepted"]=true;
                else
                    $data["accepted"]=$key->getIsAccepted();

				$mess["tasks"][$i]=$data;
				$i++;
                if($key->getIsFinished())
                    $mess["finishedtasks"]+=1;
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
                $user=$this->get("security.context")->getToken()->getUser();
                $project=$em->getRepository("AcmtoolAppBundle:Project")->getProjectByLoggedUser($user,$task->getTicket()->getProject()->getDisplayId());
        		if($task && $project){
	        		$task->setEstimation(floatval($json->{"estimation"}));
	        		$task->setEstimateddate(new \DateTime("UTC"));
	        		$em->flush();
                    $name=$this->get('security.context')->getToken()->getUser()->getName();
                    $surname=$this->get('security.context')->getToken()->getUser()->getSurname();
                    $project_name=$project->getName();
                    if($project->getTeamleader())
                        $this->get("acmtool_app.email.notifier")->notifyStoryEstimated($project->getTeamleader()->getLogin(),$project->getName(),$task->getTitle(),$name,$surname,$json->{"estimation"});
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
                $user=$this->get("security.context")->getToken()->getUser();
                $project=$em->getRepository("AcmtoolAppBundle:Project")->getProjectByLoggedUser($user,$task->getTicket()->getProject()->getDisplayId());
        		if($task && $project){
        			$total=0;
        			foreach ($task->getRealtimes() as $key ) {
        				$total+=$key->getTime();
        			}
	        		$task->setRealtime(floatval($total));
	        		$task->setRtsetdate(new \DateTime("UTC"));
	        		$em->flush();
                    $name=$this->get('security.context')->getToken()->getUser()->getName();
                    $surname=$this->get('security.context')->getToken()->getUser()->getSurname();
                    $project_name=$project->getName();
                    if($project->getTeamleader())
                        $this->get("acmtool_app.email.notifier")->notifyStoryRealtime($project->getTeamleader()->getLogin(),$project->getName(),$task->getTitle(),$name,$surname,$total);
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
                $user=$this->get("security.context")->getToken()->getUser();
                $project=$em->getRepository("AcmtoolAppBundle:Project")->getProjectByLoggedUser($user,$task->getTicket()->getProject()->getDisplayId());
        		if($task && $project){
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
        $user=$this->get("security.context")->getToken()->getUser();
        $project=$em->getRepository("AcmtoolAppBundle:Project")->getProjectByLoggedUser($user,$realtime->getTask()->getTicket()->getProject()->getDisplayId());
		if($realtime && $project)
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
                $user=$this->get("security.context")->getToken()->getUser();
                $project=$em->getRepository("AcmtoolAppBundle:Project")->getProjectByLoggedUser($user,$realtime->getTask()->getTicket()->getProject()->getDisplayId());
        		if($realtime && $project){
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
        $user=$this->get("security.context")->getToken()->getUser();
        $project=$em->getRepository("AcmtoolAppBundle:Project")->getProjectByLoggedUser($user,$task->getTicket()->getProject()->getDisplayId());
		if($task && $project)
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
        $user=$this->get("security.context")->getToken()->getUser();
        $project=$em->getRepository("AcmtoolAppBundle:Project")->getProjectByLoggedUser($user,$task->getTicket()->getProject()->getDisplayId());
		if($task && $project)
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
        $user=$this->get("security.context")->getToken()->getUser();
        $project=$em->getRepository("AcmtoolAppBundle:Project")->getProjectByLoggedUser($user,$task->getTicket()->getProject()->getDisplayId());
		if($task && $project)
		{
			$task->setisFinished(true);
			$task->setFinishdate(new \DateTime("UTC"));
			$ticket=$task->getTicket();
			$done=true;
            $project=$ticket->getProject();
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
                $company_name=$project->getOwner()->getCompanyname();
                $emails=array();
                array_push($emails, $project->getKeyaccount()->getEmail());
                //Todo: add client notification
                $this->get("acmtool_app.email.notifier")->notifyTicketinQA($emails,$project->getName(),$ticket->getTitle(),$company_name);
			}
			$mess=array("done"=>$done);
			$em->flush();
            $name=$this->get('security.context')->getToken()->getUser()->getName();
            $surname=$this->get('security.context')->getToken()->getUser()->getSurname();
            $project_name=$task->getTicket()->getProject()->getName();
            
            if($project->getTeamleader())
                $this->get("acmtool_app.email.notifier")->notifyStoryDone($project->getTeamleader()->getLogin(),$project->getName(),$task->getTitle(),$name,$surname);
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
        $user=$this->get("security.context")->getToken()->getUser();
        $project=$em->getRepository("AcmtoolAppBundle:Project")->getProjectByLoggedUser($user,$task->getTicket()->getProject()->getDisplayId());
		if($task && $project)
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
    public function acceptTaskAction()
    {
        $request = $this->get('request');
        $message = $request->getContent();
        $em = $this->getDoctrine()->getManager();
        $result = $this->get('acmtool_app.validation.json')->validate($message);
        if(!$result["valid"])
            return $result['response'];
        else
        {
            if(isset($json->{"task_id"}) && isset($json->{"accept"}))
            {
                $task=$em->getRepository("AcmtoolAppBundle:Task")->findOneById($json->{"task_id"});
                $user=$this->get("security.context")->getToken()->getUser();
                $project=$em->getRepository("AcmtoolAppBundle:Project")->getProjectByLoggedUser($user,$task->getTicket()->getProject()->getDisplayId());
                $haveaccess=false;
                if($this->get('security.context')->isGranted("ROLE_ADMIN"))
                    $haveaccess=true;
                else
                    if($project->getTeamleader()->getId()==$user->getCreds()->getId())
                        $haveaccess=true;
                    else
                        $haveaccess=false;
                if($task && $project && $haveaccess)
                {
                   if($json->{"accept"})
                   {
                        $task->setIsAccepted(true);
                        $em->flush();
                        $response=new Response('Task accepted',200);
                        return $response;
                   }
                   else
                   {
                        if(isset($json->{"reason"}))
                        {
                            $ticket=new Ticket();
                            $ticket->setProject($project);
                            $ticket->setDescription("flexwork comment:\n".$json->{"reason"}."\n".$task->getDescription());
                            $ticket->setTitle($Task->getTitle());
                            
                            $ticket->setStatus(TicketStatus::DRAFT);
                            $ticket->setCreatedBy($user->getName()." ".$user->getSurname());
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
                            $response=new Response('Task converted to ticket',200);
                            return $response;
                        }
                   }
               }

            }
            $response=new Response('{"error":"'.ConstValues::INVALIDREQUEST.'"}',400);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
    }
    private function getTaskTypesAction()
    {
        $mess= TaskTypes::serialize();
        new Response(json_encode($mess),200);
    }
	private function ifToday($date)
	{
		$today=new \DateTime("UTC");
        if($date->format("d")==$today->format("d") && $date->format("m")==$today->format("m") && $date->format("Y")==$today->format("Y"))
            return true;
        else
            return false;

	}
    private function weekOfMonth($date) {
        //Get the first day of the month.
        $d = new \DateTime('first day of this month');
        //Apply above formula.
        $week=intval($date->format("W")) - intval($d->format("W"));
        return $week+1;
    }
}
