<?php

namespace Acmtool\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Httpfoundation\Response;
use Acmtool\AppBundle\Entity\Customer;
use Acmtool\AppBundle\Entity\Address;
use Acmtool\AppBundle\Entity\Creds;
use Acmtool\AppBundle\Entity\Titles;
use Acmtool\AppBundle\Entity\ConstValues;

Const TIMEZONE="Europe/Berlin";
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
                $user->setKeyAccount($keyaccount);
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
                date_default_timezone_set('UTC');
                $user->setDay(date('d'));
                $user->setMonth(date("m"));
                $user->setYear(date("Y"));
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
                     if($json->{"isSent"})
                        $this->get("acmtool_app.email.notifier")->notifyAddedTeamMember($json->{'email'},$json->{'password'},$json->{"login"},$json->{'name'},$json->{'surname'});
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
                        if($user->getCredentials()->getLogin()!=$json->{"login"})
                            $user->getCredentials()->setLogin($json->{"login"});
                        if(isset($json->{'password'}))
                        {
                            $factory = $this->get('security.encoder_factory');
                            $encoder = $factory->getEncoder($user->getCredentials());
                            $password = $encoder->encodePassword($json->{'password'}, $user->getSalt());
                            $user->getCredentials()->setPassword($password);
                        }
                        if($user->getEmail()!=$json->{'email'})
                            $user->setEmail($json->{'email'});
                        $user->setName($json->{'name'});
                         $user->setSurname($json->{'surname'});
                        if(isset($json->{'vat'}))
                            $user->setVat($json->{'vat'});
                        $user->setPhonecode($json->{'phonecode'});
                        $user->setTelnumber($json->{'telnumber'});
                        $user->setCompanyName($json->{'companyname'});
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
            $result=$em->getRepository("AcmtoolAppBundle:Customer")->getCustomersByKeyAccount($keyaccount,$start);
        }
        if(count($result)>0)
        {
            $users=array();
            $i=0;
            foreach ($result as $user) {
                $users[$i] = array('id'=>$user->getId(),"creationdate"=>date_format($user->getStartingdate(), 'Y-m-d'),'username' =>$user->getUsername(),'email'=>$user->getEmail(),'logo'=>$user->getLogo(),"name"=>$user->getName(),"surname"=>$user->getSurname(),"phonecode"=>$user->getPhonecode(),"companyname"=>$user->getCompanyName(),"vat"=>$user->getVat(),"telnumber"=>$user->getTelnumber(),"userNumber"=>count($user->getUsers()),"projectNumber"=>count($user->getProjects()),"address"=>array("address"=>$user->getAddress()->getAddress(),"zipcode"=>$user->getAddress()->getZipcode(),"city"=>$user->getAddress()->getCity(),"country"=>$user->getAddress()->getCountry(),"state"=>$user->getAddress()->getState()),"keyaccount"=>array('id'=>$user->getKeyaccount()->getId(),"name"=>$user->getKeyaccount()->getName(),"surname"=>$user->getKeyaccount()->getSurname(),"photo"=>$user->getKeyaccount()->getPhoto()));
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
                $UserInfo = array('id'=>$user->getId(),'username' =>$user->getUsername(),'email'=>$user->getEmail(),'logo'=>$user->getLogo(),"name"=>$user->getName(),"surname"=>$user->getSurname(),"logo"=>$user->getLogo(),"companyname"=>$user->getCompanyName(),"vat"=>$user->getVat(),"tel"=>$user->getTelnumber(),"address"=>array("address"=>$user->getAddress()->getAddress(),"zipcode"=>$user->getAddress()->getZipcode(),"city"=>$user->getAddress()->getCity(),"country"=>$user->getAddress()->getCountry(),"state"=>$user->getAddress()->getState()),"keyaccount"=>array('id'=>$user->getKeyaccount()->getId(),"name"=>$user->getKeyaccount()->getName(),"surname"=>$user->getKeyaccount()->getSurname()));
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

}
