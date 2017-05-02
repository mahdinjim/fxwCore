<?php

namespace Acmtool\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DeviceToken
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class DeviceToken
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
     * @ORM\Column(name="token", type="string", length=255)
     */
    private $token;

    /**
     * @var string
     *
     * @ORM\Column(name="deviceid", type="string", length=1000,nullable=true)
     */
    private $deviceid;
    /**
     * @var string
     *
     * @ORM\Column(name="devicename", type="string", length=255,nullable=true)
     */
    private $devicename;

    /**
     * @var string
     *
     * @ORM\Column(name="os", type="string", length=255)
     */
    private $os;
    /**
    * @ORM\ManyToOne(targetEntity="Creds", inversedBy="devicetokens")
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
     * Set token
     *
     * @param string $token
     * @return DeviceToken
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string 
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set deviceid
     *
     * @param string $deviceid
     * @return DeviceToken
     */
    public function setDeviceid($deviceid)
    {
        $this->deviceid = $deviceid;

        return $this;
    }

    /**
     * Get deviceid
     *
     * @return string 
     */
    public function getDeviceid()
    {
        return $this->deviceid;
    }

    /**
     * Set os
     *
     * @param string $os
     * @return DeviceToken
     */
    public function setOs($os)
    {
        $this->os = $os;

        return $this;
    }

    /**
     * Get os
     *
     * @return string 
     */
    public function getOs()
    {
        return $this->os;
    }

    /**
     * Set user
     *
     * @param \Acmtool\AppBundle\Entity\Creds $user
     * @return DeviceToken
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

    /**
     * Set devicename
     *
     * @param string $devicename
     * @return DeviceToken
     */
    public function setDevicename($devicename)
    {
        $this->devicename = $devicename;

        return $this;
    }

    /**
     * Get devicename
     *
     * @return string 
     */
    public function getDevicename()
    {
        return $this->devicename;
    }
}
