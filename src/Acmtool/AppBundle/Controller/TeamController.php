<?php

namespace Acmtool\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Httpfoundation\Response;
use Acmtool\AppBundle\Entity\Developer;
use Acmtool\AppBundle\Entity\Creds;
use Acmtool\AppBundle\Entity\Titles;
use Acmtool\AppBundle\Entity\ConstValues;
use Acmtool\AppBundle\Entity\Roles;
use Acmtool\AppBundle\Entity\WorkedDay;

class TeamController extends Controller
{
	public function getAllTeamMmebersAction()
	{
		 $em = $this->getDoctrine()->getManager();
		 $keyaccounts=$em->getRepository("AcmtoolAppBundle:KeyAccount")->findAll();
		 $users=array();
		 $i=0;
         $date=new \DateTime("UTC");
         $month=$date->format("m");
		 if($keyaccounts>0)
		 {
            foreach ($keyaccounts as $user) {
                $users[$i] = array('id'=>$user->getId(),'email'=>$user->getEmail(),"name"=>$user->getName(),"surname"=>$user->getSurname(),"photo"=>$user->getPhoto(),"description"=>$user->getDescription(),"city"=>$user->getCity(),"country"=>$user->getCountry(),"role"=>Roles::KeyAccount(),"username"=>$user->getUsername(),"status"=>$user->getState(),"title"=>$user->getTitle(),"phonecode"=>$user->getPhonecode(),"phonenumber"=>$user->getPhonenumber(),"language"=>$user->getLanguage(),"hourate"=>$user->getHourrate(),"level"=>$user->getLevel());
                $users[$i]["projectcount"]=0;
                $users[$i]["finishedtasks"]=0;
                $users[$i]["totaltasks"]=0;
                $i++;

            }
            
		 }
		 //For this version we don't use teamleasder version but we keep it for later versions
		 /*$teamleaders=$em->getRepository("AcmtoolAppBundle:TeamLeader")->findAll();
		 if($teamleaders>0)
		 {
		 	
            foreach ($teamleaders as $user) {
                $users[$i] = array('id'=>$user->getId(),'email'=>$user->getEmail(),"name"=>$user->getName(),"surname"=>$user->getSurname(),"photo"=>$user->getPhoto(),"description"=>$user->getDescription(),"city"=>$user->getCity(),"country"=>$user->getCountry(),"role"=>Roles::Teamlead(),"username"=>$user->getUsername(),"capacity"=>$user->getCapacity(),"status"=>$user->getState(),"skills"=>$user->getSkills(),"title"=>$user->getTitle(),"phonecode"=>$user->getPhonecode(),"phonenumber"=>$user->getPhonenumber(),"language"=>$user->getLanguage(),"hourate"=>$user->getHourrate(),"level"=>$user->getLevel());
                $users[$i]["projectcount"]=count($user->getProjects());
                $finished=0;
                $total=count($user->getTasks());
                foreach ($user->getTasks() as $key) {
                    if($key->getIsFinished())
                        $finished++;
                }
                $users[$i]["finishedtasks"]=$finished;
                $users[$i]["totaltasks"]=$total;
                $i++;

            }
            
		 }*/
		 $developers=$em->getRepository("AcmtoolAppBundle:Developer")->findAll();
		 if($developers>0)
		 {
		 	
            foreach ($developers as $user) {
                
                $users[$i] = array('id'=>$user->getId(),'email'=>$user->getEmail(),"name"=>$user->getName(),"surname"=>$user->getSurname(),"photo"=>$user->getPhoto(),"description"=>$user->getDescription(),"city"=>$user->getCity(),"country"=>$user->getCountry(),"role"=>Roles::Developer(),"username"=>$user->getUsername(),"capacity"=>$user->getCapacity(),"status"=>$user->getState(),"skills"=>$user->getSkills(),"title"=>$user->getTitle(),"phonecode"=>$user->getPhonecode(),"phonenumber"=>$user->getPhonenumber(),"language"=>$user->getLanguage(),"hourate"=>$user->getHourrate(),"level"=>$user->getLevel());
                 $users[$i]["projectcount"]=count($user->getProjects());
                $finished=0;
                $users[$i]["workeddays"]=$this->getHoursByMonth($user->getCredentials(),$month,$user->getCapacity());
                $total=count($user->getTasks());
                foreach ($user->getTasks() as $key) {
                    if($key->getIsFinished())
                        $finished++;
                }
                $users[$i]["finishedtasks"]=$finished;
                $users[$i]["totaltasks"]=$total;
                $i++;

            }
           
		 }
		 $testers=$em->getRepository("AcmtoolAppBundle:Tester")->findAll();
		 if($testers>0)
		 {
		 	
            foreach ($testers as $user) {
                $users[$i] = array('id'=>$user->getId(),'email'=>$user->getEmail(),"name"=>$user->getName(),"surname"=>$user->getSurname(),"photo"=>$user->getPhoto(),"description"=>$user->getDescription(),"city"=>$user->getCity(),"country"=>$user->getCountry(),"role"=>Roles::Tester(),"username"=>$user->getUsername(),"capacity"=>$user->getCapacity(),"status"=>$user->getState(),"skills"=>$user->getSkills(),"title"=>$user->getTitle(),"phonecode"=>$user->getPhonecode(),"phonenumber"=>$user->getPhonenumber(),"language"=>$user->getLanguage(),"hourate"=>$user->getHourrate(),"level"=>$user->getLevel());
                 $users[$i]["projectcount"]=count($user->getProjects());
                $finished=0;
                 $users[$i]["workeddays"]=$this->getHoursByMonth($user->getCredentials(),$month,$user->getCapacity());
                $total=count($user->getTasks());
                foreach ($user->getTasks() as $key) {
                    if($key->getIsFinished())
                        $finished++;
                }
                $users[$i]["finishedtasks"]=$finished;
                $users[$i]["totaltasks"]=$total;
                $i++;

            }
           
		 }
		 $designers=$em->getRepository("AcmtoolAppBundle:Designer")->findAll();
		 if($designers>0)
		 {
		 	
            foreach ($designers as $user) {
                $users[$i] = array('id'=>$user->getId(),'email'=>$user->getEmail(),"name"=>$user->getName(),"surname"=>$user->getSurname(),"photo"=>$user->getPhoto(),"description"=>$user->getDescription(),"city"=>$user->getCity(),"country"=>$user->getCountry(),"role"=>Roles::Designer(),"username"=>$user->getUsername(),"capacity"=>$user->getCapacity(),"status"=>$user->getState(),"skills"=>$user->getSkills(),"title"=>$user->getTitle(),"phonecode"=>$user->getPhonecode(),"phonenumber"=>$user->getPhonenumber(),"language"=>$user->getLanguage(),"hourate"=>$user->getHourrate(),"level"=>$user->getLevel());
                 $users[$i]["projectcount"]=count($user->getProjects());
                $finished=0;
                 $users[$i]["workeddays"]=$this->getHoursByMonth($user->getCredentials(),$month,$user->getCapacity());
                $total=count($user->getTasks());
                foreach ($user->getTasks() as $key) {
                    if($key->getIsFinished())
                        $finished++;
                }
                $users[$i]["finishedtasks"]=$finished;
                $users[$i]["totaltasks"]=$total;
                $i++;

            }
            
		 }
		 $admins=$em->getRepository("AcmtoolAppBundle:SystemAdmin")->findAll();
		 if($admins>0)
		 {
		 	
            foreach ($admins as $user) {
                $users[$i] = array('id'=>$user->getId(),'email'=>$user->getEmail(),"name"=>$user->getName(),"surname"=>$user->getSurname(),"photo"=>$user->getPhoto(),"description"=>$user->getDescription(),"city"=>$user->getCity(),"country"=>$user->getCountry(),"role"=>Roles::SysAdmin(),"username"=>$user->getUsername(),"capacity"=>$user->getCapacity(),"status"=>$user->getState(),"skills"=>$user->getSkills(),"title"=>$user->getTitle(),"phonecode"=>$user->getPhonecode(),"phonenumber"=>$user->getPhonenumber(),"language"=>$user->getLanguage(),"hourate"=>$user->getHourrate(),"level"=>$user->getLevel());
                 $users[$i]["projectcount"]=count($user->getProjects());
                $finished=0;
                 $users[$i]["workeddays"]=$this->getHoursByMonth($user->getCredentials(),$month,$user->getCapacity());
                $total=count($user->getTasks());
                foreach ($user->getTasks() as $key) {
                    if($key->getIsFinished())
                        $finished++;
                }
                $users[$i]["finishedtasks"]=$finished;
                $users[$i]["totaltasks"]=$total;
                $i++;

            }
           
		 }
		 $res=new Response();
         $res->setStatusCode(200);
         $res->headers->set('Content-Type', 'application/json');
         $res->setContent(json_encode($users));
         return $res;

	}
    public function getAllDevTeamMmebersAction()
    {
         $em = $this->getDoctrine()->getManager();
         
         $users=array();
         $i=0;
         //For this version we don't use teamleasder version but we keep it for later versions
         /*if($this->get('security.context')->isGranted("ROLE_ADMIN")){
             $teamleaders=$em->getRepository("AcmtoolAppBundle:TeamLeader")->findAll();
             if($teamleaders>0)
             {
                
                foreach ($teamleaders as $user) {
                    $users[$i] = array('id'=>$user->getId(),'email'=>$user->getEmail(),"name"=>$user->getName(),"surname"=>$user->getSurname(),"photo"=>$user->getPhoto(),"description"=>$user->getDescription(),"city"=>$user->getCity(),"country"=>$user->getCountry(),"role"=>Roles::Teamlead(),"username"=>$user->getUsername(),"capacity"=>$user->getCapacity(),"status"=>$user->getState(),"skills"=>$user->getSkills(),"title"=>$user->getTitle(),"phonecode"=>$user->getPhonecode(),"phonenumber"=>$user->getPhonenumber(),"order"=>$i);
                    $i++;

                }
                
             }
         }*/
         $developers=$em->getRepository("AcmtoolAppBundle:Developer")->findAll();
         if($developers>0)
         {
            
            foreach ($developers as $user) {
                $users[$i] = array('id'=>$user->getId(),'email'=>$user->getEmail(),"name"=>$user->getName(),"surname"=>$user->getSurname(),"photo"=>$user->getPhoto(),"description"=>$user->getDescription(),"city"=>$user->getCity(),"country"=>$user->getCountry(),"role"=>Roles::Developer(),"username"=>$user->getUsername(),"capacity"=>$user->getCapacity(),"status"=>$user->getState(),"skills"=>$user->getSkills(),"title"=>$user->getTitle(),"phonecode"=>$user->getPhonecode(),"phonenumber"=>$user->getPhonenumber(),"order"=>$i);
                $i++;

            }
           
         }
         $testers=$em->getRepository("AcmtoolAppBundle:Tester")->findAll();
         if($testers>0)
         {
            
            foreach ($testers as $user) {
                $users[$i] = array('id'=>$user->getId(),'email'=>$user->getEmail(),"name"=>$user->getName(),"surname"=>$user->getSurname(),"photo"=>$user->getPhoto(),"description"=>$user->getDescription(),"city"=>$user->getCity(),"country"=>$user->getCountry(),"role"=>Roles::Tester(),"username"=>$user->getUsername(),"capacity"=>$user->getCapacity(),"status"=>$user->getState(),"skills"=>$user->getSkills(),"title"=>$user->getTitle(),"phonecode"=>$user->getPhonecode(),"phonenumber"=>$user->getPhonenumber(),"order"=>$i);
                $i++;

            }
           
         }
         $designers=$em->getRepository("AcmtoolAppBundle:Designer")->findAll();
         if($designers>0)
         {
            
            foreach ($designers as $user) {
                $users[$i] = array('id'=>$user->getId(),'email'=>$user->getEmail(),"name"=>$user->getName(),"surname"=>$user->getSurname(),"photo"=>$user->getPhoto(),"description"=>$user->getDescription(),"city"=>$user->getCity(),"country"=>$user->getCountry(),"role"=>Roles::Designer(),"username"=>$user->getUsername(),"capacity"=>$user->getCapacity(),"status"=>$user->getState(),"skills"=>$user->getSkills(),"title"=>$user->getTitle(),"phonecode"=>$user->getPhonecode(),"phonenumber"=>$user->getPhonenumber(),"order"=>$i);
                $i++;

            }
            
         }
         $admins=$em->getRepository("AcmtoolAppBundle:SystemAdmin")->findAll();
         if($admins>0)
         {
            
            foreach ($admins as $user) {
                $users[$i] = array('id'=>$user->getId(),'email'=>$user->getEmail(),"name"=>$user->getName(),"surname"=>$user->getSurname(),"photo"=>$user->getPhoto(),"description"=>$user->getDescription(),"city"=>$user->getCity(),"country"=>$user->getCountry(),"role"=>Roles::SysAdmin(),"username"=>$user->getUsername(),"capacity"=>$user->getCapacity(),"status"=>$user->getState(),"skills"=>$user->getSkills(),"title"=>$user->getTitle(),"phonecode"=>$user->getPhonecode(),"phonenumber"=>$user->getPhonenumber(),"order"=>$i);
                $i++;

            }
           
         }
         $res=new Response();
         $res->setStatusCode(200);
         $res->headers->set('Content-Type', 'application/json');
         $res->setContent(json_encode($users));
         return $res;

    }
    public function getPerformanceByMonthAction($month)
    {
         $em = $this->getDoctrine()->getManager();
         $keyaccounts=$em->getRepository("AcmtoolAppBundle:KeyAccount")->findAll();
         $users=array();
         $i=0;
         $date=new \DateTime("UTC");
         if($keyaccounts>0)
         {
            foreach ($keyaccounts as $user) {
                $users[$i] = array();
                $i++;
            }
            
         }
         //For this version we don't use teamleasder version but we keep it for later versions
         /*$teamleaders=$em->getRepository("AcmtoolAppBundle:TeamLeader")->findAll();
         if($teamleaders>0)
         {
            
            foreach ($teamleaders as $user) {
                $users[$i] = array();
               
                $i++;

            }
            
         }*/
         $developers=$em->getRepository("AcmtoolAppBundle:Developer")->findAll();
         if($developers>0)
         {
            
            foreach ($developers as $user) {
                $users[$i]["workeddays"]=$this->getHoursByMonth($user->getCredentials(),$month,$user->getCapacity());
                $i++;
            }
           
         }
         $testers=$em->getRepository("AcmtoolAppBundle:Tester")->findAll();
         if($testers>0)
         {
            
            foreach ($testers as $user) {
                $users[$i]["workeddays"]=$this->getHoursByMonth($user->getCredentials(),$month,$user->getCapacity());
                $i++;

            }
           
         }
         $designers=$em->getRepository("AcmtoolAppBundle:Designer")->findAll();
         if($designers>0)
         {
            
            foreach ($designers as $user) {
                $users[$i]["workeddays"]=$this->getHoursByMonth($user->getCredentials(),$month,$user->getCapacity());
                $i++;

            }
            
         }
         $admins=$em->getRepository("AcmtoolAppBundle:SystemAdmin")->findAll();
         if($admins>0)
         {
            
            foreach ($admins as $user) {
               $users[$i]["workeddays"]=$this->getHoursByMonth($user->getCredentials(),$month,$user->getCapacity());
                $i++;

            }
           
         }
         $res=new Response();
         $res->setStatusCode(200);
         $res->headers->set('Content-Type', 'application/json');
         $res->setContent(json_encode($users));
         return $res;
    }
    public function getTeamRolesAction()
    {
        $Roles=array("KeyAccount"=>Roles::KeyAccount(),"TeamLeader"=>Roles::Teamlead(),"Developer"=>Roles::Developer(),
            "Tester"=>Roles::Tester(),"Designer"=>Roles::Designer(),"SysAdmin"=>Roles::SysAdmin());
        $mess=array("Roles"=>$Roles);
        $res=new Response();
        $res->setStatusCode(200);
        $res->headers->set('Content-Type', 'application/json');
        $res->setContent(json_encode($mess));
        return $res;
    }
	public function uploadPhotoAction($id,$role)
	{
		$request = $this->get('request');
		$baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
		$path=__DIR__.'/../../../../web'.'/uploads/teamphotos';
		$em = $this->getDoctrine()->getManager();
        $data = $request->getContent();
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        list(,$extension)=explode("/", $type);
        $data = base64_decode($data);
        $user=null;
        $keyaccountrole=Roles::KeyAccount();
        $teamleadrole=Roles::Teamlead();
        $developerrole=Roles::Developer();
        $testerrole=Roles::Tester();
        $designerrole=Roles::Designer();
        $sysadminrole=Roles::SysAdmin();
        if($role==$keyaccountrole["role"])
        {
        	$user=$em->getRepository("AcmtoolAppBundle:KeyAccount")->findOneById($id);
        }
        elseif ($role==$teamleadrole["role"]) {
        	$user=$em->getRepository("AcmtoolAppBundle:TeamLeader")->findOneById($id);
        }
        elseif ($role==$developerrole["role"]) {
        	$user=$em->getRepository("AcmtoolAppBundle:Developer")->findOneById($id);
        }
        elseif ($role==$testerrole["role"]) {
        	$user=$em->getRepository("AcmtoolAppBundle:Tester")->findOneById($id);
        }
        elseif ($role==$designerrole["role"]) {
        	$user=$em->getRepository("AcmtoolAppBundle:Designer")->findOneById($id);
        }
        elseif ($role==$sysadminrole["role"]) {
        	$user=$em->getRepository("AcmtoolAppBundle:SystemAdmin")->findOneById($id);
        }
        if($user!=null)
        {
        	$filename=$this->random_string(70);
        	$result=file_put_contents($path."/".$filename.".".$extension, $data);
        	$photoUrl=$baseurl."/uploads/teamphotos/".$filename.".".$extension;
        	$user->setPhoto($photoUrl);
        	$em->flush();
			$res=new Response();
	        $res->setStatusCode(200);
	        $res->setContent("photo uplaoded successfully");
	        return $res;
        }
        else{
        	$res=new Response();
	        $res->setStatusCode(400);
	        $res->setContent("Can't upload photo");
	        return $res;
        }
		
	}
    private function getHoursByMonth($user,$month,$capacity)
    {
        $em = $this->getDoctrine()->getManager();
        $hours=$em->getRepository("AcmtoolAppBundle:WorkedHours")->getWorkedHoursByMonth($user,$month);
        $mess=array();
        if($hours)
        {
           $week1=array();
           $week2=array();
           $week3=array();
           $week4=array();
           $week5=array();
            foreach ($hours as $key) {
                if($key->getWeek()==1)
                {
                    if(!array_key_exists ( $key->getDay() , $week1 ))
                    {
                        $week1[$key->getDay()]=$this->createWorkingday($key);
                    }
                    else
                    {
                        $workedDay=$week1[$key->getDay()];
                        $sum=$workedDay->getTotalHours()+$key->getWorkedhour();
                        $workedDay->setTotalHours($sum);
                    }
                }
                if($key->getWeek()==2)
                {
                    if(!array_key_exists ( $key->getDay() , $week2 ))
                    {
                       
                        $week2[$key->getDay()]=$this->createWorkingday($key);
                    }
                    else
                    {
                        $workedDay=$week2[$key->getDay()];
                        $sum=$workedDay->getTotalHours()+$key->getWorkedhour();
                        $workedDay->setTotalHours($sum);
                    }
                }
                if($key->getWeek()==3)
                {
                    if(!array_key_exists ( $key->getDay() , $week3 ))
                    {
                       
                        $week3[$key->getDay()]=$this->createWorkingday($key);
                    }
                    else
                    {
                        $workedDay=$week3[$key->getDay()];
                        $sum=$workedDay->getTotalHours()+$key->getWorkedhour();
                        $workedDay->setTotalHours($sum);
                    }
                }
                if($key->getWeek()==4)
                {
                    if(!array_key_exists ( $key->getDay() , $week4 ))
                    {
                       
                        $week4[$key->getDay()]=$this->createWorkingday($key);
                    }
                    else
                    {
                        $workedDay=$week4[$key->getDay()];
                        $sum=$workedDay->getTotalHours()+$key->getWorkedhour();
                        $workedDay->setTotalHours($sum);
                    }
                }
                if($key->getWeek()==5)
                {
                    if(!array_key_exists ( $key->getDay() , $week5 ))
                    {
                       
                        $week5[$key->getDay()]=$this->createWorkingday($key);
                    }
                    else
                    {
                        $workedDay=$week5[$key->getDay()];
                        $sum=$workedDay->getTotalHours()+$key->getWorkedhour();
                        $workedDay->setTotalHours($sum);
                    }
                }
                
            }
            if(count($week1))
            {
               $mess["week1"]=$this->serializeWeek($week1,$capacity);
            }
            if(count($week2))
            {
                $mess["week2"]=$this->serializeWeek($week2,$capacity);
            }
            if(count($week3))
            {
                $mess["week3"]=$this->serializeWeek($week3,$capacity);
            }
            if(count($week4))
            {
                $mess["week4"]=$this->serializeWeek($week4,$capacity);
            }
            if(count($week5))
            {
                $mess["week5"]=$this->serializeWeek($week5,$capacity);
            }
            
        }
        return $mess;
    }
    private function serializeWeek($week,$capacity)
    {
        $mess=array();
        $i=0;
        $sum=0;
        $data=array();
        foreach ($week as $key) {
            $data[$i]=$key->serialize();
            $i++;
            $sum+=$key->getTotalHours();
        }
        $performance=($sum/floatval($capacity))*100;
        $mess["days"]=$data;
        $mess["weekhours"]=$sum;
        $mess["weekperformance"]=$performance;
        return $mess;

    }
    private function createWorkingday($key)
    {
        $workedDay=new WorkedDay();
        $workedDay->setYear($key->getYear());
        $workedDay->setMonth($key->getMonth());
        $workedDay->setDay($key->getDay());
        $workedDay->setDayOfTheWeek($key->getDayOfTheWeek());
        $workedDay->setWeek($key->getWeek());
        $workedDay->setTotalHours($key->getWorkedhour());
        return $workedDay;
    }
	private function random_string($length) {
	    $key = '';
	    $keys = array_merge(range(0, 9), range('a', 'z'));

	    for ($i = 0; $i < $length; $i++) {
	        $key .= $keys[array_rand($keys)];
	    }

	    return $key;
	}
}