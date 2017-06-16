<?php

namespace Nmotion\NmotionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RestaurantCheckin
 */
class RestaurantCheckin
{
    use EntityAux;

    const TABLE_MAYBE_EMPTY = 1002;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var RestaurantServiceType
     */
    private $serviceType;

    /**
     * @var string
     */
    private $tableNumber;
    
    /**
     * @var integer
     */
    private $takeawayPickupTime = 0;

    /**
     * @var integer
     */
    private $createdAt;

    /**
     * @var integer
     */
    private $updatedAt;

    /**
     * @var Restaurant
     */
    private $restaurant;

    /**
     * @var User
     */
    private $user;

    /**
     * @var boolean
     */
    private $checkedOut = false;


    public function __construct(Restaurant $restaurant = null, User $user = null)
    {
        if ($restaurant) {
            $this->setRestaurant($restaurant);
        }
        if ($user) {
            $this->setUser($user);
        }
    }

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
     * Set serviceType
     *
     * @param RestaurantServiceType $serviceType
     *
     * @return RestaurantCheckin
     */
    public function setServiceType($serviceType)
    {
        $this->serviceType = $serviceType;

        return $this;
    }

    /**
     * Get serviceType
     *
     * @return RestaurantServiceType
     */
    public function getServiceType()
    {
        return $this->serviceType;
    }

    /**
     * @param string $tableNumber
     *
     * @return $this
     */
    public function setTableNumber($tableNumber)
    {
        $this->tableNumber = $tableNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getTableNumber()
    {
        return $this->tableNumber;
    }

    /**
     * Set checked out
     *
     * @param boolean $checkedOut
     *
     * @return RestaurantCheckin
     */
    public function setCheckedOut($checkedOut)
    {
        $this->checkedOut = $checkedOut;

        return $this;
    }

    /**
     * Get checkedOut
     *
     * @return boolean
     */
    public function isCheckedOut()
    {
        return (boolean) $this->checkedOut;
    }

    /**
     * Set createdAt
     *
     * @param integer $createdAt
     *
     * @return RestaurantCheckin
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return integer
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param integer $updatedAt
     *
     * @return RestaurantCheckin
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return integer
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set restaurant
     *
     * @param Restaurant $restaurant
     *
     * @return RestaurantCheckin
     */
    public function setRestaurant(Restaurant $restaurant = null)
    {
        $this->restaurant = $restaurant;

        return $this;
    }

    /**
     * Get restaurant
     *
     * @return Restaurant
     */
    public function getRestaurant()
    {
        return $this->restaurant;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return RestaurantCheckin
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


    /**
     * Set takeaway pickup time
     *
     * @param integer $takeawayPickupTime
     * @return RestaurantCheckin
     */
    public function setTakeawayPickupTime($takeawayPickupTime)
    {
    	$this->takeawayPickupTime = $takeawayPickupTime;
    
    	return $this;
    }
    
    /**
     * Get takeaway pickup time
     *
     * @return integer
     */
    public function getTakeawayPickupTime()
    {
    	return $this->takeawayPickupTime;
    }
    
    
    /**
     * Is checkin expired
     *
     * @param int $time
     *
     * @return boolean
     */
    public function isExpired($time = null)
    {
        $time = $time ? : time();
        $checkOutTime = $this->getRestaurant()->getCheckOutTimeInSeconds();

        return $this->isCheckedOut()
            || !($time >= $this->getUpdatedAt() && $time <= ($this->getUpdatedAt() + $checkOutTime));
    }
}
