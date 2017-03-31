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
class InvoiceController extends Controller
{
	public function getAdminListAction()
	{
		$em = $this->getDoctrine()->getManager();
		$projects = $em->getRepository("AcmtoolAppBundle:Project")->findAll();
		$mess=[];
		$i=0;
		$unbilledTicket = [];
		foreach ($projects as $key) {
			$invoiceItem = array();
			$est = 0;
			$rt = 0;
			$bt = 0;
			$firstDate= new \DateTime();
			$count = 0;
			foreach ($key->getTickets() as $ticket) {
				if($ticket->getStatus() == TicketStatus::DONE && !$ticket->getIsBilled())
				{
					$count++;
					$est += $ticket->getEstimation();
					$rt += $ticket->getRealtime();
					if($ticket->getEstimation() >= $ticket->getRealtime())
					{
						$bt +=$ticket->getRealtime();
					}
					else
						$bt+=$ticket->getEstimation();
					if($firstDate > $ticket->getFinisheddate())
						$firstDate=$ticket->getFinisheddate();
				}
			}
			if($count > 0)
			{
				if($key->getOwner()->getCurrency()!=null)
					$currency = $key->getOwner()->getCurrency();
				else
					$currency = ConstValues::DEFAULTCURRENCY;
				if($key->getRate() != null)
					$totalAmmount = $key->getRate() * $bt;
				$invoiceItem = array("project_id"=>$key->getDisplayId(),"project_name"=>$key->getName(),
					"company_name"=>$key->getOwner()->getCompanyname(),"estimation"=>$est,"billed_time"=>$bt,
					"realtime"=>$rt,"ammount"=>$totalAmmount,"creation_date"=>date_format($firstDate, 'Y-m-d'),"currency"=>$currency);
				$unbilledTicket[$i] = $invoiceItem;
				$i++;
			}

		}
		$invoices = array_reverse($em->getRepository("AcmtoolAppBundle:Invoice")->findAll());
		$unpaidInvoices=[];
		$paidInvoices=[];
		$unpaidCount=0;
		$paidcount=0;
		foreach ($invoices as $key) {
			if($key->getPaied() && $paidcount<3)
			{
				$paidInvoices[$paidcount] = $this->createInvoiceItem($key);
				$paidcount++;
			}
			if(!$key->getPaied())
			{
				$unpaidInvoices[$unpaidCount] = $this->createInvoiceItem($key);
				$unpaidCount++;
			}
		}
		$mess["unbilledTicket"]=$unbilledTicket;
		$mess["unpaidInvoices"]=$unpaidInvoices;
		$mess["paidInvoices"]=$paidInvoices;
		return new Response(json_encode($mess),200);
	}
	public function getInvoiceListAction($year,$month)
	{
		$em = $this->getDoctrine()->getManager();
		if($this->get('security.context')->isGranted('ROLE_ADMIN'))
		{
			$startDate = new \DateTime();
			$endDate = new \DateTime();
			$startDate = $startDate->setDate($year,$month,1);
			$endmonth = $month + 1;
			$endDate =  $endDate->setDate($year,$endmonth,1);
			$invoices = $em->createQuery("SELECT i FROM AcmtoolAppBundle:Invoice i WHERE i.paied=:paid AND i.endDate BETWEEN :startDate AND :endDate")
				->setParameter("endDate",$endDate)
				->setParameter("startDate",$startDate)
				->setParameter("paid",true)
				->getResult();
		}
		if($this->get('security.context')->isGranted('ROLE_CUSTOMER') || $this->get('security.context')->isGranted('ROLE_CUSER'))
		{
			$user=$this->get("security.context")->getToken()->getUser();
			if($this->get('security.context')->isGranted('ROLE_CUSER'))
				$user=$user->getCompany();
			$invoices = $em->createQuery("SELECT i FROM AcmtoolAppBundle:Invoice i WHERE i.year=:year AND i.client=:user")
				->setParameter("year",$year)
				->setParameter("user",$user)
				->getResult();
		}
		$mess =[];
		$i = 0;
		if(count($invoices) > 0)
		{
			$paid=[];
			$unpaid=[];
			foreach (array_reverse($invoices) as $key) {
				$currency = ConstValues::DEFAULTCURRENCY;
				if($key->getClient()->getCurrency()!=null)
					$currency = $key->getClient()->getCurrency();
				$data = array("id"=>$key->getDisplayId(),"project_name"=>$key->getProject()->getName(),
					"client"=>$key->getClient()->getCompanyname(),"date"=>date_format($key->getEndDate(),"Y-m-d"),"billed_time"=>$key->getBt(),
					"amount"=>$key->getAmount(),"currency"=>$currency,"paid"=>$key->getPaied(),"billedFrom"=>$key->getBilledFrom());
				if($key->getPaied())
					array_push($paid, $data);
				else
					array_push($unpaid, $data);
			}
			$mess = array_merge($unpaid,$paid);
		}
		return new Response(json_encode($mess),200);

	}
	public function getInvoiceDetailsAction($invoiceId)
	{
		$em = $this->getDoctrine()->getManager();

		if($this->get('security.context')->isGranted('ROLE_ADMIN'))
		{
			$invoice = $em->getRepository("AcmtoolAppBundle:Invoice")->findOneByDisplayid($invoiceId);
		}
		if($this->get('security.context')->isGranted('ROLE_CUSTOMER') || $this->get('security.context')->isGranted('ROLE_CUSER'))
		{
			$user=$this->get("security.context")->getToken()->getUser();
			if($this->get('security.context')->isGranted('ROLE_CUSER'))
				$user=$user->getCompany();
			$invoice = $em->getRepository("AcmtoolAppBundle:Invoice")->findOneBy(array("displayid"=>$invoiceId,"client"=>$user));
		}
		if($invoice)
		{
			$client = $invoice->getClient();
			$project = $invoice->getProject();
			if($client->getTax()!=null)
				$tax = $client->getTax();
			else
				$tax = ConstValues::DEFAULTTAX;
			if($invoice->getDiscount() != null)
				$discount = $invoice->getDiscount();
			else
				$discount = 0;
			$currency = ConstValues::DEFAULTCURRENCY;
			if($invoice->getBilledFrom() != null)
				$billedFrom = $invoice->getBilledFrom();
			else
				$billedFrom = ConstValues::DEFAULTBILLEDFROM;
			if( $client->getCurrency()!=null)
				$currency = $client->getCurrency();
			setlocale(LC_MONETARY, 'de_DE');
			$subTotal = $invoice->getAmount() - $discount;
			$taxamount = ($subTotal/100)*$tax;
			$grandTotal = $subTotal + $taxamount;
			$clientID = $client->getId();
			$vat = $client->getVat();
			$address = array("address"=>$client->getAddress()->getAddress(),"zipcode"=>$client->getAddress()->getZipcode(),"city"=>$client->getAddress()->getCity(),"country"=>$client->getAddress()->getCountry(),"state"=>$client->getAddress()->getState());
			$data = array("id"=>$invoice->getDisplayId(),"description"=>$invoice->getDiscription(),
					"client"=>$invoice->getClient()->getCompanyname(),"enddate"=>date_format($invoice->getEndDate(),"Y-m-d"),
					"startdate"=>date_format($invoice->getCreationDate(),"Y-m-d"),"limitdate"=>date_format($invoice->getLimitDate(),"Y-m-d"),"billed_time"=>$invoice->getBt(),
					"amount"=>$this->formatNumber($invoice->getAmount()),"discount"=>$this->formatNumber($discount),"subTotal"=>$this->formatNumber($subTotal),"tax"=>$tax,"up"=>$this->formatNumber($invoice->getUp()),"grandTotal"=>$this->formatNumber($grandTotal),"taxamount"=>$this->formatNumber($taxamount),
					"currency"=>$currency,"paid"=>$invoice->getPaied(),"address"=>$address,"client_id"=>$clientID,'vat'=>$vat,"billedFrom"=>$billedFrom);
			return new Response(json_encode($data),200);
		}
		else
			return new Response("Bad request",400);

	}
	private function formatNumber($number)
	{
		return number_format($number,2,',','.');
	}
	public function getInvoiceReportAction($invoice,$id)
	{
		$tickets = [];
		$em = $this->getDoctrine()->getManager();
		if($invoice == "true")
		{
			if($this->get('security.context')->isGranted('ROLE_ADMIN'))
			{
				$invoice = $em->getRepository("AcmtoolAppBundle:Invoice")->findOneByDisplayid($id);
			}
			if($this->get('security.context')->isGranted('ROLE_CUSTOMER') || $this->get('security.context')->isGranted('ROLE_CUSER'))
			{
				$user=$this->get("security.context")->getToken()->getUser();
				if($this->get('security.context')->isGranted('ROLE_CUSER'))
					$user=$user->getCompany();
				$invoice = $em->getRepository("AcmtoolAppBundle:Invoice")->findOneBy(array("displayid"=>$id,"client"=>$user));
			}
			if($invoice)
			{
				$tickets = $invoice->getTickets();
			}
		}
		else
		{
			$user=$this->get("security.context")->getToken()->getUser();
			$project = $em->getRepository("AcmtoolAppBundle:Project")->getProjectByLoggedUser($user,$id);
			foreach ($project->getTickets() as $ticket) {
				if($ticket->getStatus() == TicketStatus::DONE && !$ticket->getIsBilled())
				{
					array_push($tickets, $ticket);
				}
			}
		}
		$mess=array();
		if(count($tickets) > 0)
		{

            $i=0;
            $mess["totalestimated"]=0;
            $mess["totalrealtime"]=0;
            $mess["btime"]=0;
            $mess["data"]=array();
            foreach ($tickets as $ticket) {
               $tasks=$ticket->getTasks();
                if(count($tasks)>0)
                {
                    $ticketData=array("id"=>$ticket->getDiplayId(),"title"=>$ticket->getTitle(),"status"=>$ticket->getStatus());
                    $ticketData["payed"]=false;
                    $ticketData["billed"]=false;
                    $ticketData["open"]=false;
                    if($ticket->getIsPayed())
                    {
                        $ticketData["payed"]=true;
                    }
                    elseif($ticket->getIsBilled())
                    {
                        $ticketData["billed"]=true;
                    }
                    else
                        $ticketData["open"]=true;
                   $tasksdata=array();
                   $sum=0;
                   $j=0;
                   $sumestimated=0;
                   foreach ($tasks as $key) {
                       $data=array("id"=>$key->getId(),"title"=>$key->getTitle(),"estimation"=>$key->getEstimation(),
                        "realtime"=>$key->getRealtime(),"date"=>date_format($key->getFinishdate(), 'Y-m-d'));
                       $sum+=$key->getRealtime();
                       $sumestimated+=$key->getEstimation();
                       $tasksdata[$j]=$data;
                       $j++;

                   }
                   $mess["totalrealtime"]+=$sum;
                   $mess["totalestimated"]+=$sumestimated;
                   if($sum>$sumestimated)
                   {
                        $mess["btime"]+=$sumestimated;
                        $ticketData["btime"]=$sumestimated;
                   }    
                    else
                    {
                        $mess["btime"]+=$sum;
                        $ticketData["btime"]=$sum;
                    }
                        
                   $ticketData["totalhours"]=$sum;
                   $ticketData["totalestimatedhours"]=$sumestimated;
                   $ticketData["stories"]=$tasksdata;
                   $mess["data"][$i]=$ticketData;
                   $i++;
                }  
            }
		}
		$res=new Response();
        $res->setStatusCode(200);
        $res->setContent(json_encode($mess));
        $res->headers->set('Content-Type', 'application/json');
        return $res;
	}
	public function markInvoiceasPaidAction($invoiceId)
	{
		$em = $this->getDoctrine()->getManager();
		$invoice = $em->getRepository("AcmtoolAppBundle:Invoice")->findOneByDisplayid($invoiceId);
		if($invoice)
		{
			$invoice->setPaied(true);
			foreach ($invoice->getTickets() as $key) {
				$key->setIsPayed(true);
			}
			$cumultiveHours = 0;
			$client = $invoice->getClient();
			$referer = $client->getReferencedBy();
			if($referer != null)
			{
				$keyaccount = $client->getKeyAccount();
				$cumultaives = $em->createQuery("SELECT c FROM AcmtoolAppBundle:CumulativeHours c WHERE c.customer=:client AND c.referer=:referer")
					->setParameter("client",$client)
					->setParameter("referer",$referer)
					->getResult();
				if(count($cumultaives) > 0)
				{
					$item = $cumultaives[0];
					$cumultiveHours = $item->getTotalHours();
					$item->setTotalHours($cumultiveHours + $invoice->getBt());
				}
				else
				{
					$cumultive  = new CumulativeHours();
					$cumultive->setTotalHours($invoice->getBt());
					$cumultive->setCustomer($client);
					$cumultive->setReferer($referer);
					$em->persist($cumultive);
				}
				$commission = new Commission();
				$commission->setOwner($referer);
				$today = new \DateTime();
				$commission->setCreationDate($today);
				$commission->setRange1Rate(ConstValues::COMRATE1);
				$commission->setRange2Rate(ConstValues::COMRATE2);
				$commission->setRange3Rate(ConstValues::COMRATE3);
				$up = $invoice->getUp();
				if($cumultiveHours <= ConstValues::HOURRANGE1)
				{
					if($cumultiveHours + $invoice->getBt() <= ConstValues::HOURRANGE1)
					{
						$commission->setRange1($invoice->getBt());
					}
					else
					{
						$range1Remains = ConstValues::HOURRANGE1 - $cumultiveHours;
						$commission->setRange1($range1Remains);
						$commission->setRange2($invoice->getBt()-$range1Remains);
					}
				}
				elseif ($cumultiveHours<=ConstValues::HOURRANGE2) {
					if($cumultiveHours + $invoice->getBt() <= ConstValues::HOURRANGE2)
					{
						$commission->setRange2($invoice->getBt());
					}
					else
					{
						$range2Remains = ConstValues::HOURRANGE2 - $cumultiveHours;
						$commission->setRange2($range2Remains);
						$commission->setRange3($invoice->getBt()-$range2Remains);
					}
				}
				else
				{
					$commission->setRange3($invoice->getBt());
				}
				$commission->setRange1Amount($this->ComissionFormula($up,$commission->getRange1(),ConstValues::COMRATE1));
				$commission->setRange2Ammount($this->ComissionFormula($up,$commission->getRange2(),ConstValues::COMRATE2));
				$commission->setRange3Ammount($this->ComissionFormula($up,$commission->getRange3(),ConstValues::COMRATE3));
				if($referer->getId() == $client->getKeyAccount()->getCredentials()->getId())
				{
					$commission->setManagementAmount($this->ComissionFormula($up,$invoice->getBt(),ConstValues::MANGERATE));
					$commission->setRangePM(ConstValues::MANGERATE);

				}
				$commission->setInvoice($invoice);
				$invoice->addCommission($commission);
				$em->persist($commission);
			}
			$em->flush();
			return new Response("Invoice marked as payed",200);
		}
		else
			return new Response("Bad request",400);
	}
	private function ComissionFormula($up,$hours,$rate)
	{
		return ($up * $hours * $rate)/100;
	}
	private function createInvoiceItem($item)
	{
		if($item->getCurrency()!=null)
			$currency = $item->getCurrency();
		else
			$currency = ConstValues::DEFAULTCURRENCY;
		if($item->getBilledFrom() != null)
			$billedFrom = $item->getBilledFrom();
		else
			$billedFrom = ConstValues::DEFAULTBILLEDFROM;
		return array("id"=>$item->getDisplayId(),"project_id"=>$item->getProject()->getDisplayId(),"project_name"=>$item->getProject()->getName(),
			"company_name"=>$item->getClient()->getCompanyname(),"billed_time"=>$item->getBt(),"ammount"=>$item->getAmount(),
			"creation_date"=>date_format($item->getCreationDate(), 'Y-m-d'),"currency"=>$currency,"billedFrom"=>$billedFrom);
	}
	public function createInvoiceAction($project_id)
	{
		$em = $this->getDoctrine()->getManager();$request = $this->get('request');
		$user=$this->get("security.context")->getToken()->getUser();
        $project=$em->getRepository("AcmtoolAppBundle:Project")->getProjectByLoggedUser($user,$project_id);
        if($project)
        {
        	$invoice = new Invoice();
        	$today = new \DateTime();
        	$description = $project->getName()." (for details see the attached report)";
        	$invoice->setDiscription($description);
        	$invoice->setProject($project);
        	$invoice->setClient($project->getOwner());
        	$invoice->setUp($project->getRate());
        	$periode = 'P'.ConstValues::INVOICELIMITDAYS.'D';
        	$invoice->setEndDate($today);
        	$limitDate =  new \DateTime();
        	$invoice->setLimitDate($limitDate->add(new \DateInterval($periode)));
        	$year = $today->format("Y");
        	$shortyear = $today->format("y");
        	$invoice->setYear($year);
        	$firstDate= $today;
        	$bt = 0;
        	if($project->getOwner()->getBilledFrom()!=null)
        		$billedFrom = $project->getOwner()->getBilledFrom();
        	else
        		$billedFrom = ConstValues::DEFAULTBILLEDFROM;
        	if($project->getOwner()->getCurrency() != null)
        		$invoice->setCurrency($project->getOwner()->getCurrency());
        	else
        		$invoice->setCurrency(ConstValues::DEFAULTCURRENCY);
        	foreach ($project->getTickets() as $ticket) {
        		if($ticket->getStatus() == TicketStatus::DONE && !$ticket->getIsBilled())
				{
					if($ticket->getEstimation() >= $ticket->getRealtime())
					{
						$bt +=$ticket->getRealtime();
					}
					else
						$bt+=$ticket->getEstimation();
					if($firstDate > $ticket->getFinisheddate())
						$firstDate=$ticket->getFinisheddate();
					$ticket->setIsBilled(true);
					$invoice->addTicket($ticket);
					$ticket->setInvoice($invoice);
				}
				
				
        	}
        	$invoice->setAmount($bt*$project->getRate());
			$invoice->setBt($bt);
			$invoice->setCreationDate($firstDate);
			$totalcount=$em->createQuery("SELECT COUNT(i) FROM AcmtoolAppBundle:Invoice i WHERE i.year=:year AND i.billedFrom=:from")
				->setParameter("year",$year)
				->setParameter("from",$billedFrom)
        		->getSingleScalarResult();
        	if($billedFrom == ConstValues::GERMANYCODE)
        		$displayId = "FXW".$shortyear.(1001+$totalcount);
        	if($billedFrom == ConstValues::TURKEYCODE)
        		$displayId = "FXW"."TR".$shortyear.($totalcount+1);
        	$invoice->setDisplayid($displayId);
        	$invoice->setBilledFrom($billedFrom);
        	$em->persist($invoice);
        	$em->flush();
        	$this->get("acmtool_app.notifier.handler")->invoiceCreated($invoice);
        	if($project->getOwner()->getCurrency()!=null)
					$currency = $project->getOwner()->getCurrency();
			else
				$currency = ConstValues::DEFAULTCURRENCY;
        	$data=array("id"=>$displayId,"project_name"=>$project->getName(),
					"company_name"=>$invoice->getClient()->getCompanyname(),"billed_time"=>$bt,
					"ammount"=>$invoice->getAmount(),"creation_date"=>date_format($firstDate, 'Y-m-d'),"currency"=>$currency,"billedFrom"=>$billedFrom);
        	return new Response(json_encode($data),200);

        }
        else
        	return new Response("Bad request",400);
	}
}