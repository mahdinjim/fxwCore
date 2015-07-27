<?php

namespace Acmtool\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Acmtool\AppBundle\Entity\TeamMember;

/**
 * DevTeamMember
 *
 * @ORM\MappedSuperclass
 * 
 */
class DevTeamMember extends TeamMember
{
    /**
     * @var string
     *
     * @ORM\Column(name="capacity", type="string", length=255)
     */
    private $capacity;

    /**
     * @var integer
     *
     * @ORM\Column(name="rate", type="integer")
     */
    private $rate;

    /**
     * @var string
     *
     * @ORM\Column(name="skills", type="text")
     */
    private $skills;

    /**
     * Set capacity
     *
     * @param string $capacity
     * @return DevTeamMember
     */
    public function setCapacity($capacity)
    {
        $this->capacity = $capacity;
    
        return $this;
    }

    /**
     * Get capacity
     *
     * @return string 
     */
    public function getCapacity()
    {
        return $this->capacity;
    }

    /**
     * Set rate
     *
     * @param integer $rate
     * @return DevTeamMember
     */
    public function setRate($rate)
    {
        $this->rate = $rate;
    
        return $this;
    }

    /**
     * Get rate
     *
     * @return integer 
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * Set skills
     *
     * @param string $skills
     * @return DevTeamMember
     */
    public function setSkills($skills)
    {
        $this->skills = $skills;
    
        return $this;
    }

    /**
     * Get skills
     *
     * @return string 
     */
    public function getSkills()
    {
        return $this->skills;
    }
}
