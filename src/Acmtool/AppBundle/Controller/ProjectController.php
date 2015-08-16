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
            $project->setOwner($customer);
            $project->setKeyaccount($customer->getKeyAccount());
            $project->setName($json->{'name'});
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
	    		$project->setStartingdate(new \DateTime("NOW",  new \DateTimeZone(ConstValues::TIMEZONE)));

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
            $em->persist($project);
            $em->flush();
            $response=new Response(ConstValues::PROJECTCREATED,200);
            return $response;


        }
	}
    public function updateAction()
    {
        
    }
}