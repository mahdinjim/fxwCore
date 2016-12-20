<?php

namespace Acmtool\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Httpfoundation\Response;

class KpiExternDashboardController extends Controller 
{
	public function getClientNumberAction()
	{
		$dataArray=array();
		$em = $this->getDoctrine()->getManager();
		$clients= $em->getRepository("AcmtoolAppBundle:Customer")->findAll();
		foreach ($clients as $key) {
			if(array_key_exists($key->getYear(),$dataArray))
			{
				$dataArray[$key->getYear()][($key->getMonth()+1)]++;
			}
			else
			{
				$dataArray[$key->getYear()]=$this->initalizeNewYear();
				$dataArray[$key->getYear()][($key->getMonth()+1)]++;
			}
		}
		$res=new Response();
        $res->setStatusCode(200);
        $res->setContent(json_encode(array("data"=>$dataArray)));
        $res->headers->set('Content-Type', 'application/json');
        return $res;


	}
	private function initalizeNewYear()
	{
		$newYear=array();
		for($i=1; $i<=12; $i++)
		{
			$newYear[$i]=0;
		}
		return $newYear;
	}
}