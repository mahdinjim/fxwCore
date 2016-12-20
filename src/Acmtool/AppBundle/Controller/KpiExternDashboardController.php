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
		/*foreach ($clients as $key) {
			if(array_key_exists($key->getYear(),$dataArray))
			{
				$dataArray[$key->getYear()][($key->getMonth())]++;
			}
			else
			{
				$dataArray[$key->getYear()]=$this->initalizeNewYear();
				$dataArray[$key->getYear()][($key->getMonth())]++;
			}
		}*/
		$res=new Response();
		$data='{"data":[{"1":2},{"2":4},{"3":8}]}';
        $res->setStatusCode(200);
        $res->setContent($data);
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