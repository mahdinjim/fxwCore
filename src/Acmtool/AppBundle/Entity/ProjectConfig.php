<?php

namespace Acmtool\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ProjectConfig
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Acmtool\AppBundle\Entity\ProjectConfigRepository")
 */
class ProjectConfig
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
     * @ORM\Column(name="config", type="text")
     */
    private $config;

    /**
    * @ORM\OneToMany(targetEntity="Project", mappedBy="config")
    */
    private $projects;

    public function __construct()
    {
        $this->projects=new ArrayCollection();
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
     * Set config
     *
     * @param string $config
     * @return ProjectConfig
     */
    public function setConfig($config)
    {
        $this->config = $config;
    
        return $this;
    }

    /**
     * Get config
     *
     * @return string 
     */
    public function getConfig()
    {
        return $this->config;
    }


    /**
     * Add projects
     *
     * @param \Acmtool\AppBundle\Entity\Project $projects
     * @return ProjectConfig
     */
    public function addProject(\Acmtool\AppBundle\Entity\Project $projects)
    {
        $this->projects[] = $projects;
    
        return $this;
    }

    /**
     * Remove projects
     *
     * @param \Acmtool\AppBundle\Entity\Project $projects
     */
    public function removeProject(\Acmtool\AppBundle\Entity\Project $projects)
    {
        $this->projects->removeElement($projects);
    }

    /**
     * Get projects
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProjects()
    {
        return $this->projects;
    }
}