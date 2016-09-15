<?php

namespace Acmtool\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * ProjectDocument
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ProjectDocument
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255)
     */
    private $path;
     /**
    * @Assert\NotBlank
    * @ORM\ManyToOne(targetEntity="Project", inversedBy="documents")
    * @ORM\JoinColumn(name="project_id",referencedColumnName="id",onDelete="SET NULL")
    */
    private $project;
    /**
    * @Assert\NotBlank
    * @ORM\ManyToOne(targetEntity="Ticket", inversedBy="documents")
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
     * Set name
     *
     * @param string $name
     * @return ProjectDocument
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
     * Set path
     *
     * @param string $path
     * @return ProjectDocument
     */
    public function setPath($path)
    {

        $this->path = $path;
    
        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        $basepath=__DIR__.'/../../../../web'.'/uploads/pdocs';
        return $this->path;
    }

    /**
     * Set project
     *
     * @param \Acmtool\AppBundle\Entity\Project $project
     * @return ProjectDocument
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
     * Set ticket
     *
     * @param \Acmtool\AppBundle\Entity\Ticket $ticket
     * @return ProjectDocument
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
