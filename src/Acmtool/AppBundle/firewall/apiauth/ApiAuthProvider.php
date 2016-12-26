<?php
namespace Acmtool\AppBundle\firewall\apiauth;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\NonceExpiredException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Acmtool\AppBundle\firewall\apiauth\ApiToken;
use Acmtool\AppBundle\Entity\Customer;
use Acmtool\AppBundle\Entity\CustomerUser;
Const PERIOD=3600;
Const TIMEZONE="Europe/Berlin";
class ApiAuthProvider implements AuthenticationProviderInterface
{
    private $doctrine;
    private $intecomService;
    public function __construct($doctrine,$intecomService)
    {
        $this->doctrine = $doctrine;
        $this->intecomService=$intecomService;
    }

    public function authenticate(TokenInterface $token)
    {
        $em=$this->doctrine->getEntityManager();
        $apitoken = $em->getRepository('AcmtoolAppBundle:Token')->findOneBy(array('tokendig' => $token->getTokenDig() ));
        
       
        if($apitoken && $this->validToken($apitoken))
        {

            $user=$em->getRepository('AcmtoolAppBundle:Token')->findUserByToken($apitoken);
            if($user && $user->getIsActive())
            {
                $authenticatedToken=new ApiToken($user->getRoles());
                $authenticatedToken->setUser($user);
                $authenticatedToken->setTokenDig($token->getTokenDig());
                if(($user instanceOf Customer) || ($user instanceOf CustomerUser))
                    $this->intecomService->addCustomAttribute($user->getEmail(),array());
                return $authenticatedToken;
            }
            else
                throw new AuthenticationException('The API authentication failed.');
            
        }
        throw new AuthenticationException('The API authentication failed.');
    }

    private function validToken($apitoken)
    {
        if($apitoken)
        {
            date_default_timezone_set(TIMEZONE);
            $expireDate=$apitoken->getCreationdate()->add(new \DateInterval('PT'.PERIOD.'S'));

            $today =new \DateTime("NOW",new \DateTimeZone(TIMEZONE));
            if($today<$expireDate)
            {
                return true;
            }
            else
            {
                $em=$this->doctrine->getEntityManager();
                $em->remove($apitoken);
                $em->flush();
                return false;
            }
        }
        else
            return false;
    }
    public function supports(TokenInterface $token)
    {
        return $token instanceof ApiToken;
    }
}