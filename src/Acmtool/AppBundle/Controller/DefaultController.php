<?php

namespace Acmtool\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Httpfoundation\Response;

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
}
