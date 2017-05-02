<?php

namespace Acmtool\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Httpfoundation\Response;
use Acmtool\AppBundle\Entity\Customer;
class DefaultController extends Controller
{
    public function indexAction()
    {
       $this->get("acmtool_app.email.notifier")->notifyAddedTeamMember("njimmahdi@gmail.com","123","mahdi","njim","mahdi");
        $message = \Swift_Message::newInstance()
            ->setSubject('Contact enquiry from symblog')
            ->setFrom('mn@flexwork.io')
            ->setTo('njimmahdi@gmail.com')
            ->setBody("hello");
        $this->get('mailer')->send($message,$failure);
        var_dump($failure);


        return new Response("<html><body>hello</body>");
    }
    public function channelIdAction($name)
    {
         
        $chatservice=$this->get("acmtool_app.messaging");
        $chatprovider=$chatservice->CreateChatProvider();
        $id=$chatprovider->getChannelId($name);
        return new Response("<html><body>channel id is:".$id."</body>");
    }
    public function testWsseAction()
    {
        return new Response('{"status":"ok"}');
    }
    public function reportAppBugAction()
    {
        $request = $this->get('request');
        $message = $request->getContent();
        $user=$this->get("security.context")->getToken()->getUser();
        $userName = $user->getName() . " " . $user->getSurname();
        if($user instanceOf Customer)
        {
            $compnayName = $user->getCompanyname();
        }
        else
            $compnayName = $user->getCompany()->getCompanyname();
        $email = $user->getEmail();
         $result = $this->get('acmtool_app.validation.json')->validate($message);
        if($result["valid"])
        {
            $json=$result['json'];
            $status = "";
            if(isset($json->{"status"}))
                $status = $json->{"status"};
            $message = "";
            if(isset($json->{"message"}))
                $message = $json->{"message"};
            $os = "";
            if(isset($json->{"os"}))
                $os = $json->{"os"};
            $phone = "";
            if(isset($json->{"phone"}))
                $phone = $json->{"phone"};
            $body = "User : ".$userName;
            $body.= "\nEmail : ".$email;
            $body.="\ncompany : ".$compnayName;
            $body.="\nphone : ".$phone;
            $body.="\nos : ".$os;
            $body.="\nstatus : ".$status;
            $body.="\nmessage :\n".$message;
            $message = \Swift_Message::newInstance()
                ->setSubject('App error')
                ->setFrom('flexy@flexwork.io')
                ->setTo('mn@flexwork.io')
                ->setBody($body);
            $this->get('mailer')->send($message,$failure);
        }
        return new Response("sent",200);
    }
}
