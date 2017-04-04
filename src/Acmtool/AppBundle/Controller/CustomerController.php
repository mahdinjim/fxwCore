<?php

namespace Acmtool\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Httpfoundation\Response;
use Acmtool\AppBundle\Entity\Customer;
use Acmtool\AppBundle\Entity\KeyAccount;
use Acmtool\AppBundle\Entity\Address;
use Acmtool\AppBundle\Entity\Creds;
use Acmtool\AppBundle\Entity\Titles;
use Acmtool\AppBundle\Entity\ConstValues;
use Acmtool\AppBundle\Entity\SupportedPmTools;
use Acmtool\AppBundle\Entity\LinkedPmTools;
Const TIMEZONE="Europe/Berlin";
Const DEFAULTKEYACCOUNTEMAIL="fd@flexwork.io";
class CustomerController extends Controller
{
    public function CreateAction()
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

            if(!(isset($json->{'password'}) && isset($json->{'login'}) && isset($json->{'email'}) && isset($json->{'name'}) && isset($json->{'surname'}) && isset($json->{'address'}) && isset($json->{'companyname'}) && isset($json->{'address'}->{'address'}) && isset($json->{'address'}->{'city'}) && isset($json->{'address'}->{'zipcode'}) && isset($json->{'address'}->{'country'})))
            {
                $response=new Response('{"errors":"'.ConstValues::INVALIDREQUEST.'"}',400);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
            else
            {
                $keyaccount=null;
                if($this->get('security.context')->isGranted('ROLE_ADMIN') && !isset($json->{"keyaccount_id"}))
                {
                    $response=new Response('{"errors":"'.ConstValues::INVALIDREQUEST.'"}',400);
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                }
                elseif($this->get('security.context')->isGranted('ROLE_ADMIN'))
                {
                    $keyaccount=$em->getRepository("AcmtoolAppBundle:KeyAccount")->findOneById($json->{"keyaccount_id"});
                }
                else
                    $keyaccount=$this->get('security.context')->getToken()->getUser();
                $user=new Customer();
                $creds=new Creds();
                $creds->setLogin($json->{"login"});
                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($creds);
                $password = $encoder->encodePassword($json->{'password'}, $user->getSalt());
                $creds->setPassword($password);
                $creds->setTitle(Titles::Customer);
                $user->setCredentials($creds);
                $user->setEmail($json->{'email'});
                $user->setName($json->{'name'});
                $user->setSurname($json->{'surname'});
                $user->setCompanyName($json->{'companyname'});
                $user->setPhonecode($json->{'phonecode'});
                $user->setTelnumber($json->{'telnumber'});
                if(isset($json->{'vat'}))
                    $user->setVat($json->{'vat'});
                if(isset($json->{"createdByPartner"}))
                    $user->setReferencedBy($keyaccount->getCredentials());
                if(isset($json->{"managedbyPartner"}))
                {
                    if($json->{"managedbyPartner"} && $keyaccount->getCanmanage())
                    {
                        $user->setKeyaccount($keyaccount);
                    }
                    else
                    {
                        $defaultkeyaccount=$em->getRepository("AcmtoolAppBundle:KeyAccount")->findOneByEmail(DEFAULTKEYACCOUNTEMAIL);
                        $user->setKeyaccount($defaultkeyaccount);
                    }
                }
                else
                {
                     $user->setKeyAccount($keyaccount);
                }
                date_default_timezone_set('UTC');
                $user->setDay(date('d'));
                $user->setMonth(date("m"));
                $user->setYear(date("Y"));
                if(isset($json->{"tax"}))
                    $user->setTax($json->{"tax"});
                else
                    $user->setTax(ConstValues::DEFAULTTAX);
                if(isset($json->{"currency"}))
                    $user->setCurrency($json->{"currency"});
                else
                    $user->setCurrency(ConstValues::DEFAULTCURRENCY);
                if(isset($json->{"billedfrom"}))
                    $user->setBilledFrom($json->{"billedfrom"});
                else
                    $user->setBilledFrom(ConstValues::DEFAULTBILLEDFROM);
                $domain = substr(strrchr($json->{"email"}, "@"), 1);
                $user->setCompnayDomain($domain);
                $format = 'Y-m-d';
                $startingdate = new \DateTime('UTC');
                $user->setStartingdate($startingdate);
                $address=new Address();
                $address->setAddress($json->{'address'}->{'address'});
                $address->setZipCode($json->{'address'}->{'zipcode'});
                $address->setCity($json->{'address'}->{'city'});
                $address->setCountry($json->{'address'}->{'country'});
                if(isset($json->{'address'}->{'state'}))
                    $address->setState($json->{'address'}->{'state'});
                $user->setAddress($address);
                $validator = $this->get('validator');
                $errorList = $validator->validate($user);
                $crederrorlist=$validator->validate($user->getCredentials());
                $addresserrorlist=$validator->validate($address);

                if (count($errorList) > 0 || count($crederrorlist)>0 || count($addresserrorlist) >0) {
                    $response= new Response();
                    $response->setStatusCode(400);
                    $errosmsg=array();
                    foreach ($errorList as $error) {
                        array_push($errosmsg, $error->getMessage());
                    }
                    foreach ($crederrorlist as $error) {
                         array_push($errosmsg, $error->getMessage());
                    }
                    foreach ($addresserrorlist as $error) {
                        array_push($errosmsg, $error->getMessage());
                    }
                    $response->setContent(json_encode(array("errors"=>$errosmsg)));
                    return $response;
                } else {
                    $em->persist($user);
                    $em->flush();
                    $this->get("acmtool_app.notifier.handler")->clientAdded($user,$json->{'password'},$json->{"isSent"});
                    $res=new Response();
                    $res->setStatusCode(200);
                    $res->setContent(ConstValues::CUSCREATED);
                    return $res;
                }

            }
        }
    }

    public function UpdateAction()
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
            if(!(isset($json->{'id'}) && isset($json->{'login'}) && isset($json->{'email'}) && isset($json->{'name'}) && isset($json->{'surname'}) && isset($json->{'address'}) && isset($json->{'companyname'}) && isset($json->{'address'}->{'address'}) && isset($json->{'address'}->{'city'}) && isset($json->{'address'}->{'zipcode'}) && isset($json->{'address'}->{'country'})))
            {
                $response=new Response('{"err":"'.ConstValues::INVALIDREQUEST.'"}',400);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
            else
            {
                if ($this->get('security.context')->isGranted('ROLE_CUSTOMER') && $this->get('security.context')->getToken()->getUser()->getId()!=$json->{'id'})  {
                        $response=new Response();
                        $response->setStatusCode(403);
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;
                }
                else
                {
                    $user=$em->getRepository("AcmtoolAppBundle:Customer")->findOneById($json->{'id'});
                    if($user instanceOf Customer){
                        if(isset($json->{"keyaccount_id"}))
                        {
                            $keyaccount=$em->getRepository("AcmtoolAppBundle:KeyAccount")->findOneById($json->{"keyaccount_id"});
                            $user->setKeyaccount($keyaccount);
                        }
                        if(isset($json->{"managedbyPartner"}))
                        {
                            if($json->{"managedbyPartner"})
                            {
                                $currentuser = $this->get('security.context')->getToken()->getUser();
                                if($currentuser instanceOf KeyAccount)
                                    $user->setKeyaccount($currentuser);
                            }
                            else
                            {
                                $defaultkeyaccount=$em->getRepository("AcmtoolAppBundle:KeyAccount")->findOneByEmail(DEFAULTKEYACCOUNTEMAIL);
                                $user->setKeyaccount($defaultkeyaccount);
                            }
                        }
                        if($user->getCredentials()->getLogin()!=$json->{"login"})
                            $user->getCredentials()->setLogin($json->{"login"});
                        if(isset($json->{'password'}))
                        {
                            $factory = $this->get('security.encoder_factory');
                            $encoder = $factory->getEncoder($user->getCredentials());
                            $password = $encoder->encodePassword($json->{'password'}, $user->getSalt());
                            $user->getCredentials()->setPassword($password);
                        }
                        $oldEmail=$user->getEmail();
                        if($user->getEmail()!=$json->{'email'})
                        {
                            $user->setEmail($json->{'email'});
                            $domain = substr(strrchr($json->{"email"}, "@"), 1);
                            $user->setCompnayDomain($domain);
                        }
                        $user->setName($json->{'name'});
                         $user->setSurname($json->{'surname'});
                        if(isset($json->{'vat'}))
                            $user->setVat($json->{'vat'});
                        $user->setPhonecode($json->{'phonecode'});
                        $user->setTelnumber($json->{'telnumber'});
                        $user->setCompanyName($json->{'companyname'});
                        if(isset($json->{"tax"}))
                            $user->setTax($json->{"tax"});
                        else
                            $user->setTax(ConstValues::DEFAULTTAX);
                        if(isset($json->{"currency"}))
                            $user->setCurrency($json->{"currency"});
                        else
                            $user->setCurrency(ConstValues::DEFAULTCURRENCY);
                        if(isset($json->{"billedfrom"}))
                            $user->setBilledFrom($json->{"billedfrom"});
                        else
                            $user->setBilledFrom(ConstValues::DEFAULTBILLEDFROM);
                        $address=new Address();
                        $user->getAddress()->setAddress($json->{'address'}->{'address'});
                        $user->getAddress()->setZipCode($json->{'address'}->{'zipcode'});
                        $user->getAddress()->setCity($json->{'address'}->{'city'});
                        $user->getAddress()->setCountry($json->{'address'}->{'country'});
                        if(isset($json->{'address'}->{'state'}))
                            $user->getAddress()->setState($json->{'address'}->{'state'});
                        $validator = $this->get('validator');
                        $errorList = $validator->validate($user);
                        $crederrorlist=$validator->validate($user->getCredentials());
                        $addresserrorlist=$validator->validate($user->getAddress());

                        if (count($errorList) > 0 || count($crederrorlist)>0 || count($addresserrorlist) >0) {
                            $response= new Response();
                            $response->setStatusCode(400);
                            $errosmsg=array();
                            foreach ($errorList as $error) {
                                array_push($errosmsg, $error->getMessage());
                            }
                            foreach ($crederrorlist as $error) {
                                 array_push($errosmsg, $error->getMessage());
                            }
                            foreach ($addresserrorlist as $error) {
                                array_push($errosmsg, $error->getMessage());
                            }
                            $response->setContent(json_encode(array("errors"=>$errosmsg)));
                            return $response;
                        } else {
                            $em->flush();
                            $this->get("acmtool_app.notifier.handler")->clientInfoUpdated($user,$oldEmail);
                            $res=new Response();
                            $res->setStatusCode(200);
                            $res->setContent(ConstValues::CUSUPDATED);
                            return $res;
                        }
                    }
                    else
                    {
                        $response=new Response('{"err":"'.ConstValues::INVALIDREQUEST.'"}',400);
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;
                    }

                }
            }
        }
    }

    public function DeleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user=$em->getRepository("AcmtoolAppBundle:Customer")->findOneById($id);
        if($user){
            $em->remove($user);
            $em->flush();
            $this->get("acmtool_app.notifier.handler")->clientDeleted($user);
            $res=new Response();
            $res->setStatusCode(200);
            $res->setContent(ConstValues::CUSDELETED);
            return $res;
        }
        else
        {
            $response=new Response('{"err":"'.ConstValues::INVALIDREQUEST.'"}',400);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
    }
    public function acceptContractAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $customer=$em->getRepository("AcmtoolAppBundle:Customer")->findOneById($id);
        if($customer)
        {
            $customer->setSignedContract(true);
            $format = 'Y-m-d';
            $startingdate = new \DateTime('UTC');
            $customer->setSignaturedate($startingdate);
            $em->flush();
            $res=new Response();
            $res->setStatusCode(200);
            $res->setContent("Contract signed");
            return $res;
        }
        else
        {
            $response=new Response('{"error":"'.ConstValues::INVALIDREQUEST.'"}',400);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
    }
    public function ListAction($page)
    {

        $em = $this->getDoctrine()->getManager();
        $keyaccount = null;
        $isPartner = false;
        if($this->get('security.context')->isGranted("ROLE_ADMIN"))
        {
            $totalpages=ceil($em->createQuery("SELECT COUNT(t) FROM AcmtoolAppBundle:Customer t")
            ->getSingleScalarResult()/10);
            $start=ConstValues::COUNT*($page-1);
            $End=ConstValues::COUNT*$page;
            $result=$em->createQuery('select d from AcmtoolAppBundle:Customer d')
                        ->setMaxResults(ConstValues::COUNT)
                        ->setFirstResult($start)
                        ->getResult();
        }
        else
        {
            $keyaccount=$this->get("security.context")->getToken()->getUser();
            $totalpages=ceil($em->getRepository("AcmtoolAppBundle:Customer")->getKeyAccountCustomersCount($keyaccount)/10);
            $start=ConstValues::COUNT*($page-1);
            $End=ConstValues::COUNT*$page;
            if($keyaccount->isPartner())
            {
                $isPartner = true;
                $result = $em->getRepository("AcmtoolAppBundle:Customer")->findByReferencedBy($keyaccount->getCredentials());
            }
            else
                $result=$em->getRepository("AcmtoolAppBundle:Customer")->getCustomersByKeyAccount($keyaccount,$start);
        }
        if(count($result)>0 || count($referencedClients) >0)
        {
            $users=array();
            $i=0;
            foreach ($result as $user) {
                $users[$i] = array('id'=>$user->getId(),"active"=>$user->getIsActive(),
                    "creationdate"=>date_format($user->getStartingdate(), 'Y-m-d'),
                    'username' =>$user->getUsername(),'email'=>$user->getEmail(),
                    'logo'=>$user->getLogo(),"name"=>$user->getName(),
                    "surname"=>$user->getSurname(),"phonecode"=>$user->getPhonecode(),
                    "companyname"=>$user->getCompanyName(),"vat"=>$user->getVat(),
                    "telnumber"=>$user->getTelnumber(),"userNumber"=>count($user->getUsers()),
                    "projectNumber"=>count($user->getProjects()),
                    "address"=>array("address"=>$user->getAddress()->getAddress(),"zipcode"=>$user->getAddress()->getZipcode(),"city"=>$user->getAddress()->getCity(),"country"=>$user->getAddress()->getCountry(),"state"=>$user->getAddress()->getState()),
                    "keyaccount"=>array('id'=>$user->getKeyaccount()->getId(),"name"=>$user->getKeyaccount()->getName(),"surname"=>$user->getKeyaccount()->getSurname(),"photo"=>$user->getKeyaccount()->getPhoto()),
                    "tax"=>$user->getTax(),"currency"=>$user->getCurrency(),"billedFrom"=>$user->getBilledFrom());
                if($keyaccount !=null)
                {
                    if($user->getKeyaccount()->getId()==$keyaccount->getId())
                        $users[$i]['isManager']=true;
                    else
                        $users[$i]['isManager']=false;
                }
                if(count($user->getProjects()) > 0 && $isPartner)
                {
                    $users[$i]['candelete']=false;
                }
                else
                {
                    $users[$i]['candelete']=true;
                }
                $ticketnumber=0;
                foreach ($user->getProjects() as $key) {
                    $ticketnumber+=count($key->getTickets());
                }
                $users[$i]['ticketnumber']=$ticketnumber;
                $i++;

            }
            $messag = array('totalpages' => $totalpages,'current_page'=>$page,'users'=>$users);
            $res=new Response();
            $res->setStatusCode(200);
            $res->setContent(json_encode($messag));
            return $res;
        }
        else
        {
            $mess=array();
            $mess['users']=array();
            $mess['totalpages']=1;
            $mess['current_page']=1;
            $res=new Response();
            $res->setStatusCode(200);
            $res->setContent(json_encode($mess));
            return $res;
        }
    }

    public function DetailsAction($id)
    {
        $em = $this->getDoctrine()->getManager();
       if ($this->get('security.context')->isGranted('ROLE_CUSTOMER') && $this->get('security.context')->getToken()->getUser()->getId()!=$id)  {
            $response=new Response();
            $response->setStatusCode(403);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        else
        {
            $user=$em->getRepository("AcmtoolAppBundle:Customer")->findOneById($id);
            if ($this->get('security.context')->isGranted('ROLE_KEYACCOUNT') && $user->getKeyaccount()->getId()!=$this->get('security.context')->getToken()->getUser()->getId()) {
                $response=new Response();
                $response->setStatusCode(403);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
            if($user)
            {
                $UserInfo = array('id'=>$user->getId(),'username' =>$user->getUsername(),'email'=>$user->getEmail(),
                    'logo'=>$user->getLogo(),"name"=>$user->getName(),"surname"=>$user->getSurname(),
                    "companyname"=>$user->getCompanyName(),"vat"=>$user->getVat(),"tax"=>$user->getTax(),"currency"=>$user->getCurrency(),"billedFrom"=>$user->getBilledFrom(),
                    "tel"=>$user->getTelnumber(),"address"=>array("address"=>$user->getAddress()->getAddress(),"zipcode"=>$user->getAddress()->getZipcode(),"city"=>$user->getAddress()->getCity(),"country"=>$user->getAddress()->getCountry(),"state"=>$user->getAddress()->getState()),
                    "keyaccount"=>array('id'=>$user->getKeyaccount()->getId(),"name"=>$user->getKeyaccount()->getName(),"surname"=>$user->getKeyaccount()->getSurname()));
                $res=new Response();
                $res->setStatusCode(200);
                $res->setContent(json_encode($UserInfo));
                return $res;
            }
            else
            {
                $response=new Response('{"err":"'.ConstValues::INVALIDREQUEST.'"}',400);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }


        }
    }
    public function desActivateClientAction($id,$activate)
    {
         $em = $this->getDoctrine()->getManager();
         if($this->get('security.context')->isGranted('ROLE_KEYACCOUNT') || $this->get('security.context')->isGranted('ROLE_ADMIN') )
         {
             $user=$em->getRepository("AcmtoolAppBundle:Customer")->findOneById($id);
             if($user)
             {
                if($activate==="true")
                {
                     $user->setIsActive(true);
                     foreach ($user->getUsers() as $key) {
                        $key->setIsActive(true);
                     }
                }
                   
                else
                {
                     $user->setIsActive(false);
                     foreach ($user->getUsers() as $key) {
                        $key->setIsActive(false);
                     }
                }
                $em->flush();
                $response=new Response('{"message":"account status changed successfully"}',200);
                $response->headers->set('Content-Type', 'application/json');
                return $response;  
             }
            else
            {
                $response=new Response('{"err":"'.ConstValues::INVALIDREQUEST.'"}',400);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
            
         }
          else
            {
                $response=new Response('{"err":"unauthorized"}',403);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
    }
    public function linkPmToolAction($id,$pmTool)
    {
        $em = $this->getDoctrine()->getManager();
        $tools = SupportedPmTools::getAll();
        $client = $em->getRepository("AcmtoolAppBundle:Customer")->findOneById($id);
        if($client)
        {
            if(in_array($pmTool, $tools))
            {
                $tools = $em->getRepository("AcmtoolAppBundle:LinkedPmTools")->findByClient($client);
                $found = false;
                foreach ($tools as $key) {
                    if($key->getToolname() == $pmTool)
                    {
                        $found = true;
                    }
                        
                }
                if($found)
                    return new Response("Tool already linked",200);
                else
                {
                    $tool = new LinkedPmTools();
                    $tool->setToolname($pmTool);
                    $tool->setClient($client);
                    $em->persist($tool);
                    $em->flush();
                    return new Response("Tool is linked",200);
                }
            }
            else
                return new Response("tool not supported",401);
        }
        else
            return new Response("bad request",400);
        
    }
    public function unlikPmToolAction($id,$pmTool)
    {
        $tools = SupportedPmTools::getAll();
        $em = $this->getDoctrine()->getManager();
        $client = $em->getRepository("AcmtoolAppBundle:Customer")->findOneById($id);
        if($client)
        {
            if(in_array($pmTool, $tools))
            {
                $tools = $em->getRepository("AcmtoolAppBundle:LinkedPmTools")->findByClient($client);
                $found = false;
                foreach ($tools as $key) {
                    if($key->getToolname() == $pmTool)
                    {
                        $found = true;
                        $em->remove($key);
                        $em->flush();
                    }
                        
                }
                return new Response("Tool unLinked",200);
                
            }
            else
                return new Response("tool not supported",401);
        }
        else
            return new Response("bad request",400);
    }

}
