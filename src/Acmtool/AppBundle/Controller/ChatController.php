<?php

namespace Acmtool\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Httpfoundation\Response;

class ChatController extends Controller
{
	public function getGroupInfoAction($group)
	{
		$chatservice=$this->get("acmtool_app.messaging");
		$chatprovider=$chatservice->CreateChatProvider();
		$result=$chatprovider->getGroupInfo($group);
		if($result["result"])
		{
			$res=new Response();
            $res->setStatusCode(200);
            $res->setContent(json_encode($result));
            return $res;
		}
		else
		{
			$res=new Response();
            $res->setStatusCode(400);
            $res->setContent('{error:"bad request"}');
            return $res;
		}
	}
	public function getNewMessagesAction($group,$last)
	{
		$chatservice=$this->get("acmtool_app.messaging");
		$chatprovider=$chatservice->CreateChatProvider();
		$result=$chatprovider->getNewMessages($group,$last);
		if($result["result"])
		{
			$res=new Response();
            $res->setStatusCode(200);
            $res->setContent(json_encode($result));
            return $res;
		}
		else
		{
			$res=new Response();
            $res->setStatusCode(400);
            $res->setContent('{error:"bad request"}');
            return $res;
		}
	}
	public function getMessagesAction($number,$group,$start){
		$chatservice=$this->get("acmtool_app.messaging");
		$chatprovider=$chatservice->CreateChatProvider();
		$result=$chatprovider->getAllmess($number,$group,$start);
		if($result["result"])
		{
			$res=new Response();
            $res->setStatusCode(200);
            $res->setContent(json_encode($result));
            return $res;
		}
		else
		{
			$res=new Response();
            $res->setStatusCode(400);
            $res->setContent('{error:"bad request"}');
            return $res;
		}
	}
	public function sendMessageAction($group){
		$request = $this->get('request');
		$message = $request->getContent();
        $result = $this->get('acmtool_app.validation.json')->validate($message);
        if(!$result["valid"])
            return $result['response'];
        else
        {
        	$json=$result['json'];
        	if(isset($json->{"message"}) && isset($json->{"client"})){
				$chatservice=$this->get("acmtool_app.messaging");
				$chatprovider=$chatservice->CreateChatProvider();
				$result=$chatprovider->sendMessage($json->{"message"},$group,$json->{"client"});
				if($result)
				{
					$res=new Response();
		            $res->setStatusCode(200);
		            $res->setContent(json_encode($result));
		            return $res;
				}
				else
				{
					$res=new Response();
		            $res->setStatusCode(400);
		            $res->setContent('{error:"impossible to send message"}');
		            return $res;
				}
			}
			else
			{
				$res=new Response();
		        $res->setStatusCode(400);
		        $res->setContent('{error:"missing the message"}');
		        return $res;
			}
		}
	}
	public function editMessageAction($group){
		$request = $this->get('request');
		$message = $request->getContent();
        $result = $this->get('acmtool_app.validation.json')->validate($message);
        if(!$result["valid"])
            return $result['response'];
        else
        {
        	$json=$result['json'];
        	if(isset($json->{"message"}) && isset($json->{"id"})){
				$chatservice=$this->get("acmtool_app.messaging");
				$chatprovider=$chatservice->CreateChatProvider();
				$result=$chatprovider->editMessage($json->{"id"},$json->{"message"},$group);
				if($result)
				{
					$res=new Response();
		            $res->setStatusCode(200);
		            $res->setContent(json_encode($result));
		            return $res;
				}
				else
				{
					$res=new Response();
		            $res->setStatusCode(400);
		            $res->setContent('{error:"impossible to edit message"}');
		            return $res;
				}
			}
			else
			{
				$res=new Response();
		        $res->setStatusCode(400);
		        $res->setContent('{error:"missing the message"}');
		        return $res;
			}
		}
	}
	public function markMessagesAction($group,$mess){
		$chatservice=$this->get("acmtool_app.messaging");
		$chatprovider=$chatservice->CreateChatProvider();
		$result=$chatprovider->markAsRead($mess,$group);
		if($result)
		{
			$res=new Response();
            $res->setStatusCode(200);
            $res->setContent(json_encode($result));
            return $res;
		}
		else
		{
			$res=new Response();
            $res->setStatusCode(400);
            $res->setContent('{error:"impossible to read messages"}');
            return $res;
		}

	}
	public function deleteMessagesAction($group,$mess){
		$chatservice=$this->get("acmtool_app.messaging");
		$chatprovider=$chatservice->CreateChatProvider();
		$result=$chatprovider->deleteMessage($mess,$group);
		if($result)
		{
			$res=new Response();
            $res->setStatusCode(200);
            $res->setContent(json_encode($result));
            return $res;
		}
		else
		{
			$res=new Response();
            $res->setStatusCode(400);
            $res->setContent('{error:"impossible to delete messages"}');
            return $res;
		}

	}
}