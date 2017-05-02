<?php

namespace Acmtool\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Creds
 * @UniqueEntity(fields={"login"},message="This login value is already used")
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Acmtool\AppBundle\Entity\CredsRepository")
 */
class Creds implements \Serializable
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
     * @Assert\NotBlank(message="This login can't be blank")
     * @ORM\Column(name="login", type="string", length=255)
     */
    private $login;

    /**
     * @var string
     * @Assert\NotBlank
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;
    /**
     * @var string
     * @Assert\NotBlank
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;
     /**
    * @ORM\OneToMany(targetEntity="Project", mappedBy="teamleader")
    */
    private $projects;
      /**
    * @ORM\OneToMany(targetEntity="EmailToken", mappedBy="user")
    */
    private $emailtokens;
    /**
    * @ORM\OneToMany(targetEntity="Task", mappedBy="owner")
    */
    private $tasks; 
     /**
    * @ORM\OneToMany(targetEntity="Log", mappedBy="user")
    */
    private $logs;
      /**
    * @ORM\OneToMany(targetEntity="NoNotif", mappedBy="user")
    */
    private $noNotifs;
    /**
    * @ORM\OneToMany(targetEntity="Customer", mappedBy="referencedBy")
    */
    private $refCustomers; 
    /**
    * @ORM\OneToMany(targetEntity="Commission", mappedBy="owner")
    */
    private $commissions;
      /**
    * @ORM\OneToMany(targetEntity="DeviceToken", mappedBy="user")
    */
    private $devicetokens;
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
     * Set login
     *
     * @param string $login
     * @return Creds
     */
    public function setLogin($login)
    {
        $this->login = $login;
    
        return $this;
    }

    /**
     * Get login
     *
     * @return string 
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Creds
     */
    public function setPassword($password)
    {
        $this->password = $password;
    
        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }
      /**
     * Set Title
     *
     * @param string $tile
     * @return Creds
     */
    public function setTitle($title)
    {
        $this->title= $title;
    
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
     * Add projects
     *
     * @param \Acmtool\AppBundle\Entity\Project $projects
     * @return TeamLeader
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
     * Add tasks
     *
     * @param \Acmtool\AppBundle\Entity\Task $tasks
     * @return TeamLeader
     */
    public function addTask(\Acmtool\AppBundle\Entity\Task $tasks)
    {
        $this->tasks[] = $tasks;
    
        return $this;
    }

    /**
     * Remove tasks
     *
     * @param \Acmtool\AppBundle\Entity\Task $tasks
     */
    public function removeTask(\Acmtool\AppBundle\Entity\Task $tasks)
    {
        $this->tasks->removeElement($tasks);
    }

    /**
     * Get tasks
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTasks()
    {
        return $this->tasks;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->projects = new \Doctrine\Common\Collections\ArrayCollection();
        $this->emailtokens = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tasks = new \Doctrine\Common\Collections\ArrayCollection();
        $this->logs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->noNotifs = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add emailtokens
     *
     * @param \Acmtool\AppBundle\Entity\EmailToken $emailtokens
     * @return Creds
     */
    public function addEmailtoken(\Acmtool\AppBundle\Entity\EmailToken $emailtokens)
    {
        $this->emailtokens[] = $emailtokens;

        return $this;
    }

    /**
     * Remove emailtokens
     *
     * @param \Acmtool\AppBundle\Entity\EmailToken $emailtokens
     */
    public function removeEmailtoken(\Acmtool\AppBundle\Entity\EmailToken $emailtokens)
    {
        $this->emailtokens->removeElement($emailtokens);
    }

    /**
     * Get emailtokens
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEmailtokens()
    {
        return $this->emailtokens;
    }

    /**
     * Add logs
     *
     * @param \Acmtool\AppBundle\Entity\Log $logs
     * @return Creds
     */
    public function addLog(\Acmtool\AppBundle\Entity\Log $logs)
    {
        $this->logs[] = $logs;

        return $this;
    }

    /**
     * Remove logs
     *
     * @param \Acmtool\AppBundle\Entity\Log $logs
     */
    public function removeLog(\Acmtool\AppBundle\Entity\Log $logs)
    {
        $this->logs->removeElement($logs);
    }

    /**
     * Get logs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLogs()
    {
        return $this->logs;
    }

    /**
     * Add noNotifs
     *
     * @param \Acmtool\AppBundle\Entity\NoNotif $noNotifs
     * @return Creds
     */
    public function addNoNotif(\Acmtool\AppBundle\Entity\NoNotif $noNotifs)
    {
        $this->noNotifs[] = $noNotifs;

        return $this;
    }

    /**
     * Remove noNotifs
     *
     * @param \Acmtool\AppBundle\Entity\NoNotif $noNotifs
     */
    public function removeNoNotif(\Acmtool\AppBundle\Entity\NoNotif $noNotifs)
    {
        $this->noNotifs->removeElement($noNotifs);
    }

    /**
     * Get noNotifs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getNoNotifs()
    {
        return $this->noNotifs;
    }

    /**
     * Add refCustomers
     *
     * @param \Acmtool\AppBundle\Entity\Customer $refCustomers
     * @return Creds
     */
    public function addRefCustomer(\Acmtool\AppBundle\Entity\Customer $refCustomers)
    {
        $this->refCustomers[] = $refCustomers;

        return $this;
    }

    /**
     * Remove refCustomers
     *
     * @param \Acmtool\AppBundle\Entity\Customer $refCustomers
     */
    public function removeRefCustomer(\Acmtool\AppBundle\Entity\Customer $refCustomers)
    {
        $this->refCustomers->removeElement($refCustomers);
    }

    /**
     * Get refCustomers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRefCustomers()
    {
        return $this->refCustomers;
    }

    /**
     * Add commissions
     *
     * @param \Acmtool\AppBundle\Entity\Commission $commissions
     * @return Creds
     */
    public function addCommission(\Acmtool\AppBundle\Entity\Commission $commissions)
    {
        $this->commissions[] = $commissions;

        return $this;
    }

    /**
     * Remove commissions
     *
     * @param \Acmtool\AppBundle\Entity\Commission $commissions
     */
    public function removeCommission(\Acmtool\AppBundle\Entity\Commission $commissions)
    {
        $this->commissions->removeElement($commissions);
    }

    /**
     * Get commissions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCommissions()
    {
        return $this->commissions;
    }

    /**
     * Add devicetokens
     *
     * @param \Acmtool\AppBundle\Entity\DeviceToken $devicetokens
     * @return Creds
     */
    public function addDevicetoken(\Acmtool\AppBundle\Entity\DeviceToken $devicetokens)
    {
        $this->devicetokens[] = $devicetokens;

        return $this;
    }

    /**
     * Remove devicetokens
     *
     * @param \Acmtool\AppBundle\Entity\DeviceToken $devicetokens
     */
    public function removeDevicetoken(\Acmtool\AppBundle\Entity\DeviceToken $devicetokens)
    {
        $this->devicetokens->removeElement($devicetokens);
    }

    /**
     * Get devicetokens
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDevicetokens()
    {
        return $this->devicetokens;
    }
}
