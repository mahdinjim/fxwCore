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
use Acmtool\AppBundle\Entity\Token;
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
            if(!(isset($json->{"grant_type"}) && isset($json->{"login"}) && isset($json->{"password"}) && isset($json->{"stayloggedin"})))
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
                    $result=$authservice->Authentificate($user,$password,$json->{"stayloggedin"});
                    if($result["auth"])
                    {
                        $token=$result["token"];
                        if($user instanceOf Admin)
                            $UserInfo = array('id'=>$user->getId(),'username' =>$user->getUsername(),'email'=>$user->getEmail(),"name"=>$user->getName(),"surname"=>$user->getSurname(),"roles"=>$user->getRoles(),"title"=>$user->getTitle());
                            
                        elseif ($user instanceOf Customer) {
                            $UserInfo = array('id'=>$user->getId(),'username' =>$user->getUsername(),'email'=>$user->getEmail(),'compnay_name'=>$user->getCompanyname(),"name"=>$user->getName(),"surname"=>$user->getSurname(),"roles"=>$user->getRoles(),"signed"=>true,"phonecode"=>$user->getPhonecode(),"telnumber"=>$user->getTelnumber(),"keyaccount"=>array('id'=>$user->getKeyaccount()->getId(),"email"=>$user->getKeyaccount()->getEmail(),"name"=>$user->getKeyaccount()->getName(),"surname"=>$user->getKeyaccount()->getSurname(),"photo"=>$user->getKeyaccount()->getPhoto(),"tel"=>$user->getKeyaccount()->getPhonecode().' '.$user->getKeyaccount()->getPhonenumber()));
                            $address=array("address"=>$user->getAddress()->getAddress(),"zipcode"=>$user->getAddress()->getZipcode(),"city"=>$user->getAddress()->getCity(),"country"=>$user->getAddress()->getCountry(),"state"=>$user->getAddress()->getState());
                            $UserInfo["address"]=$address;
                            $this->get("acmtool_app.notifier.handler")->clientLoggedIn($user->getEmail());
                        }
                        elseif ($user instanceOf CustomerUser) {
                            $UserInfo = array('id'=>$user->getId(),'username' =>$user->getUsername(),'email'=>$user->getEmail(),'photo'=>$user->getPhoto(),"name"=>$user->getName(),"surname"=>$user->getSurname(),"photo"=>$user->getPhoto(),"roles"=>$user->getRoles(),"title"=>$user->getTitle(),"phonecode"=>$user->getPhonecode(),"telnumber"=>$user->getTelnumber(),"keyaccount"=>array('id'=>$user->getKeyaccount()->getId(),"email"=>$user->getKeyaccount()->getEmail(),"name"=>$user->getKeyaccount()->getName(),"surname"=>$user->getKeyaccount()->getSurname(),"photo"=>$user->getKeyaccount()->getPhoto(),"tel"=>$user->getKeyaccount()->getPhonecode().' '.$user->getKeyaccount()->getPhonenumber()));
                           $this->get("acmtool_app.notifier.handler")->clientLoggedIn($user->getEmail());
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
    public function changePasswordAction()
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
            if(isset($json->{"login"}) && isset($json->{"new_password"}) && isset($json->{"current_password"}))
            {
                $username=$json->{"login"};
                $user = $em->getRepository('AcmtoolAppBundle:Creds')->getUserByUsername($username);
                if($user)
                {
                    $authservice=$this->get('acmtool_app.authentication');
                    $password=$json->{"current_password"};
                    $result=$authservice->Authentificate($user,$password);

                    if($result["auth"])
                    {
                        $factory = $this->get('security.encoder_factory');
                        $encoder = $factory->getEncoder($user->getCredentials());
                        $password = $encoder->encodePassword($json->{'new_password'}, $user->getSalt());
                        $user->getCredentials()->setPassword($password);
                        $em->flush();
                        return new Response("password updated",200);
                    }
                    else
                    {
                        return new Response('Go away',403);
                    }
                }
                else
                {
                    return new Response('bad request',400);
                }
            }
            else{
                return new Response('bad request',400);
            }
        }
    }
    public function logoutAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $token=$user->getApitoken();
        if($token)
        {
            $em = $this->getDoctrine()->getManager();
            $em->remove($token);
            $em->flush();
            return new Response('{"loggout":true}',200);
        }
        else
            return new Response('bad request',400);

    }
    public function isTokenExpiredAction()
    {
        return new Response('{"Expired":false}',200);
    }

}
