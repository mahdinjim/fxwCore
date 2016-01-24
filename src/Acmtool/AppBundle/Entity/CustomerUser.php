<?php

namespace Acmtool\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * CustomerUser
 * @UniqueEntity(fields={"email"},message="This email is already used")
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Acmtool\AppBundle\Entity\CustomerUserRepository")
 */
class CustomerUser implements UserInterface, \Serializable
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
     * @var string
     * @Assert\NotBlank(message="the email filed is required")
     * @Assert\Email(message = "The email '{{ value }}' is not a valid email.", checkMX = true, checkHost = true)
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var string
     * 
     * @ORM\Column(name="photo", type="string", length=255,nullable=true)
     */
    private $photo;

    /**
     * @var string
     * @Assert\NotBlank(message="the name field is required")
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     * @Assert\NotBlank(message="the surname field is required")
     * @ORM\Column(name="surname", type="string", length=255)
     */
    private $surname;

    /**
     * @var string
     *
     * @ORM\Column(name="telnumber", type="decimal",nullable=true)
     */
    private $telnumber;
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
    * @Assert\NotBlank
    * @ORM\ManyToOne(targetEntity="Customer", inversedBy="customers")
    * @ORM\JoinColumn(name="company_id",referencedColumnName="id")
    */
    private $company;
     /**
     * @ORM\OneToOne(targetEntity="Token")
     * @ORM\JoinColumn(name="token_id", referencedColumnName="id")
     **/
    private $apitoken;
    /**
     * @var string
     *
     * @ORM\Column(name="phonecode", type="string",nullable=true)
     */
    private $phonecode;
    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string",nullable=true)
     */
    private $title;
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
        return array('ROLE_CUSER');
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
    }
    /**
     * Set email
     *
     * @param string $email
     * @return CustomerUser
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set photo
     *
     * @param string $photo
     * @return CustomerUser
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;
    
        return $this;
    }

    /**
     * Get photo
     *
     * @return string 
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return CustomerUser
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set surname
     *
     * @param string $surname
     * @return CustomerUser
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
    
        return $this;
    }

    /**
     * Get surname
     *
     * @return string 
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set telnumber
     *
     * @param string $telnumber
     * @return CustomerUser
     */
    public function setTelnumber($telnumber)
    {
        $this->telnumber = $telnumber;
    
        return $this;
    }

    /**
     * Get telnumber
     *
     * @return string 
     */
    public function getTelnumber()
    {
        return $this->telnumber;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return CustomerUser
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    
        return $this;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return CustomerUser
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
     * Set credentials
     *
     * @param \Acmtool\AppBundle\Entity\Creds $credentials
     * @return CustomerUser
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
     * Set company
     *
     * @param \Acmtool\AppBundle\Entity\Customer $company
     * @return CustomerUser
     */
    public function setCompany(\Acmtool\AppBundle\Entity\Customer $company = null)
    {
        $this->company = $company;
    
        return $this;
    }

    /**
     * Get company
     *
     * @return \Acmtool\AppBundle\Entity\Customer 
     */
    public function getCompany()
    {
        return $this->company;
    }
    public function getKeyaccount()
    {
        return $this->company->getKeyaccount();
    }

    /**
     * Set apitoken
     *
     * @param \Acmtool\AppBundle\Entity\Token $apitoken
     * @return CustomerUser
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
     * Set phonecode
     *
     * @param string $phonecode
     * @return CustomerUser
     */
    public function setPhonecode($phonecode)
    {
        $this->phonecode = $phonecode;
    
        return $this;
    }

    /**
     * Get phonecode
     *
     * @return string 
     */
    public function getPhonecode()
    {
        return $this->phonecode;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return CustomerUser
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }
}