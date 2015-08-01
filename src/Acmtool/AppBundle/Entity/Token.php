<?php

namespace Acmtool\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Token
 * @ORM\Entity(repositoryClass="Acmtool\AppBundle\Entity\TokenRepository")
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Acmtool\AppBundle\Entity\TokenRepository")
 */
class Token
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
     *
     * @ORM\Column(name="tokendig", type="string", length=255)
     */
    private $tokendig;

    /**
     * @var string
     *
     * @ORM\Column(name="userrole", type="string", length=255)
     */
    private $userrole;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creationdate", type="datetime")
     */
    private $creationdate;

     /**
     * @ORM\OneToOne(targetEntity="Admin", mappedBy="apitoken")
     **/
    private $admin;
    /**
     * @ORM\OneToOne(targetEntity="Customer", mappedBy="apitoken")
     **/
    private $customer;
    /**
     * @ORM\OneToOne(targetEntity="CustomerUser", mappedBy="apitoken")
     **/
    private $customeruser;

     /**
     * @ORM\OneToOne(targetEntity="Designer", mappedBy="apitoken")
     **/
    private $designer;

     /**
     * @ORM\OneToOne(targetEntity="Developer", mappedBy="apitoken")
     **/
    private $developer;

     /**
     * @ORM\OneToOne(targetEntity="KeyAccount", mappedBy="apitoken")
     **/
    private $keyaccount;

     /**
     * @ORM\OneToOne(targetEntity="SystemAdmin", mappedBy="apitoken")
     **/
    private $systemadmin;

     /**
     * @ORM\OneToOne(targetEntity="TeamLeader", mappedBy="apitoken")
     **/
    private $teamleader;
     /**
     * @ORM\OneToOne(targetEntity="Tester", mappedBy="apitoken")
     **/
    private $tester;
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
     * Set tokendig
     *
     * @param string $tokendig
     * @return Token
     */
    public function setTokendig($tokendig)
    {
        $this->tokendig = $tokendig;
    
        return $this;
    }

    /**
     * Get tokendig
     *
     * @return string 
     */
    public function getTokendig()
    {
        return $this->tokendig;
    }

    /**
     * Set creationdate
     *
     * @param \DateTime $creationdate
     * @return Token
     */
    public function setCreationdate($creationdate)
    {
        $this->creationdate = $creationdate;
    
        return $this;
    }

    /**
     * Get creationdate
     *
     * @return \DateTime 
     */
    public function getCreationdate()
    {
        return $this->creationdate;
    }

    /**
     * Set admin
     *
     * @param \Acmtool\AppBundle\Entity\Admin $admin
     * @return Token
     */
    public function setAdmin(\Acmtool\AppBundle\Entity\Admin $admin = null)
    {
        $this->admin = $admin;
    
        return $this;
    }

    /**
     * Get admin
     *
     * @return \Acmtool\AppBundle\Entity\Admin 
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * Set customer
     *
     * @param \Acmtool\AppBundle\Entity\Customer $customer
     * @return Token
     */
    public function setCustomer(\Acmtool\AppBundle\Entity\Customer $customer = null)
    {
        $this->customer = $customer;
    
        return $this;
    }

    /**
     * Get customer
     *
     * @return \Acmtool\AppBundle\Entity\Customer 
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Set customeruser
     *
     * @param \Acmtool\AppBundle\Entity\CustomerUser $customeruser
     * @return Token
     */
    public function setCustomeruser(\Acmtool\AppBundle\Entity\CustomerUser $customeruser = null)
    {
        $this->customeruser = $customeruser;
    
        return $this;
    }

    /**
     * Get customeruser
     *
     * @return \Acmtool\AppBundle\Entity\CustomerUser 
     */
    public function getCustomeruser()
    {
        return $this->customeruser;
    }

    /**
     * Set designer
     *
     * @param \Acmtool\AppBundle\Entity\Designer $designer
     * @return Token
     */
    public function setDesigner(\Acmtool\AppBundle\Entity\Designer $designer = null)
    {
        $this->designer = $designer;
    
        return $this;
    }

    /**
     * Get designer
     *
     * @return \Acmtool\AppBundle\Entity\Designer 
     */
    public function getDesigner()
    {
        return $this->designer;
    }

    /**
     * Set developer
     *
     * @param \Acmtool\AppBundle\Entity\Developer $developer
     * @return Token
     */
    public function setDeveloper(\Acmtool\AppBundle\Entity\Developer $developer = null)
    {
        $this->developer = $developer;
    
        return $this;
    }

    /**
     * Get developer
     *
     * @return \Acmtool\AppBundle\Entity\Developer 
     */
    public function getDeveloper()
    {
        return $this->developer;
    }

    /**
     * Set keyaccount
     *
     * @param \Acmtool\AppBundle\Entity\KeyAccount $keyaccount
     * @return Token
     */
    public function setKeyaccount(\Acmtool\AppBundle\Entity\KeyAccount $keyaccount = null)
    {
        $this->keyaccount = $keyaccount;
    
        return $this;
    }

    /**
     * Get keyaccount
     *
     * @return \Acmtool\AppBundle\Entity\KeyAccount 
     */
    public function getKeyaccount()
    {
        return $this->keyaccount;
    }

    /**
     * Set systemadmin
     *
     * @param \Acmtool\AppBundle\Entity\SystemAdmin $systemadmin
     * @return Token
     */
    public function setSystemadmin(\Acmtool\AppBundle\Entity\SystemAdmin $systemadmin = null)
    {
        $this->systemadmin = $systemadmin;
    
        return $this;
    }

    /**
     * Get systemadmin
     *
     * @return \Acmtool\AppBundle\Entity\SystemAdmin 
     */
    public function getSystemadmin()
    {
        return $this->systemadmin;
    }

    /**
     * Set teamleader
     *
     * @param \Acmtool\AppBundle\Entity\TeamLeader $teamleader
     * @return Token
     */
    public function setTeamleader(\Acmtool\AppBundle\Entity\TeamLeader $teamleader = null)
    {
        $this->teamleader = $teamleader;
    
        return $this;
    }

    /**
     * Get teamleader
     *
     * @return \Acmtool\AppBundle\Entity\TeamLeader 
     */
    public function getTeamleader()
    {
        return $this->teamleader;
    }

    /**
     * Set tester
     *
     * @param \Acmtool\AppBundle\Entity\Tester $tester
     * @return Token
     */
    public function setTester(\Acmtool\AppBundle\Entity\Tester $tester = null)
    {
        $this->tester = $tester;
    
        return $this;
    }

    /**
     * Get tester
     *
     * @return \Acmtool\AppBundle\Entity\Tester 
     */
    public function getTester()
    {
        return $this->tester;
    }

    /**
     * Set userrole
     *
     * @param string $userrole
     * @return Token
     */
    public function setUserrole($userrole)
    {
        $this->userrole = $userrole;
    
        return $this;
    }

    /**
     * Get userrole
     *
     * @return string 
     */
    public function getUserrole()
    {
        return $this->userrole;
    }
}