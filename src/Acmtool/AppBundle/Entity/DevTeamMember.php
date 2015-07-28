<?php

namespace Acmtool\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Acmtool\AppBundle\Entity\TeamMember;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
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
     * @Assert\NotBlank
     * @ORM\Column(name="capacity", type="string", length=255)
     */
    protected $capacity;

    /**
     * @var integer
     *
     * @ORM\Column(name="rate", type="integer")
     */
    protected $rate;

    /**
     * @var string
     * @Assert\NotBlank
     * @ORM\Column(name="skills", type="text")
     */
    protected $skills;

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
