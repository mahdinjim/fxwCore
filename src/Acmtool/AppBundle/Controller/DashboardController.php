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
use Acmtool\AppBundle\Entity\Admin;
use Acmtool\AppBundle\Entity\TaskTypes;
class DashboardController extends Controller
{
	public function adminDashboardAction()
	{
		$total=array();
		for ($i=0; $i < 12; $i++) { 
			$total[$i]["estimated"]=0;
			$total[$i]["real"]=0;
			$total[$i]["billable"]=0;
		}
		$em = $this->getDoctrine()->getManager();
		$projects=$em->getRepository("AcmtoolAppBundle:Project")->findAll();
		$projectsData=array();
		foreach ($projects as $project) {
			$projectData=array();
			$projectData["name"]=$project->getName();
			if($project->getOwner()!=null)
				$projectData["company"]=$project->getOwner()->getCompanyname();
			else
				$projectData["company"]="deleted";
			$projectData["months"]=array();
			for ($i=0; $i < 12; $i++) { 
				$estimated=0;
				$real=0;
				$billable=0;
				$tickets=$em->getRepository("AcmtoolAppBundle:Ticket")->getDoneTicketByMonth($project,($i+1));
				foreach ($tickets as $ticket) {
					$estimated+=$ticket->getEstimation();
					$real+=$ticket->getRealtime();
					if($ticket->getEstimation()>$ticket->getRealtime())
						$billable+=$ticket->getRealtime();
					else
						$billable+=$ticket->getEstimation();
				}
				$total[$i]["estimated"]+=$estimated;
				$total[$i]["real"]+=$real;
				$total[$i]["billable"]+=$billable;
				$projectData["months"][$i]["estimated"]=$estimated;
				$projectData["months"][$i]["real"]=$real;
				$projectData["months"][$i]["billable"]=$billable;
			}
			array_push($projectsData, $projectData);
			
		}
		$mess=array();
		$mess["projects"]=$projectsData;
		$mess["total"]=$total;
		$res=new Response();
        $res->setStatusCode(200);
        $res->setContent(json_encode($mess));
        return $res;
	}
}