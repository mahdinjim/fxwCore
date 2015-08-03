<?php
namespace Acmtool\AppBundle\firewall\apiauth;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Acmtool\AppBundle\firewall\apiauth\ApiToken;

class ApiTokenListener implements ListenerInterface
{
    protected $securityContext;
    protected $authenticationManager;

    public function __construct(SecurityContextInterface $securityContext,AuthenticationManagerInterface $authenticationManager)
    {
         $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
    }

    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if(!$request->headers->has('x-crm-access-token'))
            return;
        else
        {
        	$tokenstring=$request->headers->get('x-crm-access-token');
        	$apitoken=new ApiToken();
        	$apitoken->setTokenDig($tokenstring);

        	try {
            	$authToken = $this->authenticationManager->authenticate($apitoken);
            	return $this->securityContext->setToken($authToken);
                
	        } catch (AuthenticationException $failed) {
                $this->securityContext->setToken(null);
	            $response = new Response();
	            $response->setStatusCode(403);
	            $event->setResponse($response);
                return;

	        }

        }
   
        
       
    }
}