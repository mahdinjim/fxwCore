<?php

namespace Acmtool\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Httpfoundation\Response;
use Acmtool\AppBundle\Entity\Admin;
use Acmtool\AppBundle\Entity\Creds;
use Acmtool\AppBundle\Entity\Titles;
use Acmtool\AppBundle\Entity\ConstValues;



class AdminControllerController extends Controller
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
            if(!(isset($json->{'password'}) && isset($json->{'login'}) && isset($json->{'email'})))
            {
                $response=new Response('{"errors":"'.ConstValues::INVALIDREQUEST.'"}',400);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
            $admin=new Admin();
            $admin->setEmail($json->{"email"});
            $creds=new Creds();
            $creds->setLogin($json->{"login"});
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($creds);
            $password = $encoder->encodePassword($json->{'password'}, $admin->getSalt());
            $creds->setPassword($password);
            $creds->setTitle(Titles::Admin);
            $admin->setCredentials($creds);
            $admin->setTitle($json->{"title"});
            if(isset($json->{"tel"}))
                $admin->setTel($json->{"tel"});
            $validator = $this->get('validator');
            $errorList = $validator->validate($admin);
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
                $em->persist($admin);
                $em->flush();
                $res=new Response();
                $res->setStatusCode(200);
                $res->setContent(ConstValues::ADMINCREATED);
                return $res;
            }


        }
        
    }
    public function updateAction()
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
            $admin=$usr= $this->get('security.context')->getToken()->getUser();
            if(!(isset($json->{'password'}) && isset($json->{'login'}) && isset($json->{'email'})))
            {
                $response=new Response('{"errors":"'.ConstValues::INVALIDREQUEST.'"}',400);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
            else
            {
                $admin->setEmail($json->{'email'});
                $admin->getCredentials()->setLogin($json->{"login"});
                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($admin->getCredentials());
                $password = $encoder->encodePassword($json->{'password'}, $admin->getSalt());
                $admin->getCredentials()->setPassword($password);
                $validator = $this->get('validator');
                $errorList = $validator->validate($admin);
                $crederrorlist=$validator->validate($admin->getCredentials());
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
                    $res->setContent(ConstValues::ADMINUPDATED);
                    return $res;
                }

                
            }
            
        }
    }
   
}
