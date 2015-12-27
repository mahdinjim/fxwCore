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
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

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
    * @Assert\NotBlank
    * @ORM\ManyToOne(targetEntity="Ticket", inversedBy="tasks")
    * @ORM\JoinColumn(name="ticket_id",referencedColumnName="id",onDelete="SET NULL")
    */
    private $ticket;


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
}