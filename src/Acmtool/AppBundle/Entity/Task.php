<?php

namespace Acmtool\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Task
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Acmtool\AppBundle\Entity\TaskRepository")
 */
class Task
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
     * @Assert\NotBlank(message="The displayid field is required")
     * @ORM\Column(name="displayId", type="string", length=255)
     */
    private $displayId;
    /**
     * @var string
     * @Assert\NotBlank(message="The description field is required")
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;
    /**
     * @var string
     * @Assert\NotBlank(message="The description field is required")
     * @ORM\Column(name="description", type="string", length=1000)
     */
    private $description;

    /**
     * @var float
     *
     * @ORM\Column(name="estimation", type="float",nullable=true)
     */
    private $estimation;
    /**
     * @var boolean
     *
     * @ORM\Column(name="isStarted", type="boolean",nullable=true)
     */
    private $isStarted;
    /**
     * @var boolean
     *
     * @ORM\Column(name="isFinished", type="boolean",nullable=true)
     */
    private $isFinished;

    /**
     * @var float
     *
     * @ORM\Column(name="realtime", type="float",nullable=true)
     */
    private $realtime;

    /**
     * @var string
     * @Assert\NotBlank(message="The status field is required")
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;
     /**
    * @Assert\NotBlank
    * @ORM\ManyToOne(targetEntity="Ticket", inversedBy="tasks")
    * @ORM\JoinColumn(name="ticket_id",referencedColumnName="id",onDelete="SET NULL")
    */
    private $ticket;
     /**
    * @ORM\ManyToOne(targetEntity="Developer", inversedBy="tasks")
    * @ORM\JoinColumn(name="developer_id",referencedColumnName="id",onDelete="SET NULL")
    */
    private $developer;
    /**
    * @ORM\ManyToOne(targetEntity="Designer", inversedBy="tasks")
    * @ORM\JoinColumn(name="designer_id",referencedColumnName="id",onDelete="SET NULL")
    */
    private $designer;
    /**
    * @ORM\ManyToOne(targetEntity="SystemAdmin", inversedBy="tasks")
    * @ORM\JoinColumn(name="sysadmin_id",referencedColumnName="id",onDelete="SET NULL")
    */
    private $sysadmin;
    /**
    * @ORM\ManyToOne(targetEntity="Tester", inversedBy="tasks")
    * @ORM\JoinColumn(name="tester_id",referencedColumnName="id",onDelete="SET NULL")
    */
    private $tester;
    /**
    * @ORM\ManyToOne(targetEntity="TeamLeader", inversedBy="tasks")
    * @ORM\JoinColumn(name="owner_id",referencedColumnName="id",onDelete="SET NULL")
    */
    private $owner;
     /**
    * @ORM\OneToMany(targetEntity="Realtime", mappedBy="task")
    */

    private $realtimes;

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
     * Set displayId
     *
     * @param string $displayId
     * @return Task
     */
    public function setDisplayId($displayId)
    {
        $this->displayId = $displayId;
    
        return $this;
    }

    /**
     * Get displayId
     *
     * @return string 
     */
    public function getDisplayId()
    {
        return $this->displayId;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Task
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set estimation
     *
     * @param float $estimation
     * @return Task
     */
    public function setEstimation($estimation)
    {
        $this->estimation = $estimation;
    
        return $this;
    }

    /**
     * Get estimation
     *
     * @return float 
     */
    public function getEstimation()
    {
        return $this->estimation;
    }

    /**
     * Set realtime
     *
     * @param float $realtime
     * @return Task
     */
    public function setRealtime($realtime)
    {
        $this->realtime = $realtime;
    
        return $this;
    }

    /**
     * Get realtime
     *
     * @return float 
     */
    public function getRealtime()
    {
        return $this->realtime;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Task
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set ticket
     *
     * @param \Acmtool\AppBundle\Entity\Ticket $ticket
     * @return Task
     */
    public function setTicket(\Acmtool\AppBundle\Entity\Ticket $ticket = null)
    {
        $this->ticket = $ticket;
    
        return $this;
    }

    /**
     * Get ticket
     *
     * @return \Acmtool\AppBundle\Entity\Ticket 
     */
    public function getTicket()
    {
        return $this->ticket;
    }

    /**
     * Set isStarted
     *
     * @param boolean $isStarted
     * @return Task
     */
    public function setIsStarted($isStarted)
    {
        $this->isStarted = $isStarted;
    
        return $this;
    }

    /**
     * Get isStarted
     *
     * @return boolean 
     */
    public function getIsStarted()
    {
        return $this->isStarted;
    }

    /**
     * Set isFinished
     *
     * @param boolean $isFinished
     * @return Task
     */
    public function setIsFinished($isFinished)
    {
        $this->isFinished = $isFinished;
    
        return $this;
    }

    /**
     * Get isFinished
     *
     * @return boolean 
     */
    public function getIsFinished()
    {
        return $this->isFinished;
    }

    /**
     * Set developer
     *
     * @param \Acmtool\AppBundle\Entity\Developer $developer
     * @return Task
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
     * Set designer
     *
     * @param \Acmtool\AppBundle\Entity\Designer $designer
     * @return Task
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
     * Set sysadmin
     *
     * @param \Acmtool\AppBundle\Entity\SystemAdmin $sysadmin
     * @return Task
     */
    public function setSysadmin(\Acmtool\AppBundle\Entity\SystemAdmin $sysadmin = null)
    {
        $this->sysadmin = $sysadmin;
    
        return $this;
    }

    /**
     * Get sysadmin
     *
     * @return \Acmtool\AppBundle\Entity\SystemAdmin 
     */
    public function getSysadmin()
    {
        return $this->sysadmin;
    }

    /**
     * Set tester
     *
     * @param \Acmtool\AppBundle\Entity\Tester $tester
     * @return Task
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
     * Set owner
     *
     * @param \Acmtool\AppBundle\Entity\TeamLeader $owner
     * @return Task
     */
    public function setOwner(\Acmtool\AppBundle\Entity\TeamLeader $owner = null)
    {
        $this->owner = $owner;
    
        return $this;
    }

    /**
     * Get owner
     *
     * @return \Acmtool\AppBundle\Entity\TeamLeader 
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Task
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
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->realtimes = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add realtimes
     *
     * @param \Acmtool\AppBundle\Entity\Realtime $realtimes
     * @return Task
     */
    public function addRealtime(\Acmtool\AppBundle\Entity\Realtime $realtimes)
    {
        $this->realtimes[] = $realtimes;
    
        return $this;
    }

    /**
     * Remove realtimes
     *
     * @param \Acmtool\AppBundle\Entity\Realtime $realtimes
     */
    public function removeRealtime(\Acmtool\AppBundle\Entity\Realtime $realtimes)
    {
        $this->realtimes->removeElement($realtimes);
    }

    /**
     * Get realtimes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRealtimes()
    {
        return $this->realtimes;
    }
}