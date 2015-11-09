<?php
namespace Acmtool\AppBundle\Listeners;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\Response;

class CorssListener
{
	public function onRequest(GetResponseEvent $event)
	{
		$request = $event->getRequest();
    	$method  = $request->getRealMethod();
	    if ('OPTIONS' == $method) {
	        $response = new Response();
	        $event->setResponse($response);
	    }
		
	}
	public function onResponse(FilterResponseEvent $event)
	{
		$response = $event->getResponse();
    	$response->headers->set('Access-Control-Allow-Origin', '*');
    	$response->headers->set('Access-Control-Allow-Methods', 'GET,POST,PUT,DELETE');
   		$response->headers->set('Access-Control-Allow-Headers', 'x-crm-access-token,Content-Type');

	}
}