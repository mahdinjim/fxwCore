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
     * @Assert\NotBlank(message="The language field is required")
     * 
     * @ORM\Column(name="language", type="string", length=255)
     */
    protected $language;
    /**
     * @var float
     * @Assert\NotBlank(message="The hourrate field is required")
     * 
     * @ORM\Column(name="hourrate", type="float", length=255)
     */
    protected $hourrate;
    /**
     * @var integer
     * @Assert\NotBlank(message="The level field is required")
     * 
     * @ORM\Column(name="level", type="integer", length=255)
     */
    protected $level;

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
     * @var string
     *
     * @ORM\Column(name="bigphoto", type="string", length=255,nullable=true)
     */
    protected $bigphoto;
    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=255,nullable=true)
     */
    protected $state;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=255,nullable=true)
     */
    protected $country;
    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255,nullable=true)
     */
    protected $city;
    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255,nullable=true)
     */
    protected $title;
    /**
     * @var string
     *
     * @ORM\Column(name="phonecode", type="string", length=255,nullable=true)
     */
    protected $phonecode;
    /**
     * @var string
     *
     * @ORM\Column(name="phonenumber", type="string", length=255,nullable=true)
     */
    protected $phonenumber;
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
    /**
     * Set bigphoto
     *
     * @param string $bigphoto
     * @return TeamMember
     */
    public function setBigPhoto($bigphoto)
    {
        $this->bigphoto = $bigphoto;
    
        return $this;
    }

    /**
     * Get bigphoto
     *
     * @return string 
     */
    public function getBigPhoto()
    {
        return $this->bigphoto;
    }
    /**
     * Set State
     *
     * @param string $statecountry * @return TeamMember
     */
    public function setState($state)
    {
        $this->state = $state;
    
        return $this;
    }

    /**
     * Get state
     *
     * @return string 
     */
    public function getState()
    {
        return $this->state;
    }
    /**
     * Set Country
     *
     * @param string $country
     * @return TeamMember
     */
    public function setCountry($country)
    {
        $this->country = $country;
    
        return $this;
    }

    /**
     * Get country
     *
     * @return string 
     */
    public function getCountry()
    {
        return $this->country;
    }
    /**
     * Set city
     *
     * @param string $city
     * @return TeamMember
     */
    public function setCity($city)
    {
        $this->city = $city;
    
        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }
    /**
     * Set title
     *
     * @param string $title
     * @return TeamMember
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
     * Set phonecode
     *
     * @param string $phonecode
     * @return TeamMember
     */
    public function setPhonecode($phonecode)
    {
        $this->phonecode = $phonecode;
    
        return $this;
    }

    /**
     * Get phonecode
     *
     * @return string 
     */
    public function getPhonecode()
    {
        return $this->phonecode;
    }
    /**
     * Set phonenumber
     *
     * @param string $phonecode
     * @return TeamMember
     */
    public function setPhonenumber($phonenumber)
    {
        $this->phonenumber = $phonenumber;
    
        return $this;
    }

    /**
     * Get phonenumber
     *
     * @return string 
     */
    public function getPhonenumber()
    {
        return $this->phonenumber;
    }
    

    /**
     * Gets the value of language.
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Sets the value of language.
     *
     * @param string $language the language
     *
     * @return self
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Gets the value of hourrate.
     *
     * @return float
     */
    public function getHourrate()
    {
        return $this->hourrate;
    }

    /**
     * Sets the value of hourrate.
     *
     * @param float $hourrate the hourrate
     *
     * @return self
     */
    public function setHourrate($hourrate)
    {
        $this->hourrate = $hourrate;

        return $this;
    }

    /**
     * Gets the value of level.
     *
     * @return integer
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Sets the value of level.
     *
     * @param integer $level the level
     *
     * @return self
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }
}