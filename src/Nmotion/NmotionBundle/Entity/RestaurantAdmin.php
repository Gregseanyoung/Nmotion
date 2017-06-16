<?php
/**
 * @author tiger
 */

namespace Nmotion\NmotionBundle\Entity;

class RestaurantAdmin extends User
{
    /**
     * @var Restaurant
     */
    private $restaurant;

    public function __construct()
    {
        parent::__construct();

        $this->setRole(self::ROLE_RESTAURANT_ADMIN);
        $this->addRole(self::ROLE_RESTAURANT_GUEST);
        $this->addRole(self::ROLE_RESTAURANT_STAFF);
        $this->addRole(self::ROLE_RESTAURANT_ADMIN);
    }

    /**
     * Set restaurant
     *
     * @param Restaurant $restaurant
     * @return RestaurantAdmin
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
}
