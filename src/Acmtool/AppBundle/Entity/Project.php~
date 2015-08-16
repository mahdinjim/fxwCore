<?php

namespace Acmtool\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Project
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Acmtool\AppBundle\Entity\ProjectRepository")
 */
class Project
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
     * @ORM\Column(name="description", type="string", length=255,nullable=true)
     */
    private $description;

    /**
     * @var string
     * @Assert\NotBlank(message="The name field is required")
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="startingdate", type="datetime")
     */
    private $startingdate;
    /**
    * @ORM\ManyToMany(targetEntity="Developer",inversedBy="projects")
    * @ORM\JoinTable(name="project_developers")
    */
    private $developers;
    /**
    * @ORM\ManyToMany(targetEntity="Designer",inversedBy="projects")
    * @ORM\JoinTable(name="project_designers")
    */
    private $designers;
    /**
    * @ORM\ManyToMany(targetEntity="Tester",inversedBy="projects")
    * @ORM\JoinTable(name="project_testers")
    */
    private $testers;
    /**
    * @ORM\ManyToMany(targetEntity="SystemAdmin",inversedBy="projects")
    * @ORM\JoinTable(name="project_sysadmins")
    */
    private $sysadmins;
     /**
    * @Assert\NotBlank
    * @ORM\ManyToOne(targetEntity="Customer", inversedBy="projects")
    * @ORM\JoinColumn(name="company_id",referencedColumnName="id",onDelete="SET NULL")
    */
    private $owner;
     /**
    * @Assert\NotBlank
    * @ORM\ManyToOne(targetEntity="KeyAccount", inversedBy="projects")
    * @ORM\JoinColumn(name="keyaccount_id",referencedColumnName="id",onDelete="SET NULL")
    */
    private $keyaccount;
     /**
    * @Assert\NotBlank
    * @ORM\ManyToOne(targetEntity="TeamLeader", inversedBy="projects")
    * @ORM\JoinColumn(name="teamleader_id",referencedColumnName="id",onDelete="SET NULL")
    */
    private $teamleader;
    function __construct() {
        $this->sysadmins=new ArrayCollection();
        $this->developers=new ArrayCollection();
        $this->testers=new ArrayCollection();
        $this->designers=new ArrayCollection();
    }
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
     * Set description
     *
     * @param string $description
     * @return Project
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
     * Set name
     *
     * @param string $name
     * @return Project
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
     * Set startingdate
     *
     * @param \DateTime $startingdate
     * @return Project
     */
    public function setStartingdate($startingdate)
    {
        $this->startingdate = $startingdate;
    
        return $this;
    }

    /**
     * Get startingdate
     *
     * @return \DateTime 
     */
    public function getStartingdate()
    {
        return $this->startingdate;
    }
}
