<?php
namespace Acmtool\AppBundle\firewall\apiauth;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class ApiToken extends AbstractToken
{
	
	private $tokenDig;
	public function __construct(array $roles = array())
    {
        parent::__construct($roles);

        // Si l'utilisateur a des rôles, on le considère comme authentifié
        $this->setAuthenticated(count($roles) > 0);
    }

    public function getCredentials()
    {
        return '';
    }

    /**
     * Sets the value of tokenDig.
     *
     * @param mixed $tokenDig the token dig
     *
     * @return self
     */
    public function setTokenDig($tokenDig)
    {
        $this->tokenDig = $tokenDig;

        return $this;
    }

    /**
     * Gets the value of tokenDig.
     *
     * @return mixed
     */
    public function getTokenDig()
    {
        return $this->tokenDig;
    }
}