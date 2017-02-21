<?php

namespace Acmtool\JBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Acoount
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Acmtool\JBundle\Entity\AcoountRepository")
 */
class Account
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
     * @ORM\Column(name="t1", type="string", length=1000)
     */
    private $t1;

    /**
     * @var string
     *
     * @ORM\Column(name="t2", type="string", length=1000)
     */
    private $t2;

    /**
     * @var string
     *
     * @ORM\Column(name="link", type="string", length=255)
     */
    private $link;

    /**
     * @var integer
     *
     * @ORM\Column(name="linker", type="integer")
     */
    private $linker;


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
     * Set t1
     *
     * @param string $t1
     * @return Acoount
     */
    public function setT1($t1)
    {
        $this->t1 = $t1;

        return $this;
    }

    /**
     * Get t1
     *
     * @return string 
     */
    public function getT1()
    {
        return $this->t1;
    }

    /**
     * Set t2
     *
     * @param string $t2
     * @return Acoount
     */
    public function setT2($t2)
    {
        $this->t2 = $t2;

        return $this;
    }

    /**
     * Get t2
     *
     * @return string 
     */
    public function getT2()
    {
        return $this->t2;
    }

    /**
     * Set link
     *
     * @param string $link
     * @return Acoount
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return string 
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set linker
     *
     * @param integer $linker
     * @return Acoount
     */
    public function setLinker($linker)
    {
        $this->linker = $linker;

        return $this;
    }

    /**
     * Get linker
     *
     * @return integer 
     */
    public function getLinker()
    {
        return $this->linker;
    }
}
