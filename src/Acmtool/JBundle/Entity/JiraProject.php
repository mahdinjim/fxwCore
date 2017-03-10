<?php

namespace Acmtool\JBundle\Entity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * JiraProject
 * @UniqueEntity(fields={"linkerId"},message="This flexwork project is already linked")
 * @UniqueEntity(fields={"projectId"},message="This jira project is already linked")
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Acmtool\JBundle\Entity\ProjectRepository")
 */
class JiraProject
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
     * @ORM\Column(name="projectName", type="string", length=255)
     */
    private $projectName;

    /**
     * @var integer
     *
     * @ORM\Column(name="project_id", type="integer")
     */
    private $projectId;

    /**
     * @var integer
     *
     * @ORM\Column(name="linker_id", type="integer")
     */
    private $linkerId;


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
     * Set jira projectName
     *
     * @param string $projectName
     * @return Project
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
     * Set projectId
     *
     * @param integer $projectId
     * @return Project
     */
    public function setProjectId($projectId)
    {
        $this->projectId = $projectId;

        return $this;
    }

    /**
     * Get jira projectId
     *
     * @return integer 
     */
    public function getProjectId()
    {
        return $this->projectId;
    }

    /**
     * Set flexwork project id linkerId
     *
     * @param integer $linkerId
     * @return Project
     */
    public function setLinkerId($linkerId)
    {
        $this->linkerId = $linkerId;

        return $this;
    }

    /**
     * Get linkerId
     *
     * @return integer 
     */
    public function getLinkerId()
    {
        return $this->linkerId;
    }
}
