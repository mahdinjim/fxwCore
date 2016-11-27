<?php

namespace Acmtool\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Log
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Acmtool\AppBundle\Entity\LogRepository")
 */
class Log
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
     * @ORM\Column(name="text", type="string", length=1000)
     */
    private $text;
    /**
     * @var string
     *
     * @ORM\Column(name="item", type="string", length=255)
     */
    private $item;
    /**
     * @var string
     *
     * @ORM\Column(name="action", type="string", length=255)
     */
    private $action;
    /**
    * @ORM\ManyToOne(targetEntity="Creds", inversedBy="logs")
    * @ORM\JoinColumn(name="user_id",referencedColumnName="id",onDelete="SET NULL")
    */
    private $user;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creationdate", type="datetime",nullable=true)
     */
    private $creationdate;
     /**
    * @ORM\ManyToOne(targetEntity="Project", inversedBy="logs")
    * @ORM\JoinColumn(name="project_id",referencedColumnName="id",onDelete="SET NULL")
    */
    private $project;
     /**
    * @ORM\ManyToOne(targetEntity="Ticket", inversedBy="logs")
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
     * Set action
     *
     * @param string $action
     * @return Log
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action
     *
     * @return string 
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set user
     *
     * @param \Acmtool\AppBundle\Entity\Creds $user
     * @return Log
     */
    public function setUser(\Acmtool\AppBundle\Entity\Creds $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Acmtool\AppBundle\Entity\Creds 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set project
     *
     * @param \Acmtool\AppBundle\Entity\Project $project
     * @return Log
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
     * @return Log
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
     * Set creationdate
     *
     * @param \DateTime $creationdate
     * @return Log
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
     * Set text
     *
     * @param string $text
     * @return Log
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set item
     *
     * @param string $item
     * @return Log
     */
    public function setItem($item)
    {
        $this->item = $item;

        return $this;
    }

    /**
     * Get item
     *
     * @return string 
     */
    public function getItem()
    {
        return $this->item;
    }
}
