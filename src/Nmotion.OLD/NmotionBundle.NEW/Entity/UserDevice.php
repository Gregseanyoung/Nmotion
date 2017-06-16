<?php

namespace Nmotion\NmotionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserDevice
 */
class UserDevice
{
    use EntityAux;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $deviceIdentity;

    /**
     * @var string
     */
    private $salt;

    /**
     * @var User
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
     * Set deviceIdentity
     *
     * @param string $deviceIdentity
     * @return UserDevice
     */
    public function setDeviceIdentity($deviceIdentity)
    {
        $this->deviceIdentity = $deviceIdentity;
    
        return $this;
    }

    /**
     * Get deviceIdentity
     *
     * @return string 
     */
    public function getDeviceIdentity()
    {
        return $this->deviceIdentity;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return UserDevice
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    
        return $this;
    }

    /**
     * Get salt
     *
     * @return string 
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set user
     *
     * @param User $user
     * @return UserDevice
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
}