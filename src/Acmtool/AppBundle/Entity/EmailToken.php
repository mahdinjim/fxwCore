<?php

namespace Acmtool\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmailToken
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Acmtool\AppBundle\Entity\EmailTokenRepository")
 */
class EmailToken
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
     * @ORM\Column(name="tokendig", type="string", length=255)
     */
    private $tokendig;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="expirationdate", type="datetime")
     */
    private $expirationdate;

    /**
    * @ORM\ManyToOne(targetEntity="Creds", inversedBy="emailtokens")
    * @ORM\JoinColumn(name="user_id",referencedColumnName="id",onDelete="SET NULL")
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
     * Set $tokendig
     *
     * @param string $$tokendig
     * @return EmailToken
     */
    public function setTokendig($tokendig)
    {
        $this->tokendig = $tokendig;

        return $this;
    }

    /**
     * Get $tokendig
     *
     * @return string 
     */
    public function getTokendig()
    {
        return $this->tokendig;
    }

    /**
     * Set expirationdate
     *
     * @param \DateTime $expirationdate
     * @return EmailToken
     */
    public function setExpirationdate($expirationdate)
    {
        $this->expirationdate = $expirationdate;

        return $this;
    }

    /**
     * Get expirationdate
     *
     * @return \DateTime 
     */
    public function getExpirationdate()
    {
        return $this->expirationdate;
    }

    /**
     * Gets the value of user.
     *
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Sets the value of user.
     *
     * @param mixed $user the user
     *
     * @return self
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }
}
