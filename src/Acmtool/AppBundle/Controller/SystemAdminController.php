<?php

namespace Acmtool\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Httpfoundation\Response;
use Acmtool\AppBundle\Entity\SystemAdmin;
use Acmtool\AppBundle\Entity\Creds;
use Acmtool\AppBundle\Entity\Titles;
use Acmtool\AppBundle\Entity\ConstValues;


class SystemAdminController extends Controller
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
            if(!(isset($json->{'password'}) && isset($json->{'login'}) && isset($json->{'email'}) && isset($json->{'name'}) && isset($json->{'surname'}) && isset($json->{'capacity'}) && isset($json->{'skills'})))
            {
                $response=new Response('{"err":"'.ConstValues::INVALIDREQUEST.'"}',400);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
            else
            {
                $user=new SystemAdmin();
                $creds=new Creds();
                $creds->setLogin($json->{"login"});
                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($creds);
                $password = $encoder->encodePassword($json->{'password'}, $user->getSalt());
                $creds->setPassword($password);
                $creds->setTitle(Titles::SystemAdmin);
                $user->setCredentials($creds);
                $user->setEmail($json->{'email'});
                $user->setName($json->{'name'});
                $user->setSurname($json->{'surname'});
                $user->setCapacity($json->{'capacity'});
                $user->setSkills($json->{'skills'});
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
                    $res=new Response();
                    $res->setStatusCode(200);
                    $res->setContent(ConstValues::SYSACREATED);
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
            if(!(isset($json->{'id'}) && isset($json->{'password'}) && isset($json->{'login'}) && isset($json->{'email'}) && isset($json->{'name'}) && isset($json->{'surname'}) && isset($json->{'capacity'}) && isset($json->{'skills'})))
            {
                $response=new Response('{"err":"'.ConstValues::INVALIDREQUEST.'"}',400);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
            else
            {
                if ($this->get('security.context')->isGranted('ROLE_SYSADMIN') && $this->get('security.context')->getToken()->getUser()->getId()!=$json->{'id'})  {
                        $response=new Response();
                        $response->setStatusCode(403);
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;
                }
                else
                {
                    $user=$em->getRepository("AcmtoolAppBundle:SystemAdmin")->findOneById($json->{'id'});
                    if($user instanceOf SystemAdmin){
                        $user->getCredentials()->setLogin($json->{"login"});
                        $factory = $this->get('security.encoder_factory');
                        $encoder = $factory->getEncoder($user->getCredentials());
                        $password = $encoder->encodePassword($json->{'password'}, $user->getSalt());
                        $user->getCredentials()->setPassword($password);
                        $user->setEmail($json->{'email'});
                        $user->setName($json->{'name'});
                        $user->setSurname($json->{'surname'});
                        $user->setCapacity($json->{'capacity'});
                        $user->setSkills($json->{'skills'});
                        if(isset($json->{"description"}))
                        {
                            $user->setDescription($json->{"description"});
                        }
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
                            $res->setContent(ConstValues::SYSAUPDATED);
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
        $user=$em->getRepository("AcmtoolAppBundle:SystemAdmin")->findOneById($id);
        if($user){
            $em->remove($user);
            $em->flush();
            $res=new Response();
            $res->setStatusCode(200);
            $res->setContent(ConstValues::SYSADELETED);
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
        $em = $this->getDoctrine()->getManager();
        $totalpages=ceil($em->createQuery("SELECT COUNT(t) FROM AcmtoolAppBundle:SystemAdmin t")
        ->getSingleScalarResult()/10);
        $start=ConstValues::COUNT*($page-1);
        $End=ConstValues::COUNT*$page;
        $result=$em->createQuery('select d from AcmtoolAppBundle:SystemAdmin d')
                    ->setMaxResults(ConstValues::COUNT)
                    ->setFirstResult($start)
                    ->getResult();
        if(count($result)>0)
        {
            $users=array();
            $i=0;
            foreach ($result as $user) {
                $users[$i] = array('id'=>$user->getId(),'username' =>$user->getUsername(),'email'=>$user->getEmail(),'photo'=>$user->getPhoto(),"name"=>$user->getName(),"surname"=>$user->getSurname(),"photo"=>$user->getPhoto(),"capacity"=>$user->getCapacity(),"skills"=>$user->getSkills(),"description"=>$user->getDescription());
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
            $response=new Response('{"err":"'.ConstValues::INVALIDREQUEST.'"}',400);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
    }

    public function DetailsAction($id)
    {
        $em = $this->getDoctrine()->getManager();
       if ($this->get('security.context')->isGranted('ROLE_SYSADMIN') && $this->get('security.context')->getToken()->getUser()->getId()!=$id)  {
            $response=new Response();
            $response->setStatusCode(403);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        else
        {

            $user=$em->getRepository("AcmtoolAppBundle:SystemAdmin")->findOneById($id);
            if($user)
            {
                $UserInfo = array('id'=>$user->getId(),'username' =>$user->getUsername(),'email'=>$user->getEmail(),'photo'=>$user->getPhoto(),"name"=>$user->getName(),"surname"=>$user->getSurname(),"photo"=>$user->getPhoto(),"capacity"=>$user->getCapacity(),"skills"=>$user->getSkills(),"description"=>$user->getDescription());
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
