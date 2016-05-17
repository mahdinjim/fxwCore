<?php

namespace Acmtool\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Acmtool\AppBundle\Entity\DevTeamMember;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * TeamLeader
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Acmtool\AppBundle\Entity\TeamLeaderRepository")
 */
//For this version teamleader role is not used but we keep this for later versions,
class TeamLeader extends DevTeamMember implements UserInterface, \Serializable
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
     /**
     * @Assert\NotBlank
     * @ORM\OneToOne(targetEntity="Creds",cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="cred_id", referencedColumnName="id")
     **/
    private $credentials;
      /**
     * @ORM\Column(type="string", length=32)
     */
    private $salt;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;
     /**
     * @ORM\OneToOne(targetEntity="Token")
     * @ORM\JoinColumn(name="token_id", referencedColumnName="id",onDelete="SET NULL")
     **/
    private $apitoken;
    
    

    public function __construct()
    {
        $this->isActive = true;
        $this->salt = md5(uniqid(null, true));
        
    }
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
       /**
     * @inheritDoc
     */
    public function getSalt()
    {
        return $this->salt;
    }
    /**
     * @inheritDoc
     */
    public function getUsername()
    {
        return $this->credentials->getLogin();
    }
    /**
     * @inheritDoc
     */
    public function getPassword()
    {
        return $this->credentials->getPassword();
    }

    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        return array('ROLE_TEAMLEADER');
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
    }
    /**
     * Set credentials
     *
     * @param \Acmtool\AppBundle\Entity\Creds $credentials
     * @return Customer
     */
    public function setCredentials(\Acmtool\AppBundle\Entity\Creds $credentials = null)
    {
        $this->credentials = $credentials;
    
        return $this;
    }

    /**
     * Get credentials
     *
     * @return \Acmtool\AppBundle\Entity\Creds 
     */
    public function getCredentials()
    {
        return $this->credentials;
    }
    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
        ) = unserialize($serialized);
    }
    /**
     * Set apitoken
     *
     * @param \Acmtool\AppBundle\Entity\Token $apitoken
     * @return TeamLeader
     */
    public function setApitoken(\Acmtool\AppBundle\Entity\Token $apitoken = null)
    {
        $this->apitoken = $apitoken;
    
        return $this;
    }

    /**
     * Get apitoken
     *
     * @return \Acmtool\AppBundle\Entity\Token 
     */
    public function getApitoken()
    {
        return $this->apitoken;
    }
   
    
}
