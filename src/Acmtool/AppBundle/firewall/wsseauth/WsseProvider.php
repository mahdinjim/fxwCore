<?php
namespace Acmtool\AppBundle\firewall\wsseauth;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\NonceExpiredException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Acmtool\AppBundle\firewall\wsseauth\WsseToken;
use Symfony\Component\Security\Core\Util\StringUtils;

class WsseProvider implements AuthenticationProviderInterface
{
    private $doctrine;
    private $cacheDir;

    public function __construct($doctrine, $cacheDir)
    {
        $this->doctrine = $doctrine;
        $this->cacheDir = $cacheDir;
    }

    public function authenticate(TokenInterface $token)
    {
        $em=$this->doctrine->getEntityManager();
        $user = $em->getRepository('AcmtoolAppBundle:Bot')->findOneBy(array('login' =>$token->getUsername()));

        if ($user && $this->validateDigest($token->digest, $token->nonce, $token->created, $user->getPassword())) {
            $authenticatedToken = new WsseToken($user->getRoles());
            $authenticatedToken->setUser($user);

            return $authenticatedToken;
        }

        throw new AuthenticationException('The WSSE authentication failed.');
    }

   
    protected function validateDigest($digest, $nonce, $created, $secret)
    {
        // Check created time is not in the future
        if (strtotime($created) > time()) {
            return false;
        }

        // Expire timestamp after 5 minutes
        if (time() - strtotime($created) > 300) {
            return false;
        }

        // Validate that the nonce is *not* used in the last 5 minutes
        // if it has, this could be a replay attack
        if (
            file_exists($this->cacheDir.'/'.md5($nonce))
            && file_get_contents($this->cacheDir.'/'.md5($nonce)) + 300 > time()
        ) {
            throw new NonceExpiredException('Previously used nonce detected');
        }
        // If cache directory does not exist we create it
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0777, true);
        }
        file_put_contents($this->cacheDir.'/'.md5($nonce), time());

        // Validate Secret
        $expected = base64_encode(sha1(base64_decode($nonce).$created.$secret, true));

        return StringUtils::equals($expected, $digest);
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof WsseToken;
    }
}