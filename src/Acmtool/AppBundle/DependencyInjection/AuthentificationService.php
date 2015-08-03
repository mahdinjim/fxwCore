<?php
namespace Acmtool\AppBundle\DependencyInjection;
use Acmtool\AppBundle\Entity\Token;

Const PERIOD=3600;
Const REASONWRONG="Wrong password/Username";
Const REASONMISSING="Missing password/Username";
Const TIMEZONE="Europe/Berlin";
class AuthentificationService
{
	private $doctrine;
	private $factory;
	private $crfProvider;
	function __construct($doctrine,$factory,$crfProvider) {
		$this->doctrine = $doctrine;
		$this->factory=$factory;
		$this->crfProvider=$crfProvider;
	}
	public function Authentificate($user,$password)
	{
		$result=[];
		$username=$user->getUsername();
        $em=$this->doctrine->getManager();
		if($username && $password)
		{
			$encoder = $this->factory->getEncoder($user->getCredentials());
    		$bool = ($encoder->isPasswordValid($user->getPassword(),$password,$user->getSalt())) ? true : false;
            if($bool)
            {
            	$token=$user->getApitoken();
            	if($token!=null && !$this->istokenExpired($token))
            	{
            		$result["auth"]=true;
					$result["token"]=$token;
					return $result;
            	}
            	else
            	{
            		$token=new Token();
                    $today =new \DateTime("NOW",  new \DateTimeZone(TIMEZONE));
                    $token->setCreationdate($today);
                    $token->setTokendig($this->generateToken($user,$today));
                    $userroles=$user->getRoles();
                    $token->setUserrole($userroles[0]);
                    $user->setApiToken($token);
                    $em->persist($token);
                    $em->flush();
                    $result["auth"]=true;
					$result["token"]=$token;
					return $result;

            	}
            }
            else
            {
            	$result["auth"]=false;
				$result["reason"]=REASONWRONG;
				return $result;
            }
    	}
    	else
		{
			$result["auth"]=false;
			$result["reason"]=REASONMISSING;
			return $result;
		}
	}
	private function istokenExpired($token)
	{
		if($token){
                date_default_timezone_set(TIMEZONE);
                $expireDate=$token->getCreationdate()->add(new \DateInterval('PT'.PERIOD.'S'));
                $today =new \DateTime("NOW",  new \DateTimeZone(TIMEZONE));
                if($today<$expireDate)
                    return false;
                else
                {
                	$em = $this->doctrine->getManager();
                    $em->remove($token);
                    $em->flush();
                    return true;;
                }
            }
            else
            {
               return true;
            }
	}
	private function generateToken($User,$creationdate)
	{
		$tmp=$User->getUsername().$creationdate->format(TIMEZONE);
        $csrfToken = $this->crfProvider->generateCsrfToken($tmp);
        return $csrfToken;

	}
}