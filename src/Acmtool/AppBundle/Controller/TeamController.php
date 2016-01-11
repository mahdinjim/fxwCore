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

class TeamController extends Controller
{
	public function getAllTeamMmebersAction()
	{
		 $em = $this->getDoctrine()->getManager();
		 $keyaccounts=$em->getRepository("AcmtoolAppBundle:KeyAccount")->findAll();
		 $users=array();
		 $i=0;
		 if($keyaccounts>0)
		 {
            foreach ($keyaccounts as $user) {
                $users[$i] = array('id'=>$user->getId(),'email'=>$user->getEmail(),"name"=>$user->getName(),"surname"=>$user->getSurname(),"photo"=>$user->getPhoto(),"description"=>$user->getDescription(),"city"=>$user->getCity(),"country"=>$user->getCountry(),"role"=>Roles::KeyAccount(),"username"=>$user->getUsername(),"status"=>$user->getState(),"title"=>$user->getTitle(),"phonecode"=>$user->getPhonecode(),"phonenumber"=>$user->getPhonenumber());
                $i++;

            }
            
		 }
		 
		 $teamleaders=$em->getRepository("AcmtoolAppBundle:TeamLeader")->findAll();
		 if($teamleaders>0)
		 {
		 	
            foreach ($teamleaders as $user) {
                $users[$i] = array('id'=>$user->getId(),'email'=>$user->getEmail(),"name"=>$user->getName(),"surname"=>$user->getSurname(),"photo"=>$user->getPhoto(),"description"=>$user->getDescription(),"city"=>$user->getCity(),"country"=>$user->getCountry(),"role"=>Roles::Teamlead(),"username"=>$user->getUsername(),"capacity"=>$user->getCapacity(),"status"=>$user->getState(),"skills"=>$user->getSkills(),"title"=>$user->getTitle(),"phonecode"=>$user->getPhonecode(),"phonenumber"=>$user->getPhonenumber());
                $i++;

            }
            
		 }
		 $developers=$em->getRepository("AcmtoolAppBundle:Developer")->findAll();
		 if($developers>0)
		 {
		 	
            foreach ($developers as $user) {
                $users[$i] = array('id'=>$user->getId(),'email'=>$user->getEmail(),"name"=>$user->getName(),"surname"=>$user->getSurname(),"photo"=>$user->getPhoto(),"description"=>$user->getDescription(),"city"=>$user->getCity(),"country"=>$user->getCountry(),"role"=>Roles::Developer(),"username"=>$user->getUsername(),"capacity"=>$user->getCapacity(),"status"=>$user->getState(),"skills"=>$user->getSkills(),"title"=>$user->getTitle(),"phonecode"=>$user->getPhonecode(),"phonenumber"=>$user->getPhonenumber());
                $i++;

            }
           
		 }
		 $testers=$em->getRepository("AcmtoolAppBundle:Tester")->findAll();
		 if($testers>0)
		 {
		 	
            foreach ($testers as $user) {
                $users[$i] = array('id'=>$user->getId(),'email'=>$user->getEmail(),"name"=>$user->getName(),"surname"=>$user->getSurname(),"photo"=>$user->getPhoto(),"description"=>$user->getDescription(),"city"=>$user->getCity(),"country"=>$user->getCountry(),"role"=>Roles::Tester(),"username"=>$user->getUsername(),"capacity"=>$user->getCapacity(),"status"=>$user->getState(),"skills"=>$user->getSkills(),"title"=>$user->getTitle(),"phonecode"=>$user->getPhonecode(),"phonenumber"=>$user->getPhonenumber());
                $i++;

            }
           
		 }
		 $designers=$em->getRepository("AcmtoolAppBundle:Designer")->findAll();
		 if($designers>0)
		 {
		 	
            foreach ($designers as $user) {
                $users[$i] = array('id'=>$user->getId(),'email'=>$user->getEmail(),"name"=>$user->getName(),"surname"=>$user->getSurname(),"photo"=>$user->getPhoto(),"description"=>$user->getDescription(),"city"=>$user->getCity(),"country"=>$user->getCountry(),"role"=>Roles::Designer(),"username"=>$user->getUsername(),"capacity"=>$user->getCapacity(),"status"=>$user->getState(),"skills"=>$user->getSkills(),"title"=>$user->getTitle(),"phonecode"=>$user->getPhonecode(),"phonenumber"=>$user->getPhonenumber());
                $i++;

            }
            
		 }
		 $admins=$em->getRepository("AcmtoolAppBundle:SystemAdmin")->findAll();
		 if($admins>0)
		 {
		 	
            foreach ($admins as $user) {
                $users[$i] = array('id'=>$user->getId(),'email'=>$user->getEmail(),"name"=>$user->getName(),"surname"=>$user->getSurname(),"photo"=>$user->getPhoto(),"description"=>$user->getDescription(),"city"=>$user->getCity(),"country"=>$user->getCountry(),"role"=>Roles::SysAdmin(),"username"=>$user->getUsername(),"capacity"=>$user->getCapacity(),"status"=>$user->getState(),"skills"=>$user->getSkills(),"title"=>$user->getTitle(),"phonecode"=>$user->getPhonecode(),"phonenumber"=>$user->getPhonenumber());
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
         if($this->get('security.context')->isGranted("ROLE_ADMIN")){
             $teamleaders=$em->getRepository("AcmtoolAppBundle:TeamLeader")->findAll();
             if($teamleaders>0)
             {
                
                foreach ($teamleaders as $user) {
                    $users[$i] = array('id'=>$user->getId(),'email'=>$user->getEmail(),"name"=>$user->getName(),"surname"=>$user->getSurname(),"photo"=>$user->getPhoto(),"description"=>$user->getDescription(),"city"=>$user->getCity(),"country"=>$user->getCountry(),"role"=>Roles::Teamlead(),"username"=>$user->getUsername(),"capacity"=>$user->getCapacity(),"status"=>$user->getState(),"skills"=>$user->getSkills(),"title"=>$user->getTitle(),"phonecode"=>$user->getPhonecode(),"phonenumber"=>$user->getPhonenumber());
                    $i++;

                }
                
             }
         }
         $developers=$em->getRepository("AcmtoolAppBundle:Developer")->findAll();
         if($developers>0)
         {
            
            foreach ($developers as $user) {
                $users[$i] = array('id'=>$user->getId(),'email'=>$user->getEmail(),"name"=>$user->getName(),"surname"=>$user->getSurname(),"photo"=>$user->getPhoto(),"description"=>$user->getDescription(),"city"=>$user->getCity(),"country"=>$user->getCountry(),"role"=>Roles::Developer(),"username"=>$user->getUsername(),"capacity"=>$user->getCapacity(),"status"=>$user->getState(),"skills"=>$user->getSkills(),"title"=>$user->getTitle(),"phonecode"=>$user->getPhonecode(),"phonenumber"=>$user->getPhonenumber());
                $i++;

            }
           
         }
         $testers=$em->getRepository("AcmtoolAppBundle:Tester")->findAll();
         if($testers>0)
         {
            
            foreach ($testers as $user) {
                $users[$i] = array('id'=>$user->getId(),'email'=>$user->getEmail(),"name"=>$user->getName(),"surname"=>$user->getSurname(),"photo"=>$user->getPhoto(),"description"=>$user->getDescription(),"city"=>$user->getCity(),"country"=>$user->getCountry(),"role"=>Roles::Tester(),"username"=>$user->getUsername(),"capacity"=>$user->getCapacity(),"status"=>$user->getState(),"skills"=>$user->getSkills(),"title"=>$user->getTitle(),"phonecode"=>$user->getPhonecode(),"phonenumber"=>$user->getPhonenumber());
                $i++;

            }
           
         }
         $designers=$em->getRepository("AcmtoolAppBundle:Designer")->findAll();
         if($designers>0)
         {
            
            foreach ($designers as $user) {
                $users[$i] = array('id'=>$user->getId(),'email'=>$user->getEmail(),"name"=>$user->getName(),"surname"=>$user->getSurname(),"photo"=>$user->getPhoto(),"description"=>$user->getDescription(),"city"=>$user->getCity(),"country"=>$user->getCountry(),"role"=>Roles::Designer(),"username"=>$user->getUsername(),"capacity"=>$user->getCapacity(),"status"=>$user->getState(),"skills"=>$user->getSkills(),"title"=>$user->getTitle(),"phonecode"=>$user->getPhonecode(),"phonenumber"=>$user->getPhonenumber());
                $i++;

            }
            
         }
         $admins=$em->getRepository("AcmtoolAppBundle:SystemAdmin")->findAll();
         if($admins>0)
         {
            
            foreach ($admins as $user) {
                $users[$i] = array('id'=>$user->getId(),'email'=>$user->getEmail(),"name"=>$user->getName(),"surname"=>$user->getSurname(),"photo"=>$user->getPhoto(),"description"=>$user->getDescription(),"city"=>$user->getCity(),"country"=>$user->getCountry(),"role"=>Roles::SysAdmin(),"username"=>$user->getUsername(),"capacity"=>$user->getCapacity(),"status"=>$user->getState(),"skills"=>$user->getSkills(),"title"=>$user->getTitle(),"phonecode"=>$user->getPhonecode(),"phonenumber"=>$user->getPhonenumber());
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
	private function random_string($length) {
	    $key = '';
	    $keys = array_merge(range(0, 9), range('a', 'z'));

	    for ($i = 0; $i < $length; $i++) {
	        $key .= $keys[array_rand($keys)];
	    }

	    return $key;
	}
}