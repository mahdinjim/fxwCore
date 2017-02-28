<?php

namespace Acmtool\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LinkedPmTools
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class LinkedPmTools
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
     * @ORM\Column(name="toolname", type="string", length=255)
     */
    private $toolname;
      /**
    * 
    * @ORM\ManyToOne(targetEntity="Customer", inversedBy="pmtools")
    * @ORM\JoinColumn(name="client_id",referencedColumnName="id",onDelete="SET NULL")
    */
    private $client;

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
     * Set toolname
     *
     * @param string $toolname
     * @return LinkedPmTools
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
     * Set client
     *
     * @param \Acmtool\AppBundle\Entity\Customer $client
     * @return LinkedPmTools
     */
    public function setClient(\Acmtool\AppBundle\Entity\Customer $client = null)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \Acmtool\AppBundle\Entity\Customer 
     */
    public function getClient()
    {
        return $this->client;
    }
}
