<?php
namespace Acmtool\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Httpfoundation\Response;
use Acmtool\AppBundle\Entity\Customer;
use Acmtool\AppBundle\Entity\Developer;
use Acmtool\AppBundle\Entity\Tester;
use Acmtool\AppBundle\Entity\Designer;
use Acmtool\AppBundle\Entity\SystemAdmin;
use Acmtool\AppBundle\Entity\TeamLeader;
use Acmtool\AppBundle\Entity\KeyAccount;
use Acmtool\AppBundle\Entity\Creds;
use Acmtool\AppBundle\Entity\Titles;
use Acmtool\AppBundle\Entity\ConstValues;
use Acmtool\AppBundle\Entity\Project;
use Acmtool\AppBundle\Entity\ProjectStates;
use Acmtool\AppBundle\Entity\Roles;
use Acmtool\AppBundle\Entity\TicketStatus;
class ProjectController extends Controller
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
        	$KeyAccount=null;
        	$customer=null;
         	if($this->get('security.context')->isGranted("ROLE_ADMIN") || $this->get("security.context")->isGranted("ROLE_KEYACCOUNT"))
         	{
         		if(!isset($json->{"customer_id"}))
         		{
         			$response=new Response('{"err":"'.ConstValues::INVALIDREQUEST.'"}',400);
            		$response->headers->set('Content-Type', 'application/json');
            		return $response;
         		}
         		else
         		{
         			$customer=$em->getRepository("AcmtoolAppBundle:Customer")->findOneById($json->{"customer_id"});
         		}
         	}
         	else
         	{
         		$customer=$this->get("security.context")->getToken()->getUser();
         	}
         	if(!(isset($json->{'name'})))
            {
                $response=new Response('{"err":"name '.ConstValues::INVALIDREQUEST.'"}',400);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
            $project=new Project();
            $project->setState(ProjectStates::TLASSIGN);
            $project->setOwner($customer);
            $project->setKeyaccount($customer->getKeyAccount());
            $project->setName($json->{'name'});
            if(isset($json->{"skills"}))
            {
                $project->setProjectSkills($json->{"skills"});
            }
            if(isset($json->{"description"}))
            	$project->setDescription($json->{"description"});
            if(isset($json->{'startingdate'}))
            {
            	if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$json->{'startingdate'}))
            	{
            		$format = 'Y-m-d';
					$startingdate = new \DateTime($json->{'startingdate'});
					$project->setStartingdate($startingdate);
            		
            	}
            	else{
            		$response=new Response('{"err":"'.ConstValues::INVALIDDATE.'"}',400);
                	$response->headers->set('Content-Type', 'application/json');
                	return $response;
            	}
            }
            else
            {
                $format = 'Y-m-d';
                $startingdate = new \DateTime('UTC');
                $project->setStartingdate($startingdate);
            }
           
	    	if(isset($json->{"teamleader_id"}))
	    	{
	    		$project->setTeamleader($em->getRepository("AcmtoolAppBundle:TeamLeader")->findOneById($json->{"teamleader_id"}));
	    	}
	    	if(isset($json->{"developers"}))
	    	{
	    		foreach ($json->{"developers"} as $dev) {
                    $member=$em->getRepository("AcmtoolAppBundle:Developer")->findOneById($dev->{"id"});
	    			$project->addDeveloper($member);
                    $member->addProject($project);
	    		}
            }
            if(isset($json->{"testers"}))
            {
                foreach ($json->{"testers"} as $dev) {
                    $member=$em->getRepository("AcmtoolAppBundle:Tester")->findOneById($dev->{"id"});
                    $project->addTester($member);
                    $member->addProject($project);
                }
            }
            if(isset($json->{"designers"}))
            {
                foreach ($json->{"designers"} as $dev) {
                    $member=$em->getRepository("AcmtoolAppBundle:Designer")->findOneById($dev->{"id"});
                    $project->addDesigner($member);
                    $member->addProject($project);
                }
            }
            if(isset($json->{"sysadmins"}))
            {
                foreach ($json->{"sysadmins"} as $dev) {
                    $member=$em->getRepository("AcmtoolAppBundle:SystemAdmin")->findOneById($dev->{"id"});
                    $project->addSysAdmin($member);
                    $member->addProject($project);
                }
            }
            
            $chatservice=$this->get("acmtool_app.messaging");
            $chatprovider=$chatservice->CreateChatProvider();
            $em->persist($project);
            $em->flush();
            $result=$chatprovider->createGroupForProject(preg_replace('/\s+/', '_', $project->getName()));
            if($result["result"])
            {
                $project->setChannelid($result["id"]);
                $em->persist($project);
                $em->flush();
                $response=new Response(ConstValues::PROJECTCREATED,200);
                return $response;

            }
            else
            {
                $response=new Response($result["reason"],400);
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
            $project=null;
            if(!isset($json->{"project_id"}))
            {
                $response=new Response('{"err":"'.ConstValues::INVALIDREQUEST.'"}',400);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
            else
            {
                $project=$em->getRepository("AcmtoolAppBundle:Project")->findOneById($json->{"project_id"});
                if((isset($json->{'name'})))
                {
                    $project->setName($json->{'name'});
                }
                if(isset($json->{"description"}))
                {
                    $project->setDescription($json->{"description"});
                }
                if(isset($json->{'startingdate'}))
                {
                    if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$json->{'startingdate'}))
                    {
                        $format = 'Y-m-d';
                        $startingdate = new \DateTime($json->{'startingdate'});
                        $project->setStartingdate($startingdate);
                        
                    }
                    else{
                        $response=new Response('{"err":"'.ConstValues::INVALIDDATE.'"}',400);
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;
                    }
                }
                if(isset($json->{"skills"}))
                {
                    $project->setProjectSkills($json->{"skills"});
                }
                $em->flush();
                $res=new Response();
                $res->setStatusCode(200);
                $res->setContent(ConstValues::PROJECTUPDATED);
                return $res;
            }
        }
              
    }
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $project=$em->getRepository("AcmtoolAppBundle:Project")->findOneById($id);
        if($project){
            $em->remove($project);
            $em->flush();
            $res=new Response();
            $res->setStatusCode(200);
            $res->setContent(ConstValues::PROJECTDELETED);
            return $res;
        }
        else
        {
            $response=new Response('{"err":"'.ConstValues::INVALIDREQUEST.'"}',400);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
    }
    public function listAction($page,$state)
    {
        $em = $this->getDoctrine()->getManager();
        $result=null;

        $totalpages=null;
        if($this->get('security.context')->isGranted("ROLE_ADMIN"))
        {
            if($state==ProjectStates::ALL)
            {
                $totalpages=ceil($em->createQuery("SELECT COUNT(p) FROM AcmtoolAppBundle:Project p")
                ->getSingleScalarResult()/ConstValues::COUNT);
                $start=ConstValues::COUNT*($page-1);
                $result=$em->createQuery('select p from AcmtoolAppBundle:Project p')
                            ->setMaxResults(ConstValues::COUNT)
                            ->setFirstResult($start)
                            ->getResult();
            }
            else
            {
                $totalpages=ceil($em->createQuery("SELECT COUNT(p) FROM AcmtoolAppBundle:Project p WHERE p.state= :state")
                ->setParameter("state",$state)
                ->getSingleScalarResult()/ConstValues::COUNT);
                $start=ConstValues::COUNT*($page-1);
                $result=$em->createQuery('select p from AcmtoolAppBundle:Project p WHERE p.state= :state')
                            ->setParameter("state",$state)
                            ->setMaxResults(ConstValues::COUNT)
                            ->setFirstResult($start)
                            ->getResult();
            }
        }
        elseif ($this->get('security.context')->isGranted("ROLE_KEYACCOUNT")) {
            $keyaccount=$this->get("security.context")->getToken()->getUser();
            $totalpages=ceil($em->getRepository("AcmtoolAppBundle:Project")->getProjectsCountbyKeyAccount($keyaccount,$state)/ConstValues::COUNT);
            $start=ConstValues::COUNT*($page-1);
            $result=$em->getRepository("AcmtoolAppBundle:Project")->getProjectsByKeyAccount($keyaccount,$start,$state);
        }
        elseif($this->get('security.context')->isGranted("ROLE_CUSTOMER")) {
            $customer=$this->get("security.context")->getToken()->getUser();
            $totalpages=ceil($em->getRepository("AcmtoolAppBundle:Project")->getProjectCountByCustomer($customer,$state)/ConstValues::COUNT);
            $start=ConstValues::COUNT*($page-1);
            $result=$em->getRepository("AcmtoolAppBundle:Project")->getProjectsByCustomer($customer,$start,$state);
        }
        elseif ($this->get('security.context')->isGranted("ROLE_TEAMLEADER")) {
            $result=$this->get("security.context")->getToken()->getUser()->getProjects();
        }
        elseif($this->get('security.context')->isGranted("ROLE_CUSER"))
        {
            $customer=$this->get("security.context")->getToken()->getUser()->getCompany();
            $totalpages=ceil($em->getRepository("AcmtoolAppBundle:Project")->getProjectCountByCustomer($customer,$state)/ConstValues::COUNT);
            $start=ConstValues::COUNT*($page-1);
            $result=$em->getRepository("AcmtoolAppBundle:Project")->getProjectsByCustomer($customer,$start,$state);
        }
        elseif($this->get('security.context')->isGranted("ROLE_DEVELOPER")){
            $user_id=$this->get("security.context")->getToken()->getUser()->getId();
            $repository = $em->getRepository('AcmtoolAppBundle:Project');
            $result = $repository->createQueryBuilder('p')
                ->innerJoin('p.developers', 'd')
                ->where('d.id = :developer_id')
                ->setParameter('developer_id', $user_id)
                ->getQuery()->getResult();
        }
        elseif($this->get('security.context')->isGranted("ROLE_DESIGNER")){
            $user_id=$this->get("security.context")->getToken()->getUser()->getId();
            $repository = $em->getRepository('AcmtoolAppBundle:Project');
            $result = $repository->createQueryBuilder('p')
                ->innerJoin('p.designers', 'd')
                ->where('d.id = :designer_id')
                ->setParameter('designer_id', $user_id)
                ->getQuery()->getResult();
        }
        elseif($this->get('security.context')->isGranted("ROLE_TESTER")){
            $user_id=$this->get("security.context")->getToken()->getUser()->getId();
            $repository = $em->getRepository('AcmtoolAppBundle:Project');
            $result = $repository->createQueryBuilder('p')
                ->innerJoin('p.testers', 'd')
                ->where('d.id = :tester_id')
                ->setParameter('tester_id', $user_id)
                ->getQuery()->getResult();
        }
        elseif($this->get('security.context')->isGranted("ROLE_SYSADMIN")){
            $user_id=$this->get("security.context")->getToken()->getUser()->getId();
            $repository = $em->getRepository('AcmtoolAppBundle:Project');
            $result = $repository->createQueryBuilder('p')
                ->innerJoin('p.sysadmins', 'd')
                ->where('d.id = :sysadmin_id')
                ->setParameter('sysadmin_id', $user_id)
                ->getQuery()->getResult();
        }
        else
        {
            $response=new Response(403);
            return $response;
        }
        if($result)
        {
            $mess=array();
            //$mess['totalpages']=$totalpages;
            $projects=array();
            $channels=array();
            $i=0;
            $j=0;

            foreach ($result as $key) {
                $projects[$i]=array("id"=>$key->getId(),"name"=>$key->getName(),"company"=>$key->getOwner()->getCompanyname());
                if($key->getChannelid()!=null){
                    $channels[$j]=array("id"=>$key->getChannelid(),"name"=>$key->getName(),"project_id"=>$key->getId());
                    $j++;
                }
                $i++;
            }
            $mess["current_page"]=$page;
            $mess["projects"]=$projects;
            $mess["channels"]=$channels;
            $res=new Response();
            $res->setStatusCode(200);
            $res->setContent(json_encode($mess));
            return $res;


        }
        else
        {
            $mess=array();
            $mess["current_page"]=1;
            $mess["projects"]=array();
            $mess["channels"]=array();
            $res=new Response();
            $res->setStatusCode(200);
            $res->setContent(json_encode($mess));
            return $res;
        }
    }
    public function acceptContractAction($project_id)
    {
        $em = $this->getDoctrine()->getManager();
        $project=$em->getRepository("AcmtoolAppBundle:Project")->findOneById($project_id);
        if($project)
        {
            $project->setSignedContract(true);
            $format = 'Y-m-d';
            $startingdate = new \DateTime('UTC');
            $project->setSignaturedate($startingdate);
            $em->flush();
            $res=new Response();
            $res->setStatusCode(200);
            $res->setContent("Contract signed");
            return $res;
        }
        else
        {
            $response=new Response('{"error":"'.ConstValues::INVALIDREQUEST.'"}',400);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
    }
    public function detailsAction($id)
    {
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $project=$em->getRepository("AcmtoolAppBundle:Project")->findOneById($id);
        if($project)
        {
            $mess=array("id"=>$project->getId(),"name"=>$project->getName(),"description"=>$project->getDescription(),"customer"=>$project->getOwner()->getCompanyname(),"state"=>$project->getState(),"skills"=>$project->getProjectSkills(),"budget"=>$project->getBudget(),"channel_id"=>$project->getChannelid(),"signed"=>$project->getSignedContract());
            $user=$project->getOwner();
            $mess["client"]=array('id'=>$user->getId(),'username' =>$user->getUsername(),'email'=>$user->getEmail(),'logo'=>$user->getLogo(),"name"=>$user->getName(),"surname"=>$user->getSurname(),"logo"=>$user->getLogo(),"companyname"=>$user->getCompanyName(),"vat"=>$user->getVat(),"tel"=>$user->getTelnumber(),"address"=>array("address"=>$user->getAddress()->getAddress(),"zipcode"=>$user->getAddress()->getZipcode(),"city"=>$user->getAddress()->getCity(),"country"=>$user->getAddress()->getCountry(),"state"=>$user->getAddress()->getState()),"keyaccount"=>array('id'=>$user->getKeyaccount()->getId(),"name"=>$user->getKeyaccount()->getName(),"surname"=>$user->getKeyaccount()->getSurname()));
            $mess["keyaccount"]=array("id"=>$project->getKeyAccount()->getId(),"surname"=>$project->getKeyAccount()->getSurname(),"name"=>$project->getKeyAccount()->getName(),"email"=>$project->getKeyAccount()->getEmail(),"photo"=>$project->getKeyAccount()->getPhoto());
            $i=0;
            $team=array();
            $Teamleader=null;
            if($project->getTeamleader())
            {
                $user=$project->getTeamleader();
                $role=$user->getTitle();
                
                $developerrole=Roles::Developer();
                $testerrole=Roles::Tester();
                $designerrole=Roles::Designer();
                $sysadminrole=Roles::SysAdmin();
                if ($role==$developerrole['role']) {
                    $Teamleader=$em->getRepository("AcmtoolAppBundle:Developer")->findOneByCreds($project->getTeamleader());
                }
                elseif ($role==$testerrole["role"]) {
                    $Teamleader=$em->getRepository("AcmtoolAppBundle:Tester")->findOneByCreds($project->getTeamleader());
                }
                elseif ($role==$designerrole["role"]) {
                    $Teamleader=$em->getRepository("AcmtoolAppBundle:Designer")->findOneByCreds($project->getTeamleader());
                }
                elseif ($role==$sysadminrole["role"]) {
                    $Teamleader=$em->getRepository("AcmtoolAppBundle:SystemAdmin")->findOneByCreds($project->getTeamleader());
                }
                $mess["teamLeader"]=array("id"=>$Teamleader->getId(),"surname"=>$Teamleader->getSurname(),"name"=>$Teamleader->getName(),"email"=>$Teamleader->getEmail(),"photo"=>$Teamleader->getPhoto());
                
            }
            foreach ($project->getDevelopers() as $key) {
                $team[$i]=array("id"=>$key->getId(),"surname"=>$key->getSurname(),"name"=>$key->getName(),"email"=>$key->getEmail(),"photo"=>$key->getPhoto(),"role"=>Roles::Developer(),"order"=>$i);
                if($Teamleader!=null)
                    if($key->getId()==$Teamleader->getId())
                        $team[$i]["isTeamLeader"]=true;
                    else
                        $team[$i]["isTeamLeader"]=false;
                else
                    $team[$i]["isTeamLeader"]=false;
                $i++;
            }           
            
            foreach ($project->getTesters() as $key) {
                $team[$i]=array("id"=>$key->getId(),"surname"=>$key->getSurname(),"name"=>$key->getName(),"email"=>$key->getEmail(),"photo"=>$key->getPhoto(),"role"=>Roles::Tester(),"order"=>$i);
                 if($Teamleader!=null)
                    if($key->getId()==$Teamleader->getId())
                        $team[$i]["isTeamLeader"]=true;
                    else
                        $team[$i]["isTeamLeader"]=false;
                else
                    $team[$i]["isTeamLeader"]=false;
                $i++;
            }  
           
            foreach ($project->getDesigners() as $key) {
                $team[$i]=array("id"=>$key->getId(),"surname"=>$key->getSurname(),"name"=>$key->getName(),"email"=>$key->getEmail(),"photo"=>$key->getPhoto(),"role"=>ROLES::Designer(),"order"=>$i);
                 if($Teamleader!=null)
                    if($key->getId()==$Teamleader->getId())
                        $team[$i]["isTeamLeader"]=true;
                    else
                        $team[$i]["isTeamLeader"]=false;
                else
                    $team[$i]["isTeamLeader"]=false;
                $i++;
            }  

            foreach ($project->getSysadmins() as $key) {
                $team[$i]=array("id"=>$key->getId(),"surname"=>$key->getSurname(),"name"=>$key->getName(),"email"=>$key->getEmail(),"photo"=>$key->getPhoto(),"role"=>Roles::SysAdmin(),"order"=>$i);
                if($Teamleader!=null)
                    if($key->getId()==$Teamleader->getId())
                        $team[$i]["isTeamLeader"]=true;
                    else
                        $team[$i]["isTeamLeader"]=false;
                else
                    $team[$i]["isTeamLeader"]=false;
                $i++;
            }  
            $mess["team"]=$team;
            $h=0;
            $dosc=array();
            $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
            foreach ($project->getDocuments() as $key) {
                $data=array("id"=>$key->getId(),"name"=>$key->getName(),"link"=>$baseurl.$key->getPath());
                $dosc[$h]=$data;
                $h++;
            }
            $mess['docs']=$dosc;
            $tickets=array();
            $i=0;
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
                    $assignedto=null;
                    if($task->getDeveloper()!=null)
                        $assignedto=array("id"=>$task->getDeveloper()->getId(),"name"=>$task->getDeveloper()->getName(),"surname"=>$task->getDeveloper()->getSurname(),"role"=>array("role"=>$developerrole["role"]));
                    elseif($task->getDesigner()!=null)
                        $assignedto=array("id"=>$task->getDesigner()->getId(),"name"=>$task->getDesigner()->getName(),"surname"=>$task->getDesigner()->getSurname(),"role"=>array("role"=>$designerrole["role"]));
                    elseif($task->getTester()!=null)
                        $assignedto=array("id"=>$task->getTester()->getId(),"name"=>$task->getTester()->getName(),"surname"=>$task->getTester()->getSurname(),"role"=>array("role"=>$testerrole["role"]));
                    elseif($task->getSysadmin()!=null)
                        $assignedto=array("id"=>$task->getSysadmin()->getId(),"name"=>$task->getSysadmin()->getName(),"surname"=>$task->getSysadmin()->getSurname(),"role"=>array("role"=>$sysadminrole["role"]));
                    if($assignedto!=null)
                        $data["assignto"]=$assignedto;
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
            $mess["tickets"]=$tickets;
            $configs=array();
            $i=0;
            foreach ($project->getConfigs() as $key ) {
                $configs[$i]=array("id"=>$key->getId(),"title"=>$key->getTitle(),"config"=>$key->getConfig());
                $i++;
            }
            $mess["configs"]=$configs;           
            $res=new Response();
            $res->setStatusCode(200);
            $res->setContent(json_encode($mess));
            $res->headers->set('Content-Type', 'application/json');
            return $res;

        }
        else
        {
            $response=new Response('{"err":"'.ConstValues::INVALIDREQUEST.'"}',400);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
    }
    public function assignTeamLeaderAction()
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
            if(!isset($json->{"project_id"}) || !isset($json->{"teamleader_id"}) || !isset($json->{"teamleader_role"}))
            {
                $response=new Response('{"err":"'.ConstValues::INVALIDREQUEST.'"}',400);
                $response->headers->set('Content-Type', 'application/json');
                return $response;       
            }
            else
            {
                $project=$em->getRepository("AcmtoolAppBundle:Project")->findOneById($json->{"project_id"});
                $isadmin=$this->get('security.context')->isGranted("ROLE_ADMIN");
                if($project->getTeamleader())
                    $isTeamLeader=($project->getTeamleader()->getId()==$this->get('security.context')->getToken()->getUser()->getCredentials()->getID());
                else
                    $isTeamLeader=false;
                if($isadmin || $isTeamLeader)
                {
                   $role=$json->{"teamleader_role"};
                    $Teamleader=null;
                    $developerrole=Roles::Developer();
                    $testerrole=Roles::Tester();
                    $designerrole=Roles::Designer();
                    $sysadminrole=Roles::SysAdmin();
                    $id=$json->{"teamleader_id"};
                    if ($role==$developerrole["role"]) {
                        $Teamleader=$em->getRepository("AcmtoolAppBundle:Developer")->findOneById($id);
                    }
                    elseif ($role==$testerrole["role"]) {
                        $Teamleader=$em->getRepository("AcmtoolAppBundle:Tester")->findOneById($id);
                    }
                    elseif ($role==$designerrole["role"]) {
                        $Teamleader=$em->getRepository("AcmtoolAppBundle:Designer")->findOneById($id);
                    }
                    elseif ($role==$sysadminrole["role"]) {
                        $Teamleader=$em->getRepository("AcmtoolAppBundle:SystemAdmin")->findOneById($id);
                    }
                    if($project && $Teamleader)
                    {
                        $project->setTeamleader($Teamleader->getCredentials());
                        $project->setState(ProjectStates::TEAMASSIGN);
                        $em->flush();
                        $res=new Response();
                        $res->setStatusCode(200);
                        $res->setContent(ConstValues::TEAMLEADERASSIGNED);
                        return $res;
                    }
                    else
                    {
                        $response=new Response('{"err":"'.ConstValues::INVALIDREQUEST.'"}',400);
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;  
                    } 
                }
                else
                {
                    return new Response(403);
                }
                

            }
        }
    }
    public function addDeveloperAction()
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
            if(!isset($json->{"project_id"}) || !isset($json->{"developers"}))
            {
                $response=new Response('{"err":"'.ConstValues::INVALIDREQUEST.'"}',400);
                $response->headers->set('Content-Type', 'application/json');
                return $response;       
            }
            else
            {
                $project=$em->getRepository("AcmtoolAppBundle:Project")->findOneById($json->{"project_id"});
                $isadmin=$this->get('security.context')->isGranted("ROLE_ADMIN");
                if($project->getTeamleader())
                    $isTeamLeader=($project->getTeamleader()->getId()==$this->get('security.context')->getToken()->getUser()->getCredentials()->getID());
                else
                    $isTeamLeader=false;
                if($isadmin || $isTeamLeader)
                {
                    if($project)
                    {
                        foreach ($json->{"developers"} as $key) {
                           $member=$em->getRepository("AcmtoolAppBundle:Developer")->findOneById($key);
                           if($member)
                           {
                                $project->addDeveloper($member);
                                $member->addProject($project);
                           }
                        }
                        
                        $em->flush();
                        $res=new Response();
                        $res->setStatusCode(200);
                        $res->setContent(ConstValues::MEMBERADDED);
                        return $res;
                    }
                    else
                    {    
                        $response=new Response('{"err":"'.ConstValues::INVALIDREQUEST.'"}',400);
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;  
                    }
                }
                else
                    return new Response(403);
                
            }

        }
    }
    public function addDesignerAction()
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
            if(!isset($json->{"project_id"}) || !isset($json->{"designers"}))
            {
                $response=new Response('{"err":"'.ConstValues::INVALIDREQUEST.'"}',400);
                $response->headers->set('Content-Type', 'application/json');
                return $response;       
            }
            else
            {
                $project=$em->getRepository("AcmtoolAppBundle:Project")->findOneById($json->{"project_id"});
                $isadmin=$this->get('security.context')->isGranted("ROLE_ADMIN");
                $isTeamLeader=($project->getTeamleader()->getId()==$this->get('security.context')->getToken()->getUser()->getCredentials()->getID());
                if($isadmin || $isTeamLeader)
                {
                    if($project)
                    {
                        foreach ($json->{"designers"} as $key) {
                           $member=$em->getRepository("AcmtoolAppBundle:Designer")->findOneById($key);
                           if($member)
                           {
                                $project->addDesigner($member);
                                $member->addProject($project);
                           }
                        }
                        
                        $em->flush();
                        $res=new Response();
                        $res->setStatusCode(200);
                        $res->setContent(ConstValues::MEMBERADDED);
                        return $res;
                    }
                    else
                    {    
                        $response=new Response('{"err":"'.ConstValues::INVALIDREQUEST.'"}',400);
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;  
                    }
                }
                else
                    return new Response(403);
                

            }

        }
    }
    public function addTesterAction()
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
            if(!isset($json->{"project_id"}) || !isset($json->{"testers"}))
            {
                $response=new Response('{"err":"'.ConstValues::INVALIDREQUEST.'"}',400);
                $response->headers->set('Content-Type', 'application/json');
                return $response;       
            }
            else
            {
                $project=$em->getRepository("AcmtoolAppBundle:Project")->findOneById($json->{"project_id"});
                $isadmin=$this->get('security.context')->isGranted("ROLE_ADMIN");
                $isTeamLeader=($project->getTeamleader()->getId()==$this->get('security.context')->getToken()->getUser()->getCredentials()->getID());
                if($isadmin || $isTeamLeader)
                {
                    if($project)
                    {
                        foreach ($json->{"testers"} as $key) {
                           $member=$em->getRepository("AcmtoolAppBundle:Tester")->findOneById($key);
                           if($member)
                           {
                                $project->addTester($member);
                                $member->addProject($project);
                           }
                        }
                        
                        $em->flush();
                        $res=new Response();
                        $res->setStatusCode(200);
                        $res->setContent(ConstValues::MEMBERADDED);
                        return $res;
                    }
                    else
                    {    
                        $response=new Response('{"err":"'.ConstValues::INVALIDREQUEST.'"}',400);
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;  
                    }
                }
                else
                    return new Response(403);
                
            }

        }
    }
    public function addSysadminAction()
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
            if(!isset($json->{"project_id"}) || !isset($json->{"sysadmins"}))
            {
                $response=new Response('{"err":"'.ConstValues::INVALIDREQUEST.'"}',400);
                $response->headers->set('Content-Type', 'application/json');
                return $response;       
            }
            else
            {
                $project=$em->getRepository("AcmtoolAppBundle:Project")->findOneById($json->{"project_id"});
                $isadmin=$this->get('security.context')->isGranted("ROLE_ADMIN");
                $isTeamLeader=($project->getTeamleader()->getId()==$this->get('security.context')->getToken()->getUser()->getCredentials()->getID());
                if($isadmin || $isTeamLeader)
                {
                    if($project)
                    {
                        foreach ($json->{"sysadmins"} as $key) {
                           $member=$em->getRepository("AcmtoolAppBundle:SystemAdmin")->findOneById($key);
                           if($member)
                           {
                                $project->addSysAdmin($member);
                                $member->addProject($project);
                           }
                        }
                        
                        $em->flush();
                        $res=new Response();
                        $res->setStatusCode(200);
                        $res->setContent(ConstValues::MEMBERADDED);
                        return $res;
                    }
                    else
                    {    
                        $response=new Response('{"err":"'.ConstValues::INVALIDREQUEST.'"}',400);
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;  
                    }
                }
                else
                    return new Response(403);
                
            }

        }
    }
    public function deleteDeveloperAction()
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
            if(!isset($json->{"project_id"}) || !isset($json->{"developers"}))
            {
                $response=new Response('{"err":"'.ConstValues::INVALIDREQUEST.'"}',400);
                $response->headers->set('Content-Type', 'application/json');
                return $response;       
            }
            else
            {
                $project=$em->getRepository("AcmtoolAppBundle:Project")->findOneById($json->{"project_id"});
                if($project)
                {
                    $isadmin=$this->get('security.context')->isGranted("ROLE_ADMIN");
                    $isTeamLeader=($project->getTeamleader()->getId()==$this->get('security.context')->getToken()->getUser()->getCredentials()->getID());
                    if($isadmin || $isTeamLeader)
                    {
                        foreach ($json->{"developers"} as $key) {
                            $member=$em->getRepository("AcmtoolAppBundle:Developer")->findOneById($key);
                           if($member)
                           {
                            $project->removeDeveloper($member);
                            $member->removeProject($project);
                           }
                        }
                        $em->flush();
                        $res=new Response();
                        $res->setStatusCode(200);
                        $res->setContent(ConstValues::MEMBERDELETED);
                        return $res;
                    }
                    else
                        return new Response(403);
                    
                }
                else
                {    
                    $response=new Response('{"err":"'.ConstValues::INVALIDREQUEST.'"}',400);
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;  
                }
            }
        }
    }
    public function assignBudgetAction()
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
            if(!isset($json->{"project_id"}) || !isset($json->{"budget"}))
            {
                $response=new Response('{"err":"'.ConstValues::INVALIDREQUEST.'"}',400);
                $response->headers->set('Content-Type', 'application/json');
                return $response;       
            }
            else
            {
                $project=$em->getRepository("AcmtoolAppBundle:Project")->findOneById($json->{"project_id"});
                if($project)
                {

                    $project->setBudget($json->{"budget"});
                    $em->flush();
                    $res=new Response();
                    $res->setStatusCode(200);
                    $res->setContent(ConstValues::MEMBERDELETED);
                    return $res;
                }
                else
                {    
                    $response=new Response('{"err":"'.ConstValues::INVALIDREQUEST.'"}',400);
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;  
                }
            }
        }
    }
    public function deleteDesignerAction()
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
            if(!isset($json->{"project_id"}) || !isset($json->{"designers"}))
            {
                $response=new Response('{"err":"'.ConstValues::INVALIDREQUEST.'"}',400);
                $response->headers->set('Content-Type', 'application/json');
                return $response;       
            }
            else
            {
                $project=$em->getRepository("AcmtoolAppBundle:Project")->findOneById($json->{"project_id"});
                if($project)
                {
                    $isadmin=$this->get('security.context')->isGranted("ROLE_ADMIN");
                    $isTeamLeader=($project->getTeamleader()->getId()==$this->get('security.context')->getToken()->getUser()->getCredentials()->getID());
                    if($isadmin || $isTeamLeader)
                    {
                        foreach ($json->{"designers"} as $key) {
                           $member=$em->getRepository("AcmtoolAppBundle:Designer")->findOneById($key);
                           if($member)
                           {
                            $project->removeDesigner($member);
                            $member->removeProject($project);
                           }
                        }
                        $em->flush();
                        $res=new Response();
                        $res->setStatusCode(200);
                        $res->setContent(ConstValues::MEMBERDELETED);
                        return $res;
                    }
                    else
                        return new Response(403);

                    
                }
                else
                {    
                    $response=new Response('{"err":"'.ConstValues::INVALIDREQUEST.'"}',400);
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;  
                }
            }
        }
    }
    public function deleteTesterAction()
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
            if(!isset($json->{"project_id"}) || !isset($json->{"testers"}))
            {
                $response=new Response('{"err":"'.ConstValues::INVALIDREQUEST.'"}',400);
                $response->headers->set('Content-Type', 'application/json');
                return $response;       
            }
            else
            {
                $project=$em->getRepository("AcmtoolAppBundle:Project")->findOneById($json->{"project_id"});
                if($project)
                {
                    $isadmin=$this->get('security.context')->isGranted("ROLE_ADMIN");
                    $isTeamLeader=($project->getTeamleader()->getId()==$this->get('security.context')->getToken()->getUser()->getCredentials()->getID());
                    if($isadmin || $isTeamLeader)
                    {
                       foreach ($json->{"testers"} as $key) {
                            $member=$em->getRepository("AcmtoolAppBundle:Tester")->findOneById($key);
                           if($member)
                           {
                            $project->removeTester($member);
                            $member->removeProject($project);
                           }
                        }
                        $em->flush();
                        $res=new Response();
                        $res->setStatusCode(200);
                        $res->setContent(ConstValues::MEMBERDELETED);
                        return $res; 
                    }
                    else
                        return new Response(403);
                    
                }
                else
                {    
                    $response=new Response('{"err":"'.ConstValues::INVALIDREQUEST.'"}',400);
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;  
                }
            }
        }
    }
    public function deleteSysadminAction()
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
            if(!isset($json->{"project_id"}) || !isset($json->{"sysadmins"}))
            {
                $response=new Response('{"err":"'.ConstValues::INVALIDREQUEST.'"}',400);
                $response->headers->set('Content-Type', 'application/json');
                return $response;       
            }
            else
            {
                $project=$em->getRepository("AcmtoolAppBundle:Project")->findOneById($json->{"project_id"});
                if($project)
                {
                    $isadmin=$this->get('security.context')->isGranted("ROLE_ADMIN");
                    $isTeamLeader=($project->getTeamleader()->getId()==$this->get('security.context')->getToken()->getUser()->getCredentials()->getID());
                    if($isadmin || $isTeamLeader)
                    {
                         foreach ($json->{"sysadmins"} as $key) {
                            $member=$em->getRepository("AcmtoolAppBundle:SystemAdmin")->findOneById($key);
                           if($member)
                           {
                            $project->removeSysadmin($member);
                            $member->removeProject($project);
                           }
                        }
                        $em->flush();
                        $res=new Response();
                        $res->setStatusCode(200);
                        $res->setContent(ConstValues::MEMBERDELETED);
                        return $res;
                    }
                    else
                        return new Response(403);

                   
                }
                else
                {    
                    $response=new Response('{"err":"'.ConstValues::INVALIDREQUEST.'"}',400);
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;  
                }
            }
        }
    }
    public function listbyClientAction($client_id)
    {
        $em =$this->getDoctrine()->getManager();
        $client=$em->getRepository("AcmtoolAppBundle:Customer")->findOneById($client_id);
        if($client)
        {
            $mess=array();
            $i=0;
            foreach ($client->getProjects() as $key) {
                $data=array("id"=>$key->getId(),"name"=>$key->getName(),"description"=>$key->getDescription(),"skills"=>$key->getProjectSkills(),"budget"=>$key->getBudget(),"signed"=>$key->getSignedContract(),"ticketcount"=>count($key->getTickets()),"creationdate"=>date_format($key->getStartingdate(), 'Y-m-d'),"rate"=>$key->getRate());
                if($key->getSignedContract())
                    $data["signaturedate"]=date_format($key->getSignaturedate(),"Y-m-d");
                $j=0;
                $team=array();

                foreach ($key->getDevelopers() as $member) {
                    $memberdata=array("id"=>$member->getId(),"surname"=>$member->getSurname(),"name"=>$member->getName(),"email"=>$member->getEmail(),"photo"=>$member->getPhoto(),"role"=>Roles::Developer());
                    if($key->getTeamleader())
                        if($key->getTeamleader()->getId()==$member->getCredentials()->getId())
                            $memberdata["isTeamLeader"]=true;
                        else
                            $memberdata["isTeamLeader"]=false;
                    else
                        $memberdata["isTeamLeader"]=false;
                    array_push($team,$memberdata);
                    $j++;
                }           
                
                foreach ($key->getTesters() as $member) {
                    $memberdata=array("id"=>$member->getId(),"surname"=>$member->getSurname(),"name"=>$member->getName(),"email"=>$member->getEmail(),"photo"=>$member->getPhoto(),"role"=>Roles::Developer());
                    if($key->getTeamleader())
                        if($key->getTeamleader()->getId()==$member->getCredentials()->getId())
                            $memberdata["isTeamLeader"]=true;
                        else
                            $memberdata["isTeamLeader"]=false;
                    else
                        $memberdata["isTeamLeader"]=false;
                    array_push($team,$memberdata);
                    $j++;
                }  
               
                foreach ($key->getDesigners() as $member) {
                   $memberdata=array("id"=>$member->getId(),"surname"=>$member->getSurname(),"name"=>$member->getName(),"email"=>$member->getEmail(),"photo"=>$member->getPhoto(),"role"=>Roles::Developer());
                    if($key->getTeamleader())
                        if($key->getTeamleader()->getId()==$member->getCredentials()->getId())
                            $memberdata["isTeamLeader"]=true;
                        else
                            $memberdata["isTeamLeader"]=false;
                    else
                        $memberdata["isTeamLeader"]=false;
                    array_push($team,$memberdata);
                    $j++;
                }  

                foreach ($key->getSysadmins() as $member) {
                    $memberdata=array("id"=>$member->getId(),"surname"=>$member->getSurname(),"name"=>$member->getName(),"email"=>$member->getEmail(),"photo"=>$member->getPhoto(),"role"=>Roles::Developer());
                    if($key->getTeamleader())
                        if($key->getTeamleader()->getId()==$member->getCredentials()->getId())
                            $memberdata["isTeamLeader"]=true;
                        else
                            $memberdata["isTeamLeader"]=false;
                    else
                        $memberdata["isTeamLeader"]=false;
                    array_push($team,$memberdata);
                    $j++;        
                }  
                $data["team"]=$team;
                $mess[$i]=$data;
                $i++;
            }
            $res=new Response();
            $res->setStatusCode(200);
            $res->setContent(json_encode($mess));
            $res->headers->set('Content-Type', 'application/json');
            return $res;
        }
        else
        {
            $response=new Response('{"err":"'.ConstValues::INVALIDREQUEST.'"}',400);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
    }
    public function assignRateAction()
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
            if(!isset($json->{"project_id"}) || !isset($json->{"rate"}))
            {
                $response=new Response('{"err":"'.ConstValues::INVALIDREQUEST.'"}',400);
                $response->headers->set('Content-Type', 'application/json');
                return $response;       
            }
            else
            {
                $project=$em->getRepository("AcmtoolAppBundle:Project")->findOneById($json->{"project_id"});
                if($project)
                {

                    $project->setRate($json->{"rate"});
                    $em->flush();
                    $res=new Response();
                    $res->setStatusCode(200);
                    $res->setContent(ConstValues::MEMBERDELETED);
                    return $res;
                }
                else
                {    
                    $response=new Response('{"err":"'.ConstValues::INVALIDREQUEST.'"}',400);
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;  
                }
            }
        }
    }
    public function generateTicketReportAction($month,$year,$project_id)
    {
        $em = $this->getDoctrine()->getManager();
        $project=$em->getRepository("AcmtoolAppBundle:Project")->findOneById($project_id);
        if($project)
        {
            $tickets=$project->getTickets();
            $mess=array();
            $i=0;
            foreach ($tickets as $ticket) {
               $tasks=$em->getRepository("AcmtoolAppBundle:Task")->getTasksByMonth($ticket,$month,$year);
                if(count($tasks)>0)
                {
                    $ticketData=array("id"=>$ticket->getId(),"title"=>$ticket->getTitle());
                   $tasksdata=array();
                   $sum=0;
                   $j=0;

                   foreach ($tasks as $key) {
                       $data=array("id"=>$key->getId(),"title"=>$key->getTitle(),"estimation"=>$key->getEstimation(),"realtime"=>$key->getRealtime(),"date"=>date_format($key->getFinishdate(), 'Y-m-d'));
                       $sum+=$key->getRealtime();
                       $tasksdata[$j]=$data;
                       $j++;

                   }
                   $ticketData["totalhours"]=$sum;
                   $ticketData["stories"]=$tasksdata;
                   $mess[$i]=$ticketData;
                   $i++;
                }
               
            }
            $res=new Response();
            $res->setStatusCode(200);
            $res->setContent(json_encode($mess));
            $res->headers->set('Content-Type', 'application/json');
            return $res;
        }
        else
        {
            $response=new Response('{"err":"'.ConstValues::INVALIDREQUEST.'"}',400);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
    }
    public function generateDateReportAction($month,$year,$project_id)
    {
        $em = $this->getDoctrine()->getManager();
        $project=$em->getRepository("AcmtoolAppBundle:Project")->findOneById($project_id);
        if($project)
        {
            $tickets=$project->getTickets();
            $mess=array();
            $i=0;
            $days=array();
            foreach ($tickets as $ticket) {
               $tasks=$em->getRepository("AcmtoolAppBundle:Task")->getTasksByMonth($ticket,$month,$year);
                if(count($tasks)>0)
                {
                   foreach ($tasks as $key) {
                       $data=array("id"=>$key->getId(),"title"=>$key->getTitle(),"estimation"=>$key->getEstimation(),"realtime"=>$key->getRealtime(),"ticket"=>$ticket->getTitle());
                       if(array_key_exists(date_format($key->getFinishdate(),'Y-m-d'),$days))
                       {
                            array_push($days[date_format($key->getFinishdate(),'Y-m-d')]["stories"], $data);
                            $days[date_format($key->getFinishdate(),'Y-m-d')]["totalhours"]+=$key->getRealtime();
                       }
                       else
                       {
                            $days[date_format($key->getFinishdate(),'Y-m-d')]["stories"]=array();
                            array_push($days[date_format($key->getFinishdate(),'Y-m-d')]["stories"], $data);
                            $days[date_format($key->getFinishdate(),'Y-m-d')]["totalhours"]=$key->getRealtime();
                            $days[date_format($key->getFinishdate(),'Y-m-d')]["date"]=date_format($key->getFinishdate(),'Y-m-d');
                       }

                    }
                }
               
            }
            $mess=array_values($days);
            $res=new Response();
            $res->setStatusCode(200);
            $res->setContent(json_encode($mess));
            $res->headers->set('Content-Type', 'application/json');
            return $res;
        }
        else
        {
            $response=new Response('{"err":"'.ConstValues::INVALIDREQUEST.'"}',400);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
    }

}