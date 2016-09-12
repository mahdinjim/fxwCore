<?php
namespace Acmtool\AppBundle\firewall\emailauth;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\NonceExpiredException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Acmtool\AppBundle\firewall\emailauth\EmailToken;
Const PERIOD=3600;
Const TIMEZONE="Europe/Berlin";
class EmailAuthProvider implements AuthenticationProviderInterface
{
    private $doctrine;
    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function authenticate(TokenInterface $token)
    {
        $em=$this->doctrine->getEntityManager();
        $emailtoken = $em->getRepository('AcmtoolAppBundle:EmailToken')->findOneBy(array('tokendig' => $token->getTokenDig() ));
        
       
        if($emailtoken && $this->validToken($emailtoken))
        {

            $user=$em->getRepository('AcmtoolAppBundle:EmailToken')->getUser($emailtoken);
            if($user)
            {
                $authenticatedToken=new EmailToken($user->getRoles());
                $authenticatedToken->setUser($user);
                $authenticatedToken->setTokenDig($token->getTokenDig());
                return $authenticatedToken;
            }
            else
                throw new AuthenticationException('The Email authentication failed.');
            
        }
        throw new AuthenticationException('The Email authentication failed.');
    }

    private function validToken($emailtoken)
    {
        if($emailtoken)
        {
            date_default_timezone_set(TIMEZONE);
            $expireDate=$emailtoken->getExpirationdate();

            $today =new \DateTime("NOW",new \DateTimeZone(TIMEZONE));
            if($today<$expireDate)
            {
                return true;
            }
            else
            {
                $em=$this->doctrine->getEntityManager();
                $em->remove($emailtoken);
                $em->flush();
                return false;
            }
        }
        else
            return false;
    }
    public function supports(TokenInterface $token)
    {
        return $token instanceof EmailToken;
    }
}