<?php

namespace Acmtool\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * TeamMember
 * @UniqueEntity(fields={"email"},message="This email is already used")
 * @ORM\MappedSuperclass
 */
class TeamMember
{
    

    /**
     * @var string
     * 
     * @ORM\Column(name="description", type="string", length=2000,nullable=true)
     */
    protected $description;

    /**
     * @var string
     * @Assert\NotBlank(message="The email field is required")
     * @Assert\Email(message = "The email '{{ value }}' is not a valid email.", checkMX = true, checkHost = true)
     * @ORM\Column(name="email", type="string", length=255)
     */
    protected $email;

    /**
     * @var string
     * @Assert\NotBlank(message="The name field is required")
     * 
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var string
     * @Assert\NotBlank(message="The surname field is required")
     * @ORM\Column(name="surname", type="string", length=255)
     */
    protected $surname;

    /**
     * @var string
     *
     * @ORM\Column(name="photo", type="string", length=255,nullable=true)
     */
    protected $photo;

    /**
     * Set description
     *
     * @param string $description
     * @return TeamMember
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
     * Set email
     *
     * @param string $email
     * @return TeamMember
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return TeamMember
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
     * Set surname
     *
     * @param string $surname
     * @return TeamMember
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
    
        return $this;
    }

    /**
     * Get surname
     *
     * @return string 
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set photo
     *
     * @param string $photo
     * @return TeamMember
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;
    
        return $this;
    }

    /**
     * Get photo
     *
     * @return string 
     */
    public function getPhoto()
    {
        return $this->photo;
    }
    
}