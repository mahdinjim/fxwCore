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
				$dataArray[$key->getYear()][intval($key->getMonth())]++;
			}
			else
			{
				$dataArray[$key->getYear()]=$this->initalizeNewYear();
				$dataArray[$key->getYear()][intval($key->getMonth())]++;
			}
		}
		$mess=array();
		for($i=0;$i<count($dataArray);$i++)
		{
			$dataYear=array();
			$years=array_keys($dataArray);
			$currentYear=$years[$i];
			$dataYear["year"]=strval($currentYear);
			$dataYear["data"]=array();
			
			for($j=0; $j<count($dataArray[$currentYear]); $j++)
			{
				$dataMonth=array();
				$months=array_keys($dataArray[$currentYear]);
				$currentMonth=$months[$j];
				$monthName=$this->getMonthName($currentMonth);
				array_push($dataMonth, array("monthName"=>$monthName,"clientNum"=>$dataArray[$currentYear][$currentMonth]));
				array_push($dataYear["data"], $dataMonth);
			}
			array_push($mess, $dataYear);
		}
		$res=new Response();
        $res->setStatusCode(200);
        $res->setContent(json_encode($mess));
        $res->headers->set('Content-Type', 'application/json');
        return $res;


	}
	private function getMonthName($index)
	{
		$months=["Jan","Feb","Mar","Apr","Mai","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
		return $months[$index-1];
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