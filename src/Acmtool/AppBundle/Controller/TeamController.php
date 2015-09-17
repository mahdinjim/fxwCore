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


class TeamController extends Controller
{
	public function getAllTeamMmebersAction()
	{
		 $em = $this->getDoctrine()->getManager();
		 $keyaccounts=$em->getRepository("AcmtoolAppBundle:KeyAccount")->findAll();
		 $mess=array();
		 if($keyaccounts>0)
		 {
		 	$i=0;
            foreach ($keyaccounts as $user) {
                $users[$i] = array('id'=>$user->getId(),'email'=>$user->getEmail(),"name"=>$user->getName(),"surname"=>$user->getSurname(),"photo"=>$user->getPhoto(),"description"=>$user->getDescription(),"city"=>$user->getCity(),"country"=>$user->getCountry(),"role"=>"Account Manager","username"=>$user->getUsername());
                $i++;

            }
            $mess=array_merge($mess,$users);
		 }
		 $teamleaders=$em->getRepository("AcmtoolAppBundle:TeamLeader")->findAll();
		 if($teamleaders>0)
		 {
		 	$i=0;
            foreach ($teamleaders as $user) {
                $users[$i] = array('id'=>$user->getId(),'email'=>$user->getEmail(),"name"=>$user->getName(),"surname"=>$user->getSurname(),"photo"=>$user->getPhoto(),"description"=>$user->getDescription(),"city"=>$user->getCity(),"country"=>$user->getCountry(),"role"=>"Account Manager","username"=>$user->getUsername(),"capacity"=>$user->getCapacity(),"state"=>$user->getState(),"skills"=>$user->getSkills());
                $i++;

            }
            $mess=array_merge($mess,$users);
		 }
		 $teamleaders=$em->getRepository("AcmtoolAppBundle:TeamLeader")->findAll();
		 if($teamleaders>0)
		 {
		 	$i=0;
            foreach ($teamleaders as $user) {
                $users[$i] = array('id'=>$user->getId(),'email'=>$user->getEmail(),"name"=>$user->getName(),"surname"=>$user->getSurname(),"photo"=>$user->getPhoto(),"description"=>$user->getDescription(),"city"=>$user->getCity(),"country"=>$user->getCountry(),"role"=>"Team Leader","username"=>$user->getUsername(),"capacity"=>$user->getCapacity(),"state"=>$user->getState(),"skills"=>$user->getSkills());
                $i++;

            }
            $mess=array_merge($mess,$users);
		 }
		 $developers=$em->getRepository("AcmtoolAppBundle:Developer")->findAll();
		 if($developers>0)
		 {
		 	$i=0;
            foreach ($developers as $user) {
                $users[$i] = array('id'=>$user->getId(),'email'=>$user->getEmail(),"name"=>$user->getName(),"surname"=>$user->getSurname(),"photo"=>$user->getPhoto(),"description"=>$user->getDescription(),"city"=>$user->getCity(),"country"=>$user->getCountry(),"role"=>"Developer","username"=>$user->getUsername(),"capacity"=>$user->getCapacity(),"state"=>$user->getState(),"skills"=>$user->getSkills());
                $i++;

            }
            $mess=array_merge($mess,$users);
		 }
		 $testers=$em->getRepository("AcmtoolAppBundle:Tester")->findAll();
		 if($testers>0)
		 {
		 	$i=0;
            foreach ($testers as $user) {
                $users[$i] = array('id'=>$user->getId(),'email'=>$user->getEmail(),"name"=>$user->getName(),"surname"=>$user->getSurname(),"photo"=>$user->getPhoto(),"description"=>$user->getDescription(),"city"=>$user->getCity(),"country"=>$user->getCountry(),"role"=>"Tester","username"=>$user->getUsername(),"capacity"=>$user->getCapacity(),"state"=>$user->getState(),"skills"=>$user->getSkills());
                $i++;

            }
            $mess=array_merge($mess,$users);
		 }
		 $designers=$em->getRepository("AcmtoolAppBundle:Designer")->findAll();
		 if($designers>0)
		 {
		 	$i=0;
            foreach ($designers as $user) {
                $users[$i] = array('id'=>$user->getId(),'email'=>$user->getEmail(),"name"=>$user->getName(),"surname"=>$user->getSurname(),"photo"=>$user->getPhoto(),"description"=>$user->getDescription(),"city"=>$user->getCity(),"country"=>$user->getCountry(),"role"=>"UX/UI designer","username"=>$user->getUsername(),"capacity"=>$user->getCapacity(),"state"=>$user->getState(),"skills"=>$user->getSkills());
                $i++;

            }
            $mess=array_merge($mess,$users);
		 }
		 $admins=$em->getRepository("AcmtoolAppBundle:SystemAdmin")->findAll();
		 if($admins>0)
		 {
		 	$i=0;
            foreach ($admins as $user) {
                $users[$i] = array('id'=>$user->getId(),'email'=>$user->getEmail(),"name"=>$user->getName(),"surname"=>$user->getSurname(),"photo"=>$user->getPhoto(),"description"=>$user->getDescription(),"city"=>$user->getCity(),"country"=>$user->getCountry(),"role"=>"System Admin","username"=>$user->getUsername(),"capacity"=>$user->getCapacity(),"state"=>$user->getState(),"skills"=>$user->getSkills());
                $i++;

            }
            $mess=array_merge($mess,$users);
		 }
		 $res=new Response();
         $res->setStatusCode(200);
         $res->headers->set('Content-Type', 'application/json');
         $res->setContent(json_encode($mess));
         return $res;

	}
}