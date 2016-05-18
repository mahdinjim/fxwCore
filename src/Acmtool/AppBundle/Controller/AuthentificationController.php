<?php

namespace Acmtool\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Httpfoundation\Response;
use Acmtool\AppBundle\Entity\Admin;
use Acmtool\AppBundle\Entity\Creds;
use Acmtool\AppBundle\Entity\TeamMember;
use Acmtool\AppBundle\Entity\ConstValues;
use Acmtool\AppBundle\Entity\Customer;
use Acmtool\AppBundle\Entity\CustomerUser;

class AuthentificationController extends Controller
{
    public function ApiAuthentificationAction()
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
            if(!(isset($json->{"grant_type"}) && isset($json->{"login"}) && isset($json->{"password"})))
            {
                $response=new Response('{"err":"'.ConstValues::INVALIDREQUEST.'"}',400);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
            
            $grantype=isset($json->{"grant_type"});
            if($grantype=="password")
            {
               
                $username=$json->{"login"};
                $user = $em->getRepository('AcmtoolAppBundle:Creds')->getUserByUsername($username);
                if($user)
                {
                    $authservice=$this->get('acmtool_app.authentication');
                    $password=$json->{"password"};
                    $result=$authservice->Authentificate($user,$password);
                    if($result["auth"])
                    {
                        $token=$result["token"];
                        if($user instanceOf Admin)
                            $UserInfo = array('id'=>$user->getId(),'username' =>$user->getUsername(),'email'=>$user->getEmail(),'tel'=>$user->getTel(),"name"=>$user->getName(),"surname"=>$user->getSurname(),"roles"=>$user->getRoles(),"title"=>$user->getTitle());
                        elseif ($user instanceOf Customer) {
                            $UserInfo = array('id'=>$user->getId(),'username' =>$user->getUsername(),'email'=>$user->getEmail(),'compnay_name'=>$user->getCompanyname(),"name"=>$user->getName(),"surname"=>$user->getSurname(),"roles"=>$user->getRoles(),"signed"=>$user->getSignedContract(),"keyaccount"=>array('id'=>$user->getKeyaccount()->getId(),"email"=>$user->getKeyaccount()->getEmail(),"name"=>$user->getKeyaccount()->getName(),"surname"=>$user->getKeyaccount()->getSurname(),"photo"=>$user->getKeyaccount()->getPhoto(),"tel"=>$user->getKeyaccount()->getPhonecode().' '.$user->getKeyaccount()->getPhonenumber()));
                        }
                        elseif ($user instanceOf CustomerUser) {
                            $UserInfo = array('id'=>$user->getId(),'username' =>$user->getUsername(),'email'=>$user->getEmail(),'photo'=>$user->getPhoto(),"name"=>$user->getName(),"surname"=>$user->getSurname(),"photo"=>$user->getPhoto(),"roles"=>$user->getRoles(),"keyaccount"=>array('id'=>$user->getKeyaccount()->getId(),"email"=>$user->getKeyaccount()->getEmail(),"name"=>$user->getKeyaccount()->getName(),"surname"=>$user->getKeyaccount()->getSurname(),"photo"=>$user->getKeyaccount()->getPhoto(),"tel"=>$user->getKeyaccount()->getPhonecode().' '.$user->getKeyaccount()->getPhonenumber()));
                        }
                        elseif ($user instanceOf TeamMember) {
                            $UserInfo = array('id'=>$user->getId(),'username' =>$user->getUsername(),'email'=>$user->getEmail(),'photo'=>$user->getPhoto(),"name"=>$user->getName(),"surname"=>$user->getSurname(),"photo"=>$user->getPhoto(),"roles"=>$user->getRoles(),"title"=>$user->getTitle());
                        }
                        $tokenInfo = array('token' => $token->getTokendig(),'experationDate'=>$token->getCreationdate()->add(new \DateInterval('PT'.ConstValues::PERIOD.'S')) );
                        $mess = array('user' => $UserInfo, 'token'=>$tokenInfo);
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
                    $response=new Response('{"errors":"'.ConstValues::REASONWRONG.'"}',403);
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                }
                
            }
            else
            {
                $response=new Response('{"errors":"'.ConstValues::INVALIDREQUEST.'"}',400);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
        }
    }
    public function isTokenExpiredAction()
    {
        return new Response('{"Expired":false}',200);
    }

}
