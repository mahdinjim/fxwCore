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
     * this field is for client breifing of the project
     * @ORM\Column(name="description", type="text",nullable=true)
     */
    private $description;
    /**
     * @var string
     * this field is for flexwork agent to add it's own description of the project and that description will be used in contract
     * @ORM\Column(name="descriptioncontract", type="text",nullable=true)
     */
    private $descriptionContract;
     /**
     * @var boolean
     *
     * @ORM\Column(name="signedContract", type="boolean",nullable=true)
     */
    private $signedContract=false;
    /**
     * @var boolean
     *
     * @ORM\Column(name="contractPrepared", type="boolean",nullable=true)
     */
    private $contractPrepared=false;
    /**
     * @var string
     * @Assert\NotBlank(message="The name field is required")
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;
     /**
     * @var string
     * @Assert\NotBlank(message="The state field is required")
     * @ORM\Column(name="state", type="string", length=255)
     */
    private $state;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="startingdate", type="datetime",nullable=true)
     */
    private $startingdate;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="signaturedate", type="datetime",nullable=true)
     */
    private $signaturedate;
    /**
     * @var \float
     *
     * @ORM\Column(name="rate", type="float",nullable=true)
     */
    private $rate;
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
    * @ORM\ManyToOne(targetEntity="Creds", inversedBy="projects")
    * @ORM\JoinColumn(name="teamleader_id",referencedColumnName="id",onDelete="SET NULL")
    */
    private $teamleader;
    
     /**
    * @ORM\OneToMany(targetEntity="ProjectConfig", mappedBy="project")
    */

    private $configs;
    /**
    * @ORM\OneToMany(targetEntity="ProjectDocument", mappedBy="project")
    */
    private $documents;
    /**
     * @var string
     * 
     * @ORM\Column(name="channelid", type="string", length=255,nullable=true)
     */
    private $channelid;
    /**
     * @var string
     * 
     * @ORM\Column(name="pivotalId", type="string", length=255,nullable=true)
     */
    private $pivotal_id;
    /**
     * @var string
     * 
     * @ORM\Column(name="pivotalUrl", type="string", length=255,nullable=true)
     */
    private $pivotalUrl;
    /**
     * @var string
     * 
     * @ORM\Column(name="pivotalName", type="string", length=255,nullable=true)
     */
    private $pivotalName;
    /**
     * @var string
     * 
     * @ORM\Column(name="repoId", type="string", length=255,nullable=true)
     */
    private $repoId;
    /**
     * @var string
     * 
     * @ORM\Column(name="repoUrl", type="string", length=255,nullable=true)
     */
    private $repoUrl;
     /**
     * @var string
     * 
     * @ORM\Column(name="repoName", type="string", length=255,nullable=true)
     */
    private $repoName;
    /**
     * @var float
     * 
     * @ORM\Column(name="budget", type="float",nullable=true)
     */
    private $budget;
     /**
    * @ORM\OneToMany(targetEntity="Ticket", mappedBy="project")
    */
    private $tickets;
     /**
    * @ORM\OneToMany(targetEntity="Invoice", mappedBy="project")
    */
    private $invoices;
    /**
     * @var string
     * 
     * @ORM\Column(name="projectSkills", type="string", length=1000,nullable=true)
     */
    private $projectSkills;
    /**
     * @var string
     * 
     * @ORM\Column(name="displayid", type="string", length=255)
     */
    private $displayid;
    /**
     * @var float
     * 
     * @ORM\Column(name="estimation", type="float",nullable=true)
     */
    private $estimation;
    /**
     * @var float
     * 
     * @ORM\Column(name="period", type="float",nullable=true)
     */
    private $period;
     /**
    * @ORM\OneToMany(targetEntity="Log", mappedBy="project")
    */
    private $logs;
      /**
    * @ORM\OneToMany(targetEntity="NoNotif", mappedBy="project")
    */
    private $noNotifs;
    /**
    * @ORM\OneToMany(targetEntity="LinkedProject", mappedBy="project")
    */
    private $pmtools;
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
    public function setTeamleader(\Acmtool\AppBundle\Entity\Creds $teamleader = null)
    {
        $this->teamleader = $teamleader;
    
        return $this;
    }

    /**
     * Get teamleader
     *
     * @return \Acmtool\AppBundle\Entity\Creds 
     */
    public function getTeamleader()
    {
        return $this->teamleader;
    }

    /**
     * Set state
     *
     * @param string $state
     * @return Project
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
     * Set channelid
     *
     * @param string $channelid
     * @return Project
     */
    public function setChannelid($channelid)
    {
        $this->channelid = $channelid;
    
        return $this;
    }

    /**
     * Get channelid
     *
     * @return string 
     */
    public function getChannelid()
    {
        return $this->channelid;
    }

    /**
     * Set pivotal_id
     *
     * @param string $pivotalId
     * @return Project
     */
    public function setPivotalId($pivotalId)
    {
        $this->pivotal_id = $pivotalId;
    
        return $this;
    }

    /**
     * Get pivotal_id
     *
     * @return string 
     */
    public function getPivotalId()
    {
        return $this->pivotal_id;
    }

    /**
     * Set github_id
     *
     * @param string $githubId
     * @return Project
     */
    public function setGithubId($githubId)
    {
        $this->github_id = $githubId;
    
        return $this;
    }

    /**
     * Get github_id
     *
     * @return string 
     */
    public function getGithubId()
    {
        return $this->github_id;
    }

    /**
     * Set budget
     *
     * @param float $budget
     * @return Project
     */
    public function setBudget($budget)
    {
        $this->budget = $budget;
    
        return $this;
    }

    /**
     * Get budget
     *
     * @return float 
     */
    public function getBudget()
    {
        return $this->budget;
    }

    /**
     * Add tickets
     *
     * @param \Acmtool\AppBundle\Entity\Ticket $tickets
     * @return Project
     */
    public function addTicket(\Acmtool\AppBundle\Entity\Ticket $tickets)
    {
        $this->tickets[] = $tickets;
    
        return $this;
    }

    /**
     * Remove tickets
     *
     * @param \Acmtool\AppBundle\Entity\Ticket $tickets
     */
    public function removeTicket(\Acmtool\AppBundle\Entity\Ticket $tickets)
    {
        $this->tickets->removeElement($tickets);
    }

    /**
     * Get tickets
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTickets()
    {
        return array_reverse($this->tickets->toArray());
    }

    /**
     * Set pivotalUrl
     *
     * @param string $pivotalUrl
     * @return Project
     */
    public function setPivotalUrl($pivotalUrl)
    {
        $this->pivotalUrl = $pivotalUrl;
    
        return $this;
    }

    /**
     * Get pivotalUrl
     *
     * @return string 
     */
    public function getPivotalUrl()
    {
        return $this->pivotalUrl;
    }

    /**
     * Set pivotalName
     *
     * @param string $pivotalName
     * @return Project
     */
    public function setPivotalName($pivotalName)
    {
        $this->pivotalName = $pivotalName;
    
        return $this;
    }

    /**
     * Get pivotalName
     *
     * @return string 
     */
    public function getPivotalName()
    {
        return $this->pivotalName;
    }

    /**
     * Set repoId
     *
     * @param string $repoId
     * @return Project
     */
    public function setRepoId($repoId)
    {
        $this->repoId = $repoId;
    
        return $this;
    }

    /**
     * Get repoId
     *
     * @return string 
     */
    public function getRepoId()
    {
        return $this->repoId;
    }

    /**
     * Set repoUrl
     *
     * @param string $repoUrl
     * @return Project
     */
    public function setRepoUrl($repoUrl)
    {
        $this->repoUrl = $repoUrl;
    
        return $this;
    }

    /**
     * Get repoUrl
     *
     * @return string 
     */
    public function getRepoUrl()
    {
        return $this->repoUrl;
    }

    /**
     * Set repoName
     *
     * @param string $repoName
     * @return Project
     */
    public function setRepoName($repoName)
    {
        $this->repoName = $repoName;
    
        return $this;
    }

    /**
     * Get repoName
     *
     * @return string 
     */
    public function getRepoName()
    {
        return $this->repoName;
    }

    /**
     * Set projectSkills
     *
     * @param string $projectSkills
     * @return Project
     */
    public function setProjectSkills($projectSkills)
    {
        $this->projectSkills = $projectSkills;
    
        return $this;
    }

    /**
     * Get projectSkills
     *
     * @return string 
     */
    public function getProjectSkills()
    {
        return $this->projectSkills;
    }

    /**
     * Add configs
     *
     * @param \Acmtool\AppBundle\Entity\ProjectConfig $configs
     * @return Project
     */
    public function addConfig(\Acmtool\AppBundle\Entity\ProjectConfig $configs)
    {
        $this->configs[] = $configs;
    
        return $this;
    }

    /**
     * Remove configs
     *
     * @param \Acmtool\AppBundle\Entity\ProjectConfig $configs
     */
    public function removeConfig(\Acmtool\AppBundle\Entity\ProjectConfig $configs)
    {
        $this->configs->removeElement($configs);
    }

    /**
     * Get configs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getConfigs()
    {
        return $this->configs;
    }

    /**
     * Add documents
     *
     * @param \Acmtool\AppBundle\Entity\ProjectDocument $documents
     * @return Project
     */
    public function addDocument(\Acmtool\AppBundle\Entity\ProjectDocument $documents)
    {
        $this->documents[] = $documents;
    
        return $this;
    }

    /**
     * Remove documents
     *
     * @param \Acmtool\AppBundle\Entity\ProjectDocument $documents
     */
    public function removeDocument(\Acmtool\AppBundle\Entity\ProjectDocument $documents)
    {
        $this->documents->removeElement($documents);
    }

    /**
     * Get documents
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * Set signedContract
     *
     * @param boolean $signedContract
     * @return Project
     */
    public function setSignedContract($signedContract)
    {
        $this->signedContract = $signedContract;
    
        return $this;
    }

    /**
     * Get signedContract
     *
     * @return boolean 
     */
    public function getSignedContract()
    {
        return $this->signedContract;
    }

    /**
     * Set signaturedate
     *
     * @param \DateTime $signaturedate
     * @return Project
     */
    public function setSignaturedate($signaturedate)
    {
        $this->signaturedate = $signaturedate;
    
        return $this;
    }

    /**
     * Get signaturedate
     *
     * @return \DateTime 
     */
    public function getSignaturedate()
    {
        return $this->signaturedate;
    }

    /**
     * Set rate
     *
     * @param float $rate
     * @return Project
     */
    public function setRate($rate)
    {
        $this->rate = $rate;
    
        return $this;
    }

    /**
     * Get rate
     *
     * @return float 
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * Set hash
     *
     * @param string $hash
     * @return Project
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    
        return $this;
    }

    /**
     * Get hash
     *
     * @return string 
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set displayid
     *
     * @param string $displayid
     * @return Project
     */
    public function setDisplayid($displayid)
    {
        $this->displayid = $displayid;
    
        return $this;
    }

    /**
     * Get displayid
     *
     * @return string 
     */
    public function getDisplayid()
    {
        return $this->displayid;
    }

    /**
     * Set descriptionContract
     *
     * @param string $descriptionContract
     * @return Project
     */
    public function setDescriptionContract($descriptionContract)
    {
        $this->descriptionContract = $descriptionContract;

        return $this;
    }

    /**
     * Get descriptionContract
     *
     * @return string 
     */
    public function getDescriptionContract()
    {
        return $this->descriptionContract;
    }

    /**
     * Set contractPrepared
     *
     * @param boolean $contractPrepared
     * @return Project
     */
    public function setContractPrepared($contractPrepared)
    {
        $this->contractPrepared = $contractPrepared;

        return $this;
    }

    /**
     * Get contractPrepared
     *
     * @return boolean 
     */
    public function getContractPrepared()
    {
        return $this->contractPrepared;
    }

    /**
     * Set estimation
     *
     * @param float $estimation
     * @return Project
     */
    public function setEstimation($estimation)
    {
        $this->estimation = $estimation;

        return $this;
    }

    /**
     * Get estimation
     *
     * @return float 
     */
    public function getEstimation()
    {
        return $this->estimation;
    }

    /**
     * Set period
     *
     * @param float $period
     * @return Project
     */
    public function setPeriod($period)
    {
        $this->period = $period;

        return $this;
    }

    /**
     * Get period
     *
     * @return float 
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * Add logs
     *
     * @param \Acmtool\AppBundle\Entity\Log $logs
     * @return Project
     */
    public function addLog(\Acmtool\AppBundle\Entity\Log $logs)
    {
        $this->logs[] = $logs;

        return $this;
    }

    /**
     * Remove logs
     *
     * @param \Acmtool\AppBundle\Entity\Log $logs
     */
    public function removeLog(\Acmtool\AppBundle\Entity\Log $logs)
    {
        $this->logs->removeElement($logs);
    }

    /**
     * Get logs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLogs()
    {
        return $this->logs;
    }

    /**
     * Add noNotifs
     *
     * @param \Acmtool\AppBundle\Entity\NoNotif $noNotifs
     * @return Project
     */
    public function addNoNotif(\Acmtool\AppBundle\Entity\NoNotif $noNotifs)
    {
        $this->noNotifs[] = $noNotifs;

        return $this;
    }

    /**
     * Remove noNotifs
     *
     * @param \Acmtool\AppBundle\Entity\NoNotif $noNotifs
     */
    public function removeNoNotif(\Acmtool\AppBundle\Entity\NoNotif $noNotifs)
    {
        $this->noNotifs->removeElement($noNotifs);
    }

    /**
     * Get noNotifs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getNoNotifs()
    {
        return $this->noNotifs;
    }

    /**
     * Add pmtools
     *
     * @param \Acmtool\AppBundle\Entity\LinkedProject $pmtools
     * @return Project
     */
    public function addPmtool(\Acmtool\AppBundle\Entity\LinkedProject $pmtools)
    {
        $this->pmtools[] = $pmtools;

        return $this;
    }

    /**
     * Remove pmtools
     *
     * @param \Acmtool\AppBundle\Entity\LinkedProject $pmtools
     */
    public function removePmtool(\Acmtool\AppBundle\Entity\LinkedProject $pmtools)
    {
        $this->pmtools->removeElement($pmtools);
    }

    /**
     * Get pmtools
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPmtools()
    {
        return $this->pmtools;
    }

    /**
     * Add invoices
     *
     * @param \Acmtool\AppBundle\Entity\Invoice $invoices
     * @return Project
     */
    public function addInvoice(\Acmtool\AppBundle\Entity\Invoice $invoices)
    {
        $this->invoices[] = $invoices;

        return $this;
    }

    /**
     * Remove invoices
     *
     * @param \Acmtool\AppBundle\Entity\Invoice $invoices
     */
    public function removeInvoice(\Acmtool\AppBundle\Entity\Invoice $invoices)
    {
        $this->invoices->removeElement($invoices);
    }

    /**
     * Get invoices
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInvoices()
    {
        return $this->invoices;
    }
}
