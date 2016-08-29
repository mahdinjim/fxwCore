<?php

namespace Acmtool\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Ticket
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Acmtool\AppBundle\Entity\TicketRepository")
 */
class Ticket
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
     * @ORM\Column(name="diplayId", type="string", length=255)
     */
    private $diplayId;

    /**
     * @var string
     * @Assert\NotBlank(message="The title field is required")
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var float
     *
     * @ORM\Column(name="estimation", type="float",nullable=true)
     */
    private $estimation;
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
     * @var string
     * 
     * @ORM\Column(name="gitSha", type="string", length=255,nullable=true)
     */
    private $gitSha;

    /**
     * @var string
     *
     * @ORM\Column(name="gitUrl", type="string", length=255,nullable=true)
     */
    private $gitUrl;

    /**
    * @Assert\NotBlank
    * @ORM\ManyToOne(targetEntity="Project", inversedBy="tickets")
    * @ORM\JoinColumn(name="project_id",referencedColumnName="id",onDelete="SET NULL")
    */
    private $project;
      /**
     * @var string
     * 
     * @ORM\Column(name="type", type="string", length=255,nullable=true)
     */
    private $type;
    /**
     * @var string
     * @Assert\NotBlank(message="The description field is required")
     * @ORM\Column(name="description", type="text", length=1000)
     */
    private $description;
    /**
     * @var string
     * @Assert\NotBlank(message="The created by field is required")
     * @ORM\Column(name="createdBy", type="string", length=250)
     */
    private $createdBy;
    /**
     * @var string
     * 
     * @ORM\Column(name="rejectionmessage", type="text", length=2000,nullable=true)
     */
    private $rejectionmessage;
     /**
     * @var \DateTime
     *
     * @ORM\Column(name="creationdate", type="datetime",nullable=true)
     */
    private $creationdate;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="starteddate", type="datetime",nullable=true)
     */
    private $starteddate;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="estimateconfirmedddate", type="datetime",nullable=true)
     */
    private $estimateconfirmedddate;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="estimateddate", type="datetime",nullable=true)
     */
    private $estimateddate;
      /**
     * @var \DateTime
     *
     * @ORM\Column(name="productiondate", type="datetime",nullable=true)
     */
    private $productiondate;
     /**
     * @var \DateTime
     *
     * @ORM\Column(name="testingdate", type="datetime",nullable=true)
     */
    private $testingdate;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deliverydate", type="datetime",nullable=true)
     */
    private $deliverydate;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="finisheddate", type="datetime",nullable=true)
     */
    private $finisheddate;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="rejecteddate", type="datetime",nullable=true)
     */
    private $rejecteddate;
    /**
    * @ORM\OneToMany(targetEntity="Task", mappedBy="ticket")
    */
    private $tasks;
    /**
     * @var boolean
     *
     * @ORM\Column(name="isBilled", type="boolean",nullable=true)
     */
    private $isBilled=false;
    /**
     * @var boolean
     *
     * @ORM\Column(name="isPayed", type="boolean",nullable=true)
     */
    private $isPayed=false;
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
     * Set diplayId
     *
     * @param string $diplayId
     * @return Ticket
     */
    public function setDiplayId($diplayId)
    {
        $this->diplayId = $diplayId;
    
        return $this;
    }

    /**
     * Get diplayId
     *
     * @return string 
     */
    public function getDiplayId()
    {
        return $this->diplayId;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Ticket
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
     * Set estimation
     *
     * @param float $estimation
     * @return Ticket
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
     * Set status
     *
     * @param string $status
     * @return Ticket
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
     * Set gitSha
     *
     * @param string $gitSha
     * @return Ticket
     */
    public function setGitSha($gitSha)
    {
        $this->gitSha = $gitSha;
    
        return $this;
    }

    /**
     * Get gitSha
     *
     * @return string 
     */
    public function getGitSha()
    {
        return $this->gitSha;
    }

    /**
     * Set gitUrl
     *
     * @param string $gitUrl
     * @return Ticket
     */
    public function setGitUrl($gitUrl)
    {
        $this->gitUrl = $gitUrl;
    
        return $this;
    }

    /**
     * Get gitUrl
     *
     * @return string 
     */
    public function getGitUrl()
    {
        return $this->gitUrl;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tasks = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set type
     *
     * @param string $type
     * @return Ticket
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Ticket
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
     * Set project
     *
     * @param \Acmtool\AppBundle\Entity\Project $project
     * @return Ticket
     */
    public function setProject(\Acmtool\AppBundle\Entity\Project $project = null)
    {
        $this->project = $project;
    
        return $this;
    }

    /**
     * Get project
     *
     * @return \Acmtool\AppBundle\Entity\Project 
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Add tasks
     *
     * @param \Acmtool\AppBundle\Entity\Task $tasks
     * @return Ticket
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
     * Set createdBy
     *
     * @param string $createdBy
     * @return Ticket
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    
        return $this;
    }

    /**
     * Get createdBy
     *
     * @return string 
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set rejectionmessage
     *
     * @param string $rejectionmessage
     * @return Ticket
     */
    public function setRejectionmessage($rejectionmessage)
    {
        $this->rejectionmessage = $rejectionmessage;
    
        return $this;
    }

    /**
     * Get rejectionmessage
     *
     * @return string 
     */
    public function getRejectionmessage()
    {
        return $this->rejectionmessage;
    }

    /**
     * Set creationdate
     *
     * @param \DateTime $creationdate
     * @return Ticket
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
     * Set realtime
     *
     * @param float $realtime
     * @return Ticket
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
     * Set starteddate
     *
     * @param \DateTime $starteddate
     * @return Ticket
     */
    public function setStarteddate($starteddate)
    {
        $this->starteddate = $starteddate;
    
        return $this;
    }

    /**
     * Get starteddate
     *
     * @return \DateTime 
     */
    public function getStarteddate()
    {
        return $this->starteddate;
    }

    /**
     * Set estimateconfirmedddate
     *
     * @param \DateTime $estimateconfirmedddate
     * @return Ticket
     */
    public function setEstimateconfirmedddate($estimateconfirmedddate)
    {
        $this->estimateconfirmedddate = $estimateconfirmedddate;
    
        return $this;
    }

    /**
     * Get estimateconfirmedddate
     *
     * @return \DateTime 
     */
    public function getEstimateconfirmedddate()
    {
        return $this->estimateconfirmedddate;
    }

    /**
     * Set estimateddate
     *
     * @param \DateTime $estimateddate
     * @return Ticket
     */
    public function setEstimateddate($estimateddate)
    {
        $this->estimateddate = $estimateddate;
    
        return $this;
    }

    /**
     * Get estimateddate
     *
     * @return \DateTime 
     */
    public function getEstimateddate()
    {
        return $this->estimateddate;
    }

    /**
     * Set productiondate
     *
     * @param \DateTime $productiondate
     * @return Ticket
     */
    public function setProductiondate($productiondate)
    {
        $this->productiondate = $productiondate;
    
        return $this;
    }

    /**
     * Get productiondate
     *
     * @return \DateTime 
     */
    public function getProductiondate()
    {
        return $this->productiondate;
    }

    /**
     * Set testingdate
     *
     * @param \DateTime $testingdate
     * @return Ticket
     */
    public function setTestingdate($testingdate)
    {
        $this->testingdate = $testingdate;
    
        return $this;
    }

    /**
     * Get testingdate
     *
     * @return \DateTime 
     */
    public function getTestingdate()
    {
        return $this->testingdate;
    }

    /**
     * Set deliverydate
     *
     * @param \DateTime $deliverydate
     * @return Ticket
     */
    public function setDeliverydate($deliverydate)
    {
        $this->deliverydate = $deliverydate;
    
        return $this;
    }

    /**
     * Get deliverydate
     *
     * @return \DateTime 
     */
    public function getDeliverydate()
    {
        return $this->deliverydate;
    }

    /**
     * Set finisheddate
     *
     * @param \DateTime $finisheddate
     * @return Ticket
     */
    public function setFinisheddate($finisheddate)
    {
        $this->finisheddate = $finisheddate;
    
        return $this;
    }

    /**
     * Get finisheddate
     *
     * @return \DateTime 
     */
    public function getFinisheddate()
    {
        return $this->finisheddate;
    }

    /**
     * Set rejecteddate
     *
     * @param \DateTime $rejecteddate
     * @return Ticket
     */
    public function setRejecteddate($rejecteddate)
    {
        $this->rejecteddate = $rejecteddate;
    
        return $this;
    }

    /**
     * Get rejecteddate
     *
     * @return \DateTime 
     */
    public function getRejecteddate()
    {
        return $this->rejecteddate;
    }

    /**
     * Set isBilled
     *
     * @param boolean $isBilled
     * @return Ticket
     */
    public function setIsBilled($isBilled)
    {
        $this->isBilled = $isBilled;

        return $this;
    }

    /**
     * Get isBilled
     *
     * @return boolean 
     */
    public function getIsBilled()
    {
        return $this->isBilled;
    }

    /**
     * Set isPayed
     *
     * @param boolean $isPayed
     * @return Ticket
     */
    public function setIsPayed($isPayed)
    {
        $this->isPayed = $isPayed;

        return $this;
    }

    /**
     * Get isPayed
     *
     * @return boolean 
     */
    public function getIsPayed()
    {
        return $this->isPayed;
    }
}
