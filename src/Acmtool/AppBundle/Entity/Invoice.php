<?php

namespace Acmtool\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Invoice
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Invoice
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
     * @ORM\Column(name="displayid", type="string", length=255)
     */
    private $displayid;
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=1000)
     */
    private $discription;

    /**
     * @var datetime
     *
     * @ORM\Column(name="creationDate", type="datetime")
     */
    private $creationDate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="paied", type="boolean")
     */
    private $paied = false;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float")
     */
    private $amount;
    /**
     * @var float
     *
     * @ORM\Column(name="discount", type="float",nullable=true)
     */
    private $discount;
    /**
     * @var float
     *
     * @ORM\Column(name="bt", type="float")
     */
    private $bt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="paidDate", type="datetime",nullable=true)
     */
    private $paidDate;
     /**
     * @var \DateTime
     *
     * @ORM\Column(name="endDate", type="datetime")
     */
    private $endDate;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="limitDate", type="datetime")
     */
    private $limitDate;
    /**
     * @var float
     *
     * @ORM\Column(name="up", type="float")
     */
    private $up;
    /**
     * @var integer
     *
     * @ORM\Column(name="year", type="integer")
     */
    private $year;
    /**
    * @ORM\ManyToOne(targetEntity="Project", inversedBy="invoices")
    * @ORM\JoinColumn(name="project_id",referencedColumnName="id",onDelete="SET NULL")
    */
    private $project;
    /**
    * @ORM\ManyToOne(targetEntity="Customer", inversedBy="invoices")
    * @ORM\JoinColumn(name="client_id",referencedColumnName="id",onDelete="SET NULL")
    */
    private $client;
    /**
    * @ORM\OneToMany(targetEntity="Ticket", mappedBy="invoice")
    */
    private $tickets;
    /**
     * @var string
     *
     * @ORM\Column(name="billedFrom", type="string", length=20)
     */
    private $billedFrom;
    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="string", length=20)
     */
    private $currency;
    /**
    * @ORM\OneToMany(targetEntity="Commission", mappedBy="invoice")
    */
    private $commissions;
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
     * Set displayid
     *
     * @param string $displayid
     * @return Invoice
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
     * Set creationDate
     *
     * @param \DateTime $creationDate
     * @return Invoice
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * Get creationDate
     *
     * @return string 
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * Set paied
     *
     * @param boolean $paied
     * @return Invoice
     */
    public function setPaied($paied)
    {
        $this->paied = $paied;

        return $this;
    }

    /**
     * Get paied
     *
     * @return boolean 
     */
    public function getPaied()
    {
        return $this->paied;
    }

    /**
     * Set amount
     *
     * @param float $amount
     * @return Invoice
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float 
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set bt
     *
     * @param float $bt
     * @return Invoice
     */
    public function setBt($bt)
    {
        $this->bt = $bt;

        return $this;
    }

    /**
     * Get bt
     *
     * @return float 
     */
    public function getBt()
    {
        return $this->bt;
    }

    /**
     * Set paidDate
     *
     * @param \DateTime $paidDate
     * @return Invoice
     */
    public function setPaidDate($paidDate)
    {
        $this->paidDate = $paidDate;

        return $this;
    }

    /**
     * Get paidDate
     *
     * @return \DateTime 
     */
    public function getPaidDate()
    {
        return $this->paidDate;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tickets = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set discription
     *
     * @param string $discription
     * @return Invoice
     */
    public function setDiscription($discription)
    {
        $this->discription = $discription;

        return $this;
    }

    /**
     * Get discription
     *
     * @return string 
     */
    public function getDiscription()
    {
        return $this->discription;
    }

    /**
     * Set discount
     *
     * @param float $discount
     * @return Invoice
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * Get discount
     *
     * @return float 
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     * @return Invoice
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime 
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set limitDate
     *
     * @param \DateTime $limitDate
     * @return Invoice
     */
    public function setLimitDate($limitDate)
    {
        $this->limitDate = $limitDate;

        return $this;
    }

    /**
     * Get limitDate
     *
     * @return \DateTime 
     */
    public function getLimitDate()
    {
        return $this->limitDate;
    }

    /**
     * Set up
     *
     * @param float $up
     * @return Invoice
     */
    public function setUp($up)
    {
        $this->up = $up;

        return $this;
    }

    /**
     * Get up
     *
     * @return float 
     */
    public function getUp()
    {
        return $this->up;
    }

    /**
     * Set year
     *
     * @param integer $year
     * @return Invoice
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return integer 
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set project
     *
     * @param \Acmtool\AppBundle\Entity\Project $project
     * @return Invoice
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
     * Set client
     *
     * @param \Acmtool\AppBundle\Entity\Customer $client
     * @return Invoice
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

    /**
     * Add tickets
     *
     * @param \Acmtool\AppBundle\Entity\Ticket $tickets
     * @return Invoice
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
        return $this->tickets;
    }

    /**
     * Set billedFrom
     *
     * @param string $billedFrom
     * @return Invoice
     */
    public function setBilledFrom($billedFrom)
    {
        $this->billedFrom = $billedFrom;

        return $this;
    }

    /**
     * Get billedFrom
     *
     * @return string 
     */
    public function getBilledFrom()
    {
        return $this->billedFrom;
    }

    /**
     * Set currency
     *
     * @param string $currency
     * @return Invoice
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return string 
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Add commissions
     *
     * @param \Acmtool\AppBundle\Entity\Commission $commissions
     * @return Invoice
     */
    public function addCommission(\Acmtool\AppBundle\Entity\Commission $commissions)
    {
        $this->commissions[] = $commissions;

        return $this;
    }

    /**
     * Remove commissions
     *
     * @param \Acmtool\AppBundle\Entity\Commission $commissions
     */
    public function removeCommission(\Acmtool\AppBundle\Entity\Commission $commissions)
    {
        $this->commissions->removeElement($commissions);
    }

    /**
     * Get commissions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCommissions()
    {
        return $this->commissions;
    }
}
