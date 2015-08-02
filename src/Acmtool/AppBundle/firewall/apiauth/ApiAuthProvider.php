<?php
namespace Acmtool\AppBundle\firewall\apiauth;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\NonceExpiredException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Acmtool\AppBundle\firewall\apiauth\ApiToken;
Const PERIOD=3600;
class ApiAuthProvider implements AuthenticationProviderInterface
{
    private $doctrine;
    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function authenticate(TokenInterface $token)
    {
        $em=$this->doctrine->getEntityManager();
        $apitoken = $em->getRepository('AcmtoolAppBundle:Token')->findOneBy(array('tokendig' => $token->getTokenDig() ));
        
        if(!$apitoken)
            throw new AuthenticationException('The API authentication failed.');
        if($apitoken && $this->validToken($apitoken))
        {
            $user=$em->getRepository('AcmtoolAppBundle:Token')->findUserByToken($apitoken);
            if($user)
            {
                $authenticatedToken=new ApiToken();
                $authenticatedToken->setUser($user);
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
            date_default_timezone_set('UTC');
            $expireDate=$apitoken->getCreationdate()->add(new \DateInterval('PT'.PERIOD.'S'));

            $today =new \DateTime("NOW",new \DateTimeZone('UTC'));
            

            if($today<$expireDate)
                return true;
            else
            {
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