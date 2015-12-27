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
     * @Assert\NotBlank(message="The type field is required")
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;
    /**
     * @var string
     * @Assert\NotBlank(message="The description field is required")
     * @ORM\Column(name="description", type="text", length=1000)
     */
    private $description;
    /**
    * @ORM\OneToMany(targetEntity="Task", mappedBy="ticket")
    */
    private $tasks;
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
}