<?php

namespace Acmtool\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Httpfoundation\Response;
use Acmtool\AppBundle\Entity\Admin;
use Acmtool\AppBundle\Entity\Creds;
use Acmtool\AppBundle\Entity\TeamMember;
use Acmtool\AppBundle\Entity\KeyAccount;
use Acmtool\AppBundle\Entity\ConstValues;
use Acmtool\AppBundle\Entity\Customer;
use Acmtool\AppBundle\Entity\CustomerUser;
use Acmtool\AppBundle\Entity\Token;
use Acmtool\AppBundle\Entity\DeviceToken;
class AuthentificationController extends Controller
{
    private $authservice;
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
            if(!isset($json->{"grant_type"}))
            {
                $response=new Response('{"err":"'.ConstValues::INVALIDREQUEST.'"}',400);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
            else
            {
                $grantype = $json->{"grant_type"};
                if($grantype == "password")
                {
                    $result = $this->webAuth($json);
                }
                elseif ($grantype == "mobileapp") {
                    $result = $this->appAuth($json);
                }
                else
                {
                    $response=new Response('{"err":"'.ConstValues::INVALIDREQUEST.'"}',400);
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                }
                if($result["auth"])
                {
                    $token=$result["token"];
                    $user = $result["user"];
                    if($user instanceOf Admin)
                        $UserInfo = array('id'=>$user->getId(),'username' =>$user->getUsername(),'email'=>$user->getEmail(),"name"=>$user->getName(),"surname"=>$user->getSurname(),"roles"=>$user->getRoles(),"title"=>$user->getTitle());
                        
                    elseif ($user instanceOf Customer) {
                        $UserInfo = array('id'=>$user->getId(),'username' =>$user->getUsername(),'email'=>$user->getEmail(),'compnay_name'=>$user->getCompanyname(),"name"=>$user->getName(),"surname"=>$user->getSurname(),"roles"=>$user->getRoles(),"signed"=>true,"phonecode"=>$user->getPhonecode(),"telnumber"=>$user->getTelnumber(),
                            "keyaccount"=>array('id'=>$user->getKeyaccount()->getId(),
                                "email"=>$user->getKeyaccount()->getEmail(),
                                "name"=>$user->getKeyaccount()->getName(),"surname"=>$user->getKeyaccount()->getSurname(),
                                "photo"=>$user->getKeyaccount()->getPhoto(),
                                "tel"=>$user->getKeyaccount()->getPhonecode().' '.$user->getKeyaccount()->getPhonenumber(),
                                "company_name"=>$user->getKeyaccount()->getCompanyname()));
                        $address=array("address"=>$user->getAddress()->getAddress(),"zipcode"=>$user->getAddress()->getZipcode(),"city"=>$user->getAddress()->getCity(),"country"=>$user->getAddress()->getCountry(),"state"=>$user->getAddress()->getState());
                        $tools=[];
                        $i=0;
                        foreach ($user->getPmtools() as $key) {
                            $tools[$i]=$key->getToolname();
                        }
                        $UserInfo['pmtools']=$tools;
                        $UserInfo["address"]=$address;

                        $this->get("acmtool_app.notifier.handler")->clientLoggedIn($user->getEmail());
                    }
                    elseif ($user instanceOf CustomerUser) {
                        $UserInfo = array('id'=>$user->getId(),'username' =>$user->getUsername(),'email'=>$user->getEmail(),'photo'=>$user->getPhoto(),"name"=>$user->getName(),"surname"=>$user->getSurname(),"photo"=>$user->getPhoto(),"roles"=>$user->getRoles(),"title"=>$user->getTitle(),"phonecode"=>$user->getPhonecode(),"telnumber"=>$user->getTelnumber(),"keyaccount"=>array('id'=>$user->getKeyaccount()->getId(),"email"=>$user->getKeyaccount()->getEmail(),"name"=>$user->getKeyaccount()->getName(),"surname"=>$user->getKeyaccount()->getSurname(),"photo"=>$user->getKeyaccount()->getPhoto(),"tel"=>$user->getKeyaccount()->getPhonecode().' '.$user->getKeyaccount()->getPhonenumber()));
                       $this->get("acmtool_app.notifier.handler")->clientLoggedIn($user->getEmail());
                    }
                    elseif ($user instanceOf TeamMember) {
                        $UserInfo = array('id'=>$user->getId(),'username' =>$user->getUsername(),
                            'email'=>$user->getEmail(),'photo'=>$user->getPhoto(),
                            "name"=>$user->getName(),"surname"=>$user->getSurname(),
                            "photo"=>$user->getPhoto(),"roles"=>$user->getRoles(),
                            "title"=>$user->getTitle(),"city"=>$user->getCity(),"country"=>$user->getCountry(),
                            "phonecode"=>$user->getPhonecode(),"telnumber"=>$user->getPhonenumber(),
                            "languages"=>$user->getLanguage());
                        if($user instanceOf KeyAccount)
                            if($user->getCompanyname() != null)
                            {
                                $UserInfo["canmanage"] = $user->getCanmanage();
                                $UserInfo["compnay_name"] = $user->getCompanyname();
                            }
                                
                    }
                    if($token instanceOf DeviceToken)
                    {
                        $tokenInfo=array('token'=>$token->getToken());
                    }
                    else
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
        }
    }
    public function appLogoutAction($token)
    {
        $em = $this->getDoctrine()->getManager();
        $deviceToken = $em->getRepository("AcmtoolAppBundle:DeviceToken")->findOneByToken($token);
        $em->remove($deviceToken);
        $em->flush();
        return new Response('{"loggout":true}',200);
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
    private function webAuth($json)
    {
        $this->authservice = $this->get('acmtool_app.authentication');
        if(!(isset($json->{"login"}) && isset($json->{"password"}) && isset($json->{"stayloggedin"})))
        {
            $auth= array("auth" => false, "reason"=>ConstValues::INVALIDREQUEST);
            return $auth;
        }
        else
        {
            $user = $this->getAuthUser($json->{"login"});
            if($user)
            {
                $password=$json->{"password"};
                $resut= $this->authservice->Authentificate($user,$password,$json->{"stayloggedin"});
                $resut["user"] = $user;
                return $resut;
            }
            else
            {
                $auth= array("auth" => false, "reason"=>"please verify your credentials");
                return $auth;
            }
            
        }
    }
    private function appAuth($json)
    {
        $this->authservice = $this->get('acmtool_app.authentication');
        if(!(isset($json->{"login"}) && isset($json->{"password"}) && isset($json->{"os"})))
        {
            $auth= array("auth" => false, "reason"=>ConstValues::INVALIDREQUEST);
            return $auth;
        }
        else
        {
            $os = "";
            $user = $this->getAuthUser($json->{"login"});
            if($user)
            {
                if(($user instanceOf Customer) || ($user instanceOf CustomerUser))
                {
                     $password=$json->{"password"};
                    if(isset($json->{"os"}))
                    {
                        $os = $json->{"os"};
                    }
                    $deviceToken = null;
                    if(isset($json->{"deviceToken"}))
                    {
                        $deviceToken = $json->{"deviceToken"};
                    }
                    $deviceName = "anonymous";
                    if(isset($json->{"deviceName"}))
                    {
                        $deviceName = $json->{"deviceName"};
                    }
                    $resut= $this->authservice->appAuuthetificate($user,$password,$os,$deviceToken,$deviceName);
                    $resut["user"] = $user;
                    return $resut;
                }
                else
                {
                    $auth= array("auth" => false, "reason"=>"don't have access");
                    return $auth;
                }
            }
            else
            {
                $auth= array("auth" => false, "reason"=>"user don't exist");
                return $auth;
            }
            
           
        }
    }
    private function getAuthUser($username)
    {
         $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AcmtoolAppBundle:Creds')->getUserByUsername($username);
        if($user && $user->getIsActive())
        {
            return $user;
        }
        else
        {
            return false;
        }
    }

}
