<?php

namespace Acmtool\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LinkedProject
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class LinkedProject
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
     * @ORM\Column(name="project_name", type="string", length=255)
     */
    private $projectName;

    /**
     * @var string
     *
     * @ORM\Column(name="toolname", type="string", length=255)
     */
    private $toolname;
     /**
    * 
    * @ORM\ManyToOne(targetEntity="Project", inversedBy="pmtools")
    * @ORM\JoinColumn(name="project_id",referencedColumnName="id",onDelete="SET NULL")
    */
    private $project;

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
     * Set projectName
     *
     * @param string $projectName
     * @return LinkedProject
     */
    public function setProjectName($projectName)
    {
        $this->projectName = $projectName;

        return $this;
    }

    /**
     * Get projectName
     *
     * @return string 
     */
    public function getProjectName()
    {
        return $this->projectName;
    }

    /**
     * Set toolname
     *
     * @param string $toolname
     * @return LinkedProject
     */
    public function setToolname($toolname)
    {
        $this->toolname = $toolname;

        return $this;
    }

    /**
     * Get toolname
     *
     * @return string 
     */
    public function getToolname()
    {
        return $this->toolname;
    }

    /**
     * Set project
     *
     * @param \Acmtool\AppBundle\Entity\Project $project
     * @return LinkedProject
     */
    public function setProject(\Acmtool\AppBundle\Entity\Project $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return \Acmtool\AppBundle\Entity\Project 
     */
    public function getProject()
    {
        return $this->project;
    }
}
