<?php

namespace Acmtool\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Acmtool\AppBundle\Entity\TeamMember;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * KeyAccount
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Acmtool\AppBundle\Entity\KeyAccountRepository")
 */
class KeyAccount extends TeamMember implements UserInterface, \Serializable
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
    /**
    * @ORM\OneToMany(targetEntity="Customer", mappedBy="keyAccount",cascade={"remove"})
    */
    private $customers;
    /**
    * @ORM\OneToMany(targetEntity="Project", mappedBy="keyaccount",cascade={"remove"})
    */
    private $projects;
    /**
     * @ORM\Column(name="is_partner", type="boolean")
     */
    private $partner = false;
    /**
     * @ORM\Column(name="canmanage", type="boolean")
     */
    private $canmanage = false;
    /**
    * @ORM\Column(name="companyname", type="string", length=255, nullable=true)
    */
    private $companyname;
    public function __construct()
    {
        $this->isActive = true;
        $this->salt = md5(uniqid(null, true));
        $this->customers = new ArrayCollection();
        $this->projects=new ArrayCollection();
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
        if($this->isPartner())
            return array('ROLE_KEYACCOUNT','ROLE_PARTNER');
        else
            return array('ROLE_KEYACCOUNT');
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
     * @return KeyAccount
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
     * @return KeyAccount
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
     /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return Developer
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    
        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }
    /**
     * Add projects
     *
     * @param \Acmtool\AppBundle\Entity\Project $projects
     * @return KeyAccount
     */
    public function addProject(\Acmtool\AppBundle\Entity\Project $projects)
    {
        $this->projects[] = $projects;
    
        return $this;
    }

    /**
     * Remove projects
     *
     * @param \Acmtool\AppBundle\Entity\Project $projects
     */
    public function removeProject(\Acmtool\AppBundle\Entity\Project $projects)
    {
        $this->projects->removeElement($projects);
    }

    /**
     * Get projects
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProjects()
    {
        return $this->projects;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return KeyAccount
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Set partner
     *
     * @param boolean $partner
     * @return KeyAccount
     */
    public function setPartner($partner)
    {
        $this->partner = $partner;

        return $this;
    }

    /**
     * Get partner
     *
     * @return boolean 
     */
    public function isPartner()
    {
        return $this->partner;
    }

    /**
     * Add customers
     *
     * @param \Acmtool\AppBundle\Entity\Customer $customers
     * @return KeyAccount
     */
    public function addCustomer(\Acmtool\AppBundle\Entity\Customer $customers)
    {
        $this->customers[] = $customers;

        return $this;
    }

    /**
     * Remove customers
     *
     * @param \Acmtool\AppBundle\Entity\Customer $customers
     */
    public function removeCustomer(\Acmtool\AppBundle\Entity\Customer $customers)
    {
        $this->customers->removeElement($customers);
    }

    /**
     * Get customers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCustomers()
    {
        return $this->customers;
    }

    /**
     * Get partner
     *
     * @return boolean 
     */
    public function getPartner()
    {
        return $this->partner;
    }

    /**
     * Set companyname
     *
     * @param string $companyname
     * @return KeyAccount
     */
    public function setCompanyname($companyname)
    {
        $this->companyname = $companyname;

        return $this;
    }

    /**
     * Get companyname
     *
     * @return string 
     */
    public function getCompanyname()
    {
        return $this->companyname;
    }

    /**
     * Set canmanage
     *
     * @param boolean $canmanage
     * @return KeyAccount
     */
    public function setCanmanage($canmanage)
    {
        $this->canmanage = $canmanage;

        return $this;
    }

    /**
     * Get canmanage
     *
     * @return boolean 
     */
    public function getCanmanage()
    {
        return $this->canmanage;
    }
}
