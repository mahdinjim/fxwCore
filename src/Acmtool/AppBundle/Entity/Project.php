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

    /**
     * Add developers
     *
     * @param \Acmtool\AppBundle\Entity\Developer $developers
     * @return Project
     */
    public function addDeveloper(\Acmtool\AppBundle\Entity\Developer $developers)
    {
        $this->developers[] = $developers;
    
        return $this;
    }

    /**
     * Remove developers
     *
     * @param \Acmtool\AppBundle\Entity\Developer $developers
     */
    public function removeDeveloper(\Acmtool\AppBundle\Entity\Developer $developers)
    {
        $this->developers->removeElement($developers);
    }

    /**
     * Get developers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDevelopers()
    {
        return $this->developers;
    }

    /**
     * Add designers
     *
     * @param \Acmtool\AppBundle\Entity\Designer $designers
     * @return Project
     */
    public function addDesigner(\Acmtool\AppBundle\Entity\Designer $designers)
    {
        $this->designers[] = $designers;
    
        return $this;
    }

    /**
     * Remove designers
     *
     * @param \Acmtool\AppBundle\Entity\Designer $designers
     */
    public function removeDesigner(\Acmtool\AppBundle\Entity\Designer $designers)
    {
        $this->designers->removeElement($designers);
    }

    /**
     * Get designers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDesigners()
    {
        return $this->designers;
    }

    /**
     * Add testers
     *
     * @param \Acmtool\AppBundle\Entity\Tester $testers
     * @return Project
     */
    public function addTester(\Acmtool\AppBundle\Entity\Tester $testers)
    {
        $this->testers[] = $testers;
    
        return $this;
    }

    /**
     * Remove testers
     *
     * @param \Acmtool\AppBundle\Entity\Tester $testers
     */
    public function removeTester(\Acmtool\AppBundle\Entity\Tester $testers)
    {
        $this->testers->removeElement($testers);
    }

    /**
     * Get testers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTesters()
    {
        return $this->testers;
    }

    /**
     * Add sysadmins
     *
     * @param \Acmtool\AppBundle\Entity\SystemAdmin $sysadmins
     * @return Project
     */
    public function addSysadmin(\Acmtool\AppBundle\Entity\SystemAdmin $sysadmins)
    {
        $this->sysadmins[] = $sysadmins;
    
        return $this;
    }

    /**
     * Remove sysadmins
     *
     * @param \Acmtool\AppBundle\Entity\SystemAdmin $sysadmins
     */
    public function removeSysadmin(\Acmtool\AppBundle\Entity\SystemAdmin $sysadmins)
    {
        $this->sysadmins->removeElement($sysadmins);
    }

    /**
     * Get sysadmins
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSysadmins()
    {
        return $this->sysadmins;
    }

    /**
     * Set owner
     *
     * @param \Acmtool\AppBundle\Entity\Customer $owner
     * @return Project
     */
    public function setOwner(\Acmtool\AppBundle\Entity\Customer $owner = null)
    {
        $this->owner = $owner;
    
        return $this;
    }

    /**
     * Get owner
     *
     * @return \Acmtool\AppBundle\Entity\Customer 
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set keyaccount
     *
     * @param \Acmtool\AppBundle\Entity\KeyAccount $keyaccount
     * @return Project
     */
    public function setKeyaccount(\Acmtool\AppBundle\Entity\KeyAccount $keyaccount = null)
    {
        $this->keyaccount = $keyaccount;
    
        return $this;
    }

    /**
     * Get keyaccount
     *
     * @return \Acmtool\AppBundle\Entity\KeyAccount 
     */
    public function getKeyaccount()
    {
        return $this->keyaccount;
    }

    /**
     * Set teamleader
     *
     * @param \Acmtool\AppBundle\Entity\TeamLeader $teamleader
     * @return Project
     */
    public function setTeamleader(\Acmtool\AppBundle\Entity\TeamLeader $teamleader = null)
    {
        $this->teamleader = $teamleader;
    
        return $this;
    }

    /**
     * Get teamleader
     *
     * @return \Acmtool\AppBundle\Entity\TeamLeader 
     */
    public function getTeamleader()
    {
        return $this->teamleader;
    }
}