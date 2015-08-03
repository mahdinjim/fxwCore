<?php

namespace Acmtool\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Httpfoundation\Response;
use Acmtool\AppBundle\Entity\Admin;
use Acmtool\AppBundle\Entity\Creds;

Const PERIOD=3600;
Const INVALIDREQUEST="invalid_request";
Const ADMINCREATED="Admin created successfully";
Const ADMINUPDATED="Admin updated successfully";
Const REASONWRONG="Wrong password/Username";
class AdminControllerController extends Controller
{
    public function CreateAction()
    {
        $request = $this->get('request');
        $message = $request->getContent();
        $em = $this->getDoctrine()->getManager();
        $result = $this->verifyJson($message);
        if(!$result["valid"])
            return $result['response'];
        else
        {
            $json=$result['json'];
            if(!(isset($json->{'password'}) && isset($json->{'login'}) && isset($json->{'email'})))
            {
                $response=new Response('{"err":"'.INVALIDREQUEST.'"}',400);
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
            $admin->setCredentials($creds);
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
                $res->setContent(ADMINCREATED);
                return $res;
            }


        }
        
    }

    public function AuthentificateAction()
    {
        $request = $this->get('request');
        $message = $request->getContent();
        $em = $this->getDoctrine()->getManager();
        $result = $this->verifyJson($message);
        if(!$result["valid"])
            return $result['response'];
        else
        {
            $json=$result['json'];
            if(!(isset($json->{"grant_type"}) && isset($json->{"login"}) && isset($json->{"password"})))
            {
                $response=new Response('{"err":"'.INVALIDREQUEST.'"}',400);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
            
            $grantype=isset($json->{"grant_type"});
            if($grantype=="password")
            {
                $username=$json->{"login"};
                $user = $em->getRepository('AcmtoolAppBundle:Admin')->findByUserName($username);
                if($user)
                {
                    $authservice=$this->get('acmtool_app.authentication');
                    $password=$json->{"password"};
                    $result=$authservice->Authentificate($user,$password);
                    if($result["auth"])
                    {
                        $token=$result["token"];
                        $UserInfo = array('username' =>$user->getUsername(),'email'=>$user->getEmail(),'tel'=>$user->getTel() );
                        $tokenInfo = array('token' => $token->getTokendig(),'experationDate'=>$token->getCreationdate()->add(new \DateInterval('PT'.PERIOD.'S')) );
                        $mess = array('admin' => $UserInfo, 'token'=>$tokenInfo);
                        $response = new Response(json_encode($mess), 200);
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;
                    }
                    else
                    {
                        $response=new Response('{"errors":"'.$result["reason"].'"}',403);
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;
                    }

                }
                else
                {
                    $response=new Response('{"errors":"'.REASONWRONG.'"}',403);
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                }
                
            }
            else
            {
                $response=new Response('{"errors":"'.INVALIDREQUEST.'"}',400);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
        }
    }

    public function updateAction()
    {
        $request = $this->get('request');
        $message = $request->getContent();
        $em = $this->getDoctrine()->getManager();
        $result = $this->verifyJson($message);
        if(!$result["valid"])
            return $result['response'];
        else
        {
            $json=$result['json'];
            $admin=$usr= $this->get('security.context')->getToken()->getUser();
            if(!(isset($json->{'password'}) && isset($json->{'login'}) && isset($json->{'email'})))
            {
                $response=new Response('{"err":"'.INVALIDREQUEST.'"}',400);
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
                    $res->setContent(ADMINUPDATED);
                    return $res;
                }

                
            }
            
        }
    }
    private function verifyJson($message)
    {
        $json = json_decode($message);
        if(json_last_error()){
            $response = new Response();
            $response->setStatusCode(400);
            switch (json_last_error()) {
                case JSON_ERROR_NONE:
                    $erro_message =  ' - No errors';
                break;
                case JSON_ERROR_DEPTH:
                    $erro_message = ' - Maximum stack depth exceeded';
                break;
                case JSON_ERROR_STATE_MISMATCH:
                    $erro_message = ' - Underflow or the modes mismatch';
                break;
                case JSON_ERROR_CTRL_CHAR:
                    $erro_message = ' - Unexpected control character found';
                break;
                case JSON_ERROR_SYNTAX:
                    $erro_message = ' - Syntax error, malformed JSON';
                break;
                case JSON_ERROR_UTF8:
                    $erro_message = ' - Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
                default:
                    $erro_message = ' - Unknown error';
                break;

            }
            $response->setContent(array('errors' => $erro_message));
            return array('valid' => false,'response'=>$response );
        }
        else
            return array('valid' => true,'json'=>$json );
    }
}
