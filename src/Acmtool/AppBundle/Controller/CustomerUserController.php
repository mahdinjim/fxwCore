<?php

namespace Acmtool\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Httpfoundation\Response;
use Acmtool\AppBundle\Entity\CustomerUser;
use Acmtool\AppBundle\Entity\Creds;
use Acmtool\AppBundle\Entity\Titles;
use Acmtool\AppBundle\Entity\ConstValues;

//TODO: Add controller management of the keyaccount 
class CustomerUserController extends Controller
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
            if(!(isset($json->{'password'}) && isset($json->{'login'}) && isset($json->{'email'}) && isset($json->{'name'}) && isset($json->{'surname'})))
            {
                $response=new Response('{"err":"'.ConstValues::INVALIDREQUEST.'"}',400);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
            else
            {
                $user=new CustomerUser();
                $creds=new Creds();
                $creds->setLogin($json->{"login"});
                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($creds);
                $password = $encoder->encodePassword($json->{'password'}, $user->getSalt());
                $creds->setPassword($password);
                $creds->setTitle(Titles::CustomerUser);
                $user->setCredentials($creds);
                $user->setEmail($json->{'email'});
                $user->setName($json->{'name'});
                $user->setSurname($json->{'surname'});
                if(isset($json->{"phonenumber"}))
                    $user->setTelnumber($json->{"phonenumber"});
                $user->setTitle($json->{"title"});
                $user->setPhonecode($json->{"phonecode"});
                $user->setCompany($this->get('security.context')->getToken()->getUser());
                $validator = $this->get('validator');
                $errorList = $validator->validate($user);
                $crederrorlist=$validator->validate($creds);
                if (count($errorList) > 0 || count($crederrorlist)>0) {
                    $response= new Response();
                    $response->setStatusCode(400);
                    $errosmsg=array();
                    foreach ($errorList as $error) {
                        array_push($errosmsg, $error->getMessage());
                    }
                    foreach ($crederrorlist as $error) {
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
                    $res->setContent(ConstValues::KEYACREATED);
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
            if(!(isset($json->{'id'}) && isset($json->{'login'}) && isset($json->{'email'}) && isset($json->{'name'}) && isset($json->{'surname'})))
            {
                $response=new Response('{"error":"'.ConstValues::INVALIDREQUEST.'"}',400);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
            else
            {
                if ($this->get('security.context')->isGranted('ROLE_CUSER') && $this->get('security.context')->getToken()->getUser()->getId()!=$json->{'id'})  {
                        $response=new Response();
                        $response->setStatusCode(403);
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;
                }
                else
                {
                    $user=$em->getRepository("AcmtoolAppBundle:CustomerUser")->findOneById($json->{'id'});

                    if($user instanceOf CustomerUser){
                        if($user->getCompany()->getId()!=$this->get('security.context')->getToken()->getUser()->getId() && $this->get('security.context')->isGranted('ROLE_CUSTOMER'))
                        {
                            $response=new Response();
                            $response->setStatusCode(403);
                            $response->headers->set('Content-Type', 'application/json');
                            return $response;
                        }
                        $user->getCredentials()->setLogin($json->{"login"});
                        if(isset($json->{'password'}))
                        {
                            $factory = $this->get('security.encoder_factory');
                            $encoder = $factory->getEncoder($user->getCredentials());
                            $password = $encoder->encodePassword($json->{'password'}, $user->getSalt());
                            $user->getCredentials()->setPassword($password);
                        }
                        $user->setEmail($json->{'email'});
                        $user->setName($json->{'name'});
                        $user->setSurname($json->{'surname'});
                       if(isset($json->{"phonenumber"}))
                            $user->setTelnumber($json->{"phonenumber"});
                        $user->setTitle($json->{"title"});
                        $user->setPhonecode($json->{"phonecode"});
                        $validator = $this->get('validator');
                        $errorList = $validator->validate($user);
                        $crederrorlist=$validator->validate($user->getCredentials());
                        if (count($errorList) > 0 || count($crederrorlist)>0) {
                            $response= new Response();
                            $response->setStatusCode(400);
                            $errosmsg=array();
                            foreach ($errorList as $error) {
                                array_push($errosmsg, $error->getMessage());
                            }
                            foreach ($crederrorlist as $error) {
                                 array_push($errosmsg, $error->getMessage());
                            }
                            $response->setContent(json_encode(array("errors"=>$errosmsg)));
                            return $response;
                        } else {
                            $em->flush();
                            $res=new Response();
                            $res->setStatusCode(200);
                            $res->setContent(ConstValues::KEYAUPDATED);
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
        $user=$em->getRepository("AcmtoolAppBundle:CustomerUser")->findOneById($id);
        if($user){
            $em->remove($user);
            $em->flush();
            $res=new Response();
            $res->setStatusCode(200);
            $res->setContent(ConstValues::KEYADELETED);
            return $res;
        }
        else
        {
            $response=new Response('{"err":"'.ConstValues::INVALIDREQUEST.'"}',400);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

    }

    public function ListAction($page)
    {
        if($this->get('security.context')->isGranted("ROLE_CUSTOMER"))
        {
            $em = $this->getDoctrine()->getManager();
            $customer=$this->get('security.context')->getToken()->getUser();
            //$totalpages=ceil($em->getRepository("AcmtoolAppBundle:CustomerUser")->getCustomerUsersCount($customer)/ConstValues::COUNT);
            //$start=ConstValues::COUNT*($page-1);
            //$result=$em->getRepository("AcmtoolAppBundle:CustomerUser")->getUsersByKeyCustomer($customer,$start);
            $result=$em->getRepository("AcmtoolAppBundle:CustomerUser")->findAll();
            if(count($result)>0)
            {
                $users=array();
                $i=0;
                foreach ($result as $user) {
                    $users[$i] = array('id'=>$user->getId(),'username' =>$user->getUsername(),'email'=>$user->getEmail(),'photo'=>$user->getPhoto(),"name"=>$user->getName(),"surname"=>$user->getSurname(),"photo"=>$user->getPhoto(),"phonenumber"=>$user->getTelnumber(),"phonecode"=>$user->getPhonecode(),"title"=>$user->getTitle());
                    $i++;

                }
                //$messag = array('totalpages' => $totalpages,'current_page'=>$page,'users'=>$users);
                $messag = array('users'=>$users);
                $res=new Response();
                $res->setStatusCode(200);
                $res->setContent(json_encode($messag));
                return $res;
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
            $response=new Response();
            $response->setStatusCode(403);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
    }

    public function DetailsAction($id)
    {
        $em = $this->getDoctrine()->getManager();
       if ($this->get('security.context')->isGranted('ROLE_CUSER') && $this->get('security.context')->getToken()->getUser()->getId()!=$id)  {
            $response=new Response();
            $response->setStatusCode(403);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        else
        {
             
                $user=$em->getRepository("AcmtoolAppBundle:CustomerUser")->findOneById($id);
            if($user)
            {
                $UserInfo = array('id'=>$user->getId(),'username' =>$user->getUsername(),'email'=>$user->getEmail(),'photo'=>$user->getPhoto(),"name"=>$user->getName(),"surname"=>$user->getSurname(),"photo"=>$user->getPhoto(),"tel"=>$user->getTelnumber());
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
