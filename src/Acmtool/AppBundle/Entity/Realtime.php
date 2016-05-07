<?php

namespace Acmtool\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Realtime
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Realtime
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
     * @var \DateTime
     *@Assert\NotBlank
     * @ORM\Column(name="Date", type="datetime")
     */
    private $date;

    /**
     * @var string
     *@Assert\NotBlank
     * @ORM\Column(name="time", type="float")
     */
    private $time;

    /**
    * @Assert\NotBlank
    * @ORM\ManyToOne(targetEntity="Task", inversedBy="realtimes")
    * @ORM\JoinColumn(name="task_id",referencedColumnName="id",onDelete="SET NULL")
    */
    private $task;
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
     * Set date
     *
     * @param \DateTime $date
     * @return Realtime
     */
    public function setDate($date)
    {
        $this->date = $date;
    
        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set time
     *
     * @param string $time
     * @return Realtime
     */
    public function setTime($time)
    {
        $this->time = $time;
    
        return $this;
    }

    /**
     * Get time
     *
     * @return string 
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set task
     *
     * @param \Acmtool\AppBundle\Entity\Task $task
     * @return Realtime
     */
    public function setTask(\Acmtool\AppBundle\Entity\Task $task = null)
    {
        $this->task = $task;
    
        return $this;
    }

    /**
     * Get task
     *
     * @return \Acmtool\AppBundle\Entity\Task 
     */
    public function getTask()
    {
        return $this->task;
    }
}