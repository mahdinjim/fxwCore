<?php

namespace Acmtool\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Creds
 * @UniqueEntity(fields={"login"},message="This login value is already used")
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Acmtool\AppBundle\Entity\CredsRepository")
 */
class Creds implements \Serializable
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
     * @Assert\NotBlank(message="This login can't be blank")
     * @ORM\Column(name="login", type="string", length=255)
     */
    private $login;

    /**
     * @var string
     * @Assert\NotBlank
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;
    /**
     * @var string
     * @Assert\NotBlank
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;
     /**
    * @ORM\OneToMany(targetEntity="Project", mappedBy="teamleader")
    */
    private $projects;
    /**
    * @ORM\OneToMany(targetEntity="Task", mappedBy="owner")
    */
    private $tasks; 
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
     * Set login
     *
     * @param string $login
     * @return Creds
     */
    public function setLogin($login)
    {
        $this->login = $login;
    
        return $this;
    }

    /**
     * Get login
     *
     * @return string 
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Creds
     */
    public function setPassword($password)
    {
        $this->password = $password;
    
        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }
      /**
     * Set Title
     *
     * @param string $tile
     * @return Creds
     */
    public function setTitle($title)
    {
        $this->title= $title;
    
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
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
        ) = unserialize($serialized);
    }
     /**
     * Add projects
     *
     * @param \Acmtool\AppBundle\Entity\Project $projects
     * @return TeamLeader
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
     /**
     * Add tasks
     *
     * @param \Acmtool\AppBundle\Entity\Task $tasks
     * @return TeamLeader
     */
    public function addTask(\Acmtool\AppBundle\Entity\Task $tasks)
    {
        $this->tasks[] = $tasks;
    
        return $this;
    }

    /**
     * Remove tasks
     *
     * @param \Acmtool\AppBundle\Entity\Task $tasks
     */
    public function removeTask(\Acmtool\AppBundle\Entity\Task $tasks)
    {
        $this->tasks->removeElement($tasks);
    }

    /**
     * Get tasks
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTasks()
    {
        return $this->tasks;
    }
}