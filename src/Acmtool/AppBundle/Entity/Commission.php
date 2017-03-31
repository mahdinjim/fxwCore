<?php

namespace Acmtool\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Commission
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Acmtool\AppBundle\Entity\CommissionRepository")
 */
class Commission
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
     * @ORM\Column(name="range1", type="float")
     */
    private $range1 = 0;
    /**
     * @var float
     *
     * @ORM\Column(name="range1Amount", type="float")
     */
    private $range1Amount = 0;
     /**
     * @var float
     *
     * @ORM\Column(name="range1Rate", type="float")
     */
    private $range1Rate = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="range2", type="float")
     */
    private $range2 = 0;
    /**
     * @var float
     *
     * @ORM\Column(name="range2Amount", type="float")
     */
    private $range2Ammount = 0;
     /**
     * @var float
     *
     * @ORM\Column(name="range2Rate", type="float")
     */
    private $range2Rate = 0;
    /**
     * @var float
     *
     * @ORM\Column(name="range3", type="float")
     */
    private $range3 = 0;
    /**
     * @var float
     *
     * @ORM\Column(name="range3Amount", type="float")
     */
    private $range3Ammount = 0;
     /**
     * @var float
     *
     * @ORM\Column(name="range3Rate", type="float")
     */
    private $range3Rate = 0;
    /**
     * @var float
     *
     * @ORM\Column(name="rangePM", type="float")
     */
    private $rangePM = 0;
    /**
     * @var string
     *
     * @ORM\Column(name="invoicefile", type="string", length=255, nullable=true)
     */
    private $invoicefile;
     /**
     * @var string
     *
     * @ORM\Column(name="invoicefileName", type="string", length=255, nullable=true)
     */
    private $invoicefileName;

    /**
     * @var boolean
     *
     * @ORM\Column(name="payed", type="boolean")
     */
    private $payed = false;
    /**
     * @var float
     *
     * @ORM\Column(name="managementAmount", type="float")
     */
    private $managementAmount = 0;
    /**
    * @ORM\ManyToOne(targetEntity="Invoice", inversedBy="commissions")
    * @ORM\JoinColumn(name="invoice_id",referencedColumnName="id",onDelete="SET NULL")
    */
    private $invoice;
    /**
    * @ORM\ManyToOne(targetEntity="Creds", inversedBy="commissions")
    * @ORM\JoinColumn(name="owner_id",referencedColumnName="id",onDelete="SET NULL")
    */
    private $owner;
     /**
     * @var datetime
     *
     * @ORM\Column(name="creationDate", type="datetime")
     */
    private $creationDate;

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
     * Set range1
     *
     * @param float $range1
     * @return Commission
     */
    public function setRange1($range1)
    {
        $this->range1 = $range1;

        return $this;
    }

    /**
     * Get range1
     *
     * @return float 
     */
    public function getRange1()
    {
        return $this->range1;
    }

    /**
     * Set range2
     *
     * @param float $range2
     * @return Commission
     */
    public function setRange2($range2)
    {
        $this->range2 = $range2;

        return $this;
    }

    /**
     * Get range2
     *
     * @return float 
     */
    public function getRange2()
    {
        return $this->range2;
    }

    /**
     * Set range3
     *
     * @param float $range3
     * @return Commission
     */
    public function setRange3($range3)
    {
        $this->range3 = $range3;

        return $this;
    }

    /**
     * Get range3
     *
     * @return float 
     */
    public function getRange3()
    {
        return $this->range3;
    }
    /**
     * Set invoicefile
     *
     * @param string $invoicefile
     * @return Commission
     */
    public function setInvoicefile($invoicefile)
    {
        $this->invoicefile = $invoicefile;

        return $this;
    }

    /**
     * Get invoicefile
     *
     * @return string 
     */
    public function getInvoicefile()
    {
        return $this->invoicefile;
    }

    /**
     * Set payed
     *
     * @param boolean $payed
     * @return Commission
     */
    public function setPayed($payed)
    {
        $this->payed = $payed;

        return $this;
    }

    /**
     * Get payed
     *
     * @return boolean 
     */
    public function getPayed()
    {
        return $this->payed;
    }

    /**
     * Set invoice
     *
     * @param \Acmtool\AppBundle\Entity\Invoice $invoice
     * @return Commission
     */
    public function setInvoice(\Acmtool\AppBundle\Entity\Invoice $invoice = null)
    {
        $this->invoice = $invoice;

        return $this;
    }

    /**
     * Get invoice
     *
     * @return \Acmtool\AppBundle\Entity\Invoice 
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * Set range1Amount
     *
     * @param float $range1Amount
     * @return Commission
     */
    public function setRange1Amount($range1Amount)
    {
        $this->range1Amount = $range1Amount;

        return $this;
    }

    /**
     * Get range1Amount
     *
     * @return float 
     */
    public function getRange1Amount()
    {
        return $this->range1Amount;
    }

    /**
     * Set range2Ammount
     *
     * @param float $range2Ammount
     * @return Commission
     */
    public function setRange2Ammount($range2Ammount)
    {
        $this->range2Ammount = $range2Ammount;

        return $this;
    }

    /**
     * Get range2Ammount
     *
     * @return float 
     */
    public function getRange2Ammount()
    {
        return $this->range2Ammount;
    }

    /**
     * Set range3Ammount
     *
     * @param float $range3Ammount
     * @return Commission
     */
    public function setRange3Ammount($range3Ammount)
    {
        $this->range3Ammount = $range3Ammount;

        return $this;
    }

    /**
     * Get range3Ammount
     *
     * @return float 
     */
    public function getRange3Ammount()
    {
        return $this->range3Ammount;
    }
    /**
     * Set range1Rate
     *
     * @param float $range1Rate
     * @return Commission
     */
    public function setRange1Rate($range1Rate)
    {
        $this->range1Rate = $range1Rate;

        return $this;
    }

    /**
     * Get range1Rate
     *
     * @return float 
     */
    public function getRange1Rate()
    {
        return $this->range1Rate;
    }

    /**
     * Set range2Rate
     *
     * @param float $range2Rate
     * @return Commission
     */
    public function setRange2Rate($range2Rate)
    {
        $this->range2Rate = $range2Rate;

        return $this;
    }

    /**
     * Get range2Rate
     *
     * @return float 
     */
    public function getRange2Rate()
    {
        return $this->range2Rate;
    }

    /**
     * Set range3Rate
     *
     * @param float $range3Rate
     * @return Commission
     */
    public function setRange3Rate($range3Rate)
    {
        $this->range3Rate = $range3Rate;

        return $this;
    }

    /**
     * Get range3Rate
     *
     * @return float 
     */
    public function getRange3Rate()
    {
        return $this->range3Rate;
    }

    /**
     * Set managementAmount
     *
     * @param float $managementAmount
     * @return Commission
     */
    public function setManagementAmount($managementAmount)
    {
        $this->managementAmount = $managementAmount;

        return $this;
    }

    /**
     * Get managementAmount
     *
     * @return float 
     */
    public function getManagementAmount()
    {
        return $this->managementAmount;
    }

    /**
     * Set creationDate
     *
     * @param \DateTime $creationDate
     * @return Commission
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * Get creationDate
     *
     * @return \DateTime 
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * Set owner
     *
     * @param \Acmtool\AppBundle\Entity\Creds $owner
     * @return Commission
     */
    public function setOwner(\Acmtool\AppBundle\Entity\Creds $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \Acmtool\AppBundle\Entity\Creds 
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set invoicefileName
     *
     * @param string $invoicefileName
     * @return Commission
     */
    public function setInvoicefileName($invoicefileName)
    {
        $this->invoicefileName = $invoicefileName;

        return $this;
    }

    /**
     * Get invoicefileName
     *
     * @return string 
     */
    public function getInvoicefileName()
    {
        return $this->invoicefileName;
    }

    /**
     * Set rangePM
     *
     * @param float $rangePM
     * @return Commission
     */
    public function setRangePM($rangePM)
    {
        $this->rangePM = $rangePM;

        return $this;
    }

    /**
     * Get rangePM
     *
     * @return float 
     */
    public function getRangePM()
    {
        return $this->rangePM;
    }
}
