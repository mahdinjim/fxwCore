<?php

namespace Acmtool\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CumulativeHours
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class CumulativeHours
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
     * @var float
     *
     * @ORM\Column(name="total_hours", type="float")
     */
    private $totalHours;
    /**
    * @ORM\ManyToOne(targetEntity="Customer")
    * @ORM\JoinColumn(name="client_id",referencedColumnName="id",onDelete="SET NULL")
    */
    private $customer;
    /**
    * @ORM\ManyToOne(targetEntity="Creds")
    * @ORM\JoinColumn(name="referer_id",referencedColumnName="id",onDelete="SET NULL")
    */
    private $referer;

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
     * Set totalHours
     *
     * @param float $totalHours
     * @return CumulativeHours
     */
    public function setTotalHours($totalHours)
    {
        $this->totalHours = $totalHours;

        return $this;
    }

    /**
     * Get totalHours
     *
     * @return float 
     */
    public function getTotalHours()
    {
        return $this->totalHours;
    }

    /**
     * Set customer
     *
     * @param \Acmtool\AppBundle\Entity\Customer $customer
     * @return CumulativeHours
     */
    public function setCustomer(\Acmtool\AppBundle\Entity\Customer $customer = null)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Get customer
     *
     * @return \Acmtool\AppBundle\Entity\Customer 
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Set referer
     *
     * @param \Acmtool\AppBundle\Entity\Creds $referer
     * @return CumulativeHours
     */
    public function setReferer(\Acmtool\AppBundle\Entity\Creds $referer = null)
    {
        $this->referer = $referer;

        return $this;
    }

    /**
     * Get referer
     *
     * @return \Acmtool\AppBundle\Entity\Creds 
     */
    public function getReferer()
    {
        return $this->referer;
    }
}
