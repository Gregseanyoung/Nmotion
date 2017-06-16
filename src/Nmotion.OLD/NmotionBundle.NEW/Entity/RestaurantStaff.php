<?php
/**
 * @author tiger
 */

namespace Nmotion\NmotionBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class RestaurantStaff extends User
{
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $restaurant;


    public function __construct()
    {
        parent::__construct();

        $this->setRole(self::ROLE_RESTAURANT_STAFF);
        $this->addRole(self::ROLE_RESTAURANT_GUEST);
        $this->addRole(self::ROLE_RESTAURANT_STAFF);

        $this->restaurant = new ArrayCollection();
    }

    /**
     * Add restaurant
     *
     * @param Restaurant $restaurant
     * @return RestaurantStaff
     */
    public function addRestaurant(Restaurant $restaurant)
    {
        $this->restaurant->clear();
        $this->restaurant->add($restaurant);

        return $this;
    }

    /**
     * Set restaurant
     *
     * @param Restaurant $restaurant
     * @return $this
     */
    public function setRestaurant(Restaurant $restaurant)
    {
        $this->restaurant->clear();
        $this->restaurant->add($restaurant);

        return $this;
    }

    /**
     * Remove restaurant
     *
     * @param Restaurant $restaurant
     */
    public function removeRestaurant(Restaurant $restaurant)
    {
        $this->restaurant->clear();
    }

    /**
     * Get restaurant
     *
     * @return Restaurant
     */
    public function getRestaurant()
    {
        return $this->restaurant->first();
    }
}