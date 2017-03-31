<?php
namespace Acmtool\AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Httpfoundation\Response;
use Acmtool\AppBundle\Entity\TicketStatus;
use Acmtool\AppBundle\Entity\Invoice;
use Acmtool\AppBundle\Entity\ConstValues;
use Acmtool\AppBundle\Entity\Commission;
use Acmtool\AppBundle\Entity\CumulativeHours;
class CommissionController extends Controller
{
	public function getAdminListAction()
	{
		$em = $this->getDoctrine()->getManager();
		$commissions = $em->getRepository("AcmtoolAppBundle:Commission")->findAll();
		$unvoicedcommissions = [];
		$unpaidcommissions = [];
		$paidCommissions = [];
		$paidNumber = 0;
		$mess = array();
		$request = $this->get('request');
		$baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
		foreach (array_reverse($commissions) as $key) {
			if($key->getInvoicefile() == null)
			{
				array_push($unvoicedcommissions, $this->serializeCommission($key,$baseurl));
			}
			elseif(!$key->getPayed())
			{
				array_push($unpaidcommissions, $this->serializeCommission($key,$baseurl));
			}
			else
			{
				if($paidNumber < 3)
				{
					array_push($paidCommissions, $this->serializeCommission($key,$baseurl));
					$paidNumber++;
				}
			}
		}
		$mess["uninvoiced"] = $unvoicedcommissions;
		$mess["unpaid"] = $unpaidcommissions;
		$mess["paid"] = $paidCommissions;
		return new Response(json_encode($mess),200);
	}
	public function uploadInvoiceAction($commission)
	{
		$request = $this->get('request');
		$em = $this->getDoctrine()->getManager();
    	$user=$this->get("security.context")->getToken()->getUser();
        $comm=$em->getRepository("AcmtoolAppBundle:Commission")->findOneById($commission);
    	if($comm && $user->getCredentials()->getId() == $comm->getOwner()->getId())
    	{
	    	$fileBag = $request->files;
			$files=$fileBag->all();
			$filename=str_replace(' ', '', $files['file']->getClientOriginalName());
			$partnerFolder = __DIR__.'/../../../../web'.'/uploads/partners';
			if(!file_exists($partnerFolder))
			{
				mkdir($partnerFolder);
			}
			$userfolder = $partnerFolder."/".$user->getId();
			if(!file_exists($userfolder))
			{
				mkdir($userfolder);
			}
			$path=$userfolder."/".$comm->getId();
			if(file_exists($path))
			{
				$this->deleteFolder($path);
			}
			mkdir($path);
			$filepath=$path."/".$filename;
			if(!file_exists($filepath))
			{
				$files["file"]->move($path, $filename);
				$comm->setInvoicefileName($filename);
				$comm->setInvoiceFile('/uploads/partners/'.$user->getId()."/".$comm->getId()."/".$filename);
				$em->flush();
				$response=new Response("Invoice added",200);
                return $response;

			}
			else
			{
				$response=new Response("Invoice already added",201);
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
	public function payCommissionAction($commission)
	{
		$em = $this->getDoctrine()->getManager();
		$comm=$em->getRepository("AcmtoolAppBundle:Commission")->findOneById($commission);
		if($comm)
		{
			$comm->setPayed(true);
			$em->flush();
			$response=new Response("commission paid",200);
			return $response;
		}
		else
		{
			$response=new Response('{"error":"'.ConstValues::INVALIDREQUEST.'"}',400);
        	$response->headers->set('Content-Type', 'application/json');
        	return $response;
		}
	}
	public function getCommissionListAction($year,$month)
	{
		$em = $this->getDoctrine()->getManager();
		$request = $this->get('request');
		$baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
		if($this->get('security.context')->isGranted('ROLE_ADMIN'))
		{
			$startDate = new \DateTime();
			$endDate = new \DateTime();
			$startDate = $startDate->setDate($year,$month,1);
			$endmonth = $month + 1;
			$endDate =  $endDate->setDate($year,$endmonth,1);
			$commissions = $em->createQuery("SELECT i FROM AcmtoolAppBundle:Commission i WHERE i.payed=:paid AND i.creationDate BETWEEN :startDate AND :endDate")
				->setParameter("endDate",$endDate)
				->setParameter("startDate",$startDate)
				->setParameter("paid",true)
				->getResult();
		}
		if($this->get('security.context')->isGranted('ROLE_KEYACCOUNT'))
		{
			$startDate = new \DateTime();
			$endDate = new \DateTime();
			$startDate = $startDate->setDate($year,1,1);
			$endDate =  $endDate->setDate($year+1,1,1);
			$user=$this->get("security.context")->getToken()->getUser();
			$commissions = $em->createQuery("SELECT i FROM AcmtoolAppBundle:Commission i WHERE i.creationDate BETWEEN :startDate AND :endDate AND i.owner=:user")
				->setParameter("startDate",$startDate)
				->setParameter("endDate",$endDate)
				->setParameter("user",$user->getCredentials())
				->getResult();
		}
		$mess =[];
		$i = 0;
		if(count($commissions) > 0)
		{
			foreach (array_reverse($commissions) as $key) {
				$mess[$i] = $this->serializeCommission($key,$baseurl);
				$i++;
			}
		}
		return new Response(json_encode($mess),200);
	}
	public function calculateCommissionAction($rate,$hours,$includePm)
	{
		$ammount = 0;
		if($hours <= ConstValues::HOURRANGE1)
		{
			$ammount = $this->ComissionFormula($rate,$hours,ConstValues::COMRATE1);
		}
		elseif($hours<=ConstValues::HOURRANGE2)
		{
			$remainhours = $hours - ConstValues::HOURRANGE1;
			$ammount = $this->ComissionFormula($rate,ConstValues::HOURRANGE1,ConstValues::COMRATE1) + $this->ComissionFormula($rate,$remainhours,ConstValues::COMRATE2);
		}
		else
		{
			$remainhours = $hours - ConstValues::HOURRANGE2;
			$range2hours = ConstValues::HOURRANGE2 - ConstValues::HOURRANGE1;
			$ammount = $this->ComissionFormula($rate,ConstValues::HOURRANGE1,ConstValues::COMRATE1) + $this->ComissionFormula($rate,$range2hours,ConstValues::COMRATE2) + $this->ComissionFormula($rate,$remainhours,ConstValues::COMRATE3);
		}
		if($includePm === "true")
		{
			$ammount+=$this->ComissionFormula($rate,$hours,ConstValues::MANGERATE);
		}
		$data=array();
		$data["ammount"] = $this->formatNumber($ammount);
		return new Response(json_encode($data),200);
	}
	private function deleteFolder($dirPath)
	{
		if (! is_dir($dirPath)) {
        	throw new InvalidArgumentException("$dirPath must be a directory");
	    }
	    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
	        $dirPath .= '/';
	    }
	    $files = glob($dirPath . '*', GLOB_MARK);
	    foreach ($files as $file) {
	        if (is_dir($file)) {
	            self::deleteDir($file);
	        } else {
	            unlink($file);
	        }
	    }
	    rmdir($dirPath);
	}
	private function serializeCommission($commission,$baseurl)
	{
		$em = $this->getDoctrine()->getManager();
		$data = array("id"=>$commission->getId(),"ispayed"=>$commission->getPayed(),
			"project"=>$commission->getInvoice()->getProject()->getName(),
			"client"=>$commission->getInvoice()->getClient()->getCompanyname(),
			"date"=>date_format($commission->getCreationDate(), 'Y-m-d'));
		$data["total_hours"] = $this->formatNumber($commission->getRange1() + $commission->getRange2() + $commission->getRange3());
		$data["total_ammount"] = $this->formatNumber($commission->getRange1Amount() + $commission->getRange2Ammount() + $commission->getRange3Ammount() + $commission->getManagementAmount());
		$data["pm_ammount"] = $this->formatNumber($commission->getManagementAmount());
		$refreingamount = $commission->getRange1Amount() + $commission->getRange2Ammount() + $commission->getRange3Ammount();
		$desc = $this->formatNumber($refreingamount)." ".$commission->getInvoice()->getCurrency()." = ";
		$strings = array();
		$commission_details = array();
		if($commission->getRange1Amount() > 0)
		{
			$r1 = "R - ".$this->formatNumber($commission->getRange1Rate())."% - ".$this->formatNumber($commission->getRange1Amount());
			array_push($commission_details, $r1);
		}
		if($commission->getRange2Ammount() > 0)
		{
			$r2 = "R - ".$this->formatNumber($commission->getRange2Rate())."% - ".$this->formatNumber($commission->getRange2Ammount());
			array_push($commission_details, $r2);
		}
		if($commission->getRange3Ammount() > 0)
		{
			$r3 = "R - ".$this->formatNumber($commission->getRange3Rate())."% - ".$this->formatNumber($commission->getRange3Ammount());
			array_push($commission_details, $r3);
		}
		if($commission->getRange3Ammount() > 0)
		{
			$r3 = "R - ".$this->formatNumber($commission->getRange3Rate())."% - ".$this->formatNumber($commission->getRange3Ammount());
			array_push($commission_details, $r3);
		}
		if($commission->getManagementAmount() > 0)
		{
			$r4 = "M - ".$this->formatNumber($commission->getRangePM())."% - ".$this->formatNumber($commission->getManagementAmount());
			array_push($commission_details, $r4);
		}
		if($commission->getRange1() != 0)
		{
			array_push($strings, $this->formatRangeString($commission->getRange1(),$commission->getRange1Amount(),$commission->getRange1Rate()));
		}
		if($commission->getRange2() != 0)
		{
			array_push($strings, $this->formatRangeString($commission->getRange2(),$commission->getRange2Ammount(),$commission->getRange2Rate()));
		}
		if($commission->getRange3() != 0)
		{
			array_push($strings, $this->formatRangeString($commission->getRange3(),$commission->getRange3Ammount(),$commission->getRange3Rate()));
		}
		if($commission->getInvoicefile() != null)
		{
			$data["invoice"] = $baseurl.$commission->getInvoicefile();
			$data["invoice_name"] = $commission->getInvoicefileName();
		}
		$desc.=implode(" + ",$strings);
		$data["ref_ammount"] = $desc;
		$data["details"]=$commission_details;
		$refrer = $em->getRepository("AcmtoolAppBundle:Creds")->getUserByCreds($commission->getOwner());
		$data["partner"] = $refrer->getName()." ".$refrer->getSurname();
		$data["currency"] = $commission->getInvoice()->getCurrency();
		return $data;
	}
	private function formatRangeString($range,$ammount,$rate)
	{
		$desc=" ";
		$desc.=$this->formatNumber($ammount)." for ".$this->formatNumber($range);
		$desc.=" h (".$this->formatNumber($rate)."%)";
		return $desc;
	}
	private function formatNumber($number)
	{
		return number_format($number,2,',','.');
	}
	private function ComissionFormula($up,$hours,$rate)
	{
		return ($up * $hours * $rate)/100;
	}
}