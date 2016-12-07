<?php

namespace Acmtool\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NoNotif
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Acmtool\AppBundle\Entity\NoNotifRepository")
 */
class NoNotif
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
    * @ORM\ManyToOne(targetEntity="Project", inversedBy="noNotifs")
    * @ORM\JoinColumn(name="p_id",referencedColumnName="id",onDelete="SET NULL")
    */
    private $project;
    /**
    * @ORM\ManyToOne(targetEntity="Creds", inversedBy="noNotifs")
    * @ORM\JoinColumn(name="u_id",referencedColumnName="id",onDelete="SET NULL")
    */
    private $user;


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
     * Set project
     *
     * @param \Acmtool\AppBundle\Entity\Project $project
     * @return NoNotif
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

    /**
     * Set user
     *
     * @param \Acmtool\AppBundle\Entity\Creds $user
     * @return NoNotif
     */
    public function setUser(\Acmtool\AppBundle\Entity\Creds $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Acmtool\AppBundle\Entity\Creds 
     */
    public function getUser()
    {
        return $this->user;
    }
}
