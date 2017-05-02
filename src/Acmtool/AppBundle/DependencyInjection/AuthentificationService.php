<?php
namespace Acmtool\AppBundle\DependencyInjection;
use Acmtool\AppBundle\Entity\Token;
use Acmtool\AppBundle\Entity\DeviceToken;
use Acmtool\AppBundle\Entity\ConstValues;

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
	public function Authentificate($user,$password,$isstayedloggedin=false)
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
                    $today =new \DateTime("NOW",  new \DateTimeZone(ConstValues::TIMEZONE));
                    if($isstayedloggedin)
                        $token->setCreationdate($today->add(new \DateInterval('P1Y')));
                    else
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
				$result["reason"]=ConstValues::REASONWRONG;
				return $result;
            }
    	}
    	else
		{
			$result["auth"]=false;
			$result["reason"]=ConstValues::REASONMISSING;
			return $result;
		}
	}
    public function appAuuthetificate($user,$password,$os,$phoneid=null,$phoneName)
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
               $token=new DeviceToken();
               $today =new \DateTime("NOW",  new \DateTimeZone(ConstValues::TIMEZONE));
                $token->setToken($this->generateToken($user,$today));
                $userroles=$user->getRoles();
                $token->setUser($user->getCredentials());
                if($phoneid != null)
                {
                    $token->setDeviceid($phoneid);
                }
                $token->setDevicename($phoneName);
                $token->setOs($os);
                $user->getCredentials()->addDevicetoken($token);
                $em->persist($token);
                $em->flush();
                $result["auth"]=true;
                $result["token"]=$token;
                return $result;

            }
            else
            {
                $result["auth"]=false;
                $result["reason"]=ConstValues::REASONWRONG;
                return $result;
            }
        }
        else
        {
            $result["auth"]=false;
            $result["reason"]=ConstValues::REASONMISSING;
            return $result;
        }
    }
	private function istokenExpired($token)
	{
		if($token){
                date_default_timezone_set(ConstValues::TIMEZONE);
                $expireDate=$token->getCreationdate()->add(new \DateInterval('PT'.ConstValues::PERIOD.'S'));
                $today =new \DateTime("NOW",  new \DateTimeZone(ConstValues::TIMEZONE));
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
		$tmp=$User->getUsername().$creationdate->format(ConstValues::TIMEZONE);
        $csrfToken = $this->crfProvider->generateCsrfToken($tmp);
        return $csrfToken;

	}
}