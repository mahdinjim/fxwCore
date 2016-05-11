<?php

namespace Acmtool\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WorkedHours
 * 
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Acmtool\AppBundle\Entity\WorkedHoursRepository")
 */
class WorkedHours
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
     * @var float
     *
     * @ORM\Column(name="workedhour", type="float")
     */
    private $workedhour;

    /**
     * @var integer
     *
     * @ORM\Column(name="year", type="integer")
     */
    private $year;

    /**
     * @var integer
     *
     * @ORM\Column(name="month", type="integer")
     */
    private $month;

    /**
     * @var integer
     *
     * @ORM\Column(name="day", type="integer")
     */
    private $day;

    /**
     * @var integer
     *
     * @ORM\Column(name="hour", type="integer")
     */
    private $hour;
    /**
     * @var integer
     *
     * @ORM\Column(name="dayOfTheWeek", type="integer")
     */
    private $dayOfTheWeek;
    /**
     * @var integer
     *
     * @ORM\Column(name="week", type="integer")
     */
    private $week;
    /**
     * @var integer
     *
     * @ORM\Column(name="minutes", type="integer")
     */
    private $minutes;
    /**
    * @ORM\ManyToOne(targetEntity="Creds")
    * @ORM\JoinColumn(name="user_id",referencedColumnName="id",onDelete="SET NULL")
    */
    private $user;
    

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
     * Set workedhour
     *
     * @param float $workedhour
     * @return WorkedHours
     */
    public function setWorkedhour($workedhour)
    {
        $this->workedhour = $workedhour;
    
        return $this;
    }

    /**
     * Get workedhour
     *
     * @return float 
     */
    public function getWorkedhour()
    {
        return $this->workedhour;
    }

    /**
     * Set year
     *
     * @param integer $year
     * @return WorkedHours
     */
    public function setYear($year)
    {
        $this->year = $year;
    
        return $this;
    }

    /**
     * Get year
     *
     * @return integer 
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set month
     *
     * @param integer $month
     * @return WorkedHours
     */
    public function setMonth($month)
    {
        $this->month = $month;
    
        return $this;
    }

    /**
     * Get month
     *
     * @return integer 
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * Set day
     *
     * @param integer $day
     * @return WorkedHours
     */
    public function setDay($day)
    {
        $this->day = $day;
    
        return $this;
    }

    /**
     * Get day
     *
     * @return integer 
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * Set hour
     *
     * @param integer $hour
     * @return WorkedHours
     */
    public function setHour($hour)
    {
        $this->hour = $hour;
    
        return $this;
    }

    /**
     * Get hour
     *
     * @return integer 
     */
    public function getHour()
    {
        return $this->hour;
    }

    /**
     * Set minutes
     *
     * @param integer $minutes
     * @return WorkedHours
     */
    public function setMinutes($minutes)
    {
        $this->minutes = $minutes;
    
        return $this;
    }

    /**
     * Get minutes
     *
     * @return integer 
     */
    public function getMinutes()
    {
        return $this->minutes;
    }

    /**
     * Set dayOfTheWeek
     *
     * @param integer $dayOfTheWeek
     * @return WorkedHours
     */
    public function setDayOfTheWeek($dayOfTheWeek)
    {
        $this->dayOfTheWeek = $dayOfTheWeek;
    
        return $this;
    }

    /**
     * Get dayOfTheWeek
     *
     * @return integer 
     */
    public function getDayOfTheWeek()
    {
        return $this->dayOfTheWeek;
    }

    /**
     * Set task
     *
     * @param \Acmtool\AppBundle\Entity\Creds $task
     * @return WorkedHours
     */
    public function setTask(\Acmtool\AppBundle\Entity\Creds $task = null)
    {
        $this->task = $task;
    
        return $this;
    }

    /**
     * Get task
     *
     * @return \Acmtool\AppBundle\Entity\Creds 
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * Set users
     *
     * @param \Acmtool\AppBundle\Entity\Creds $users
     * @return WorkedHours
     */
    public function setUsers(\Acmtool\AppBundle\Entity\Creds $users = null)
    {
        $this->users = $users;
    
        return $this;
    }

    /**
     * Get users
     *
     * @return \Acmtool\AppBundle\Entity\Creds 
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Set user
     *
     * @param \Acmtool\AppBundle\Entity\Creds $user
     * @return WorkedHours
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
     * Set reference
     *
     * @param \Acmtool\AppBundle\Entity\Realtime $reference
     * @return WorkedHours
     */
    public function setReference(\Acmtool\AppBundle\Entity\Realtime $reference = null)
    {
        $this->reference = $reference;
    
        return $this;
    }

    /**
     * Get reference
     *
     * @return \Acmtool\AppBundle\Entity\Realtime 
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set week
     *
     * @param integer $week
     * @return WorkedHours
     */
    public function setWeek($week)
    {
        $this->week = $week;
    
        return $this;
    }

    /**
     * Get week
     *
     * @return integer 
     */
    public function getWeek()
    {
        return $this->week;
    }
}