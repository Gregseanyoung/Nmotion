<?php

namespace Nmotion\NmotionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\ExecutionContext;

/**
 * RestaurantOperationTime
 */
class RestaurantOperationTime
{
    use EntityAux;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var Restaurant
     */
    private $restaurant;

    /**
     * @var integer
     */
    private $dayOfTheWeek;

    /**
     * @var integer
     */
    private $timeFrom;

    /**
     * @var integer
     */
    private $timeTo;


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
     * Set restaurant
     *
     * @param Restaurant $restaurant
     * @return RestaurantOperationTime
     */
    public function setRestaurant(Restaurant $restaurant)
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
     * Set dayOfTheWeek
     *
     * @param integer $dayOfTheWeek
     * @return RestaurantOperationTime
     */
    public function setDayOfTheWeek($dayOfTheWeek)
    {
        $this->dayOfTheWeek = $dayOfTheWeek;

        return $this;
    }

    /**
     * Get dayOfTheWeek
     *
     * @return integer
     */
    public function getDayOfTheWeek()
    {
        return $this->dayOfTheWeek;
    }

    /**
     * Set timeFrom
     *
     * @param integer $timeFrom
     * @return RestaurantOperationTime
     */
    public function setTimeFrom($timeFrom)
    {
        $this->timeFrom = $timeFrom;

        return $this;
    }

    /**
     * Get timeFrom
     *
     * @return integer
     */
    public function getTimeFrom()
    {
        return $this->timeFrom;
    }

    /**
     * Set timeTo
     *
     * @param integer $timeTo
     * @return RestaurantOperationTime
     */
    public function setTimeTo($timeTo)
    {
        $this->timeTo = $timeTo;

        return $this;
    }

    /**
     * Get timeTo
     *
     * @return integer
     */
    public function getTimeTo()
    {
        return $this->timeTo;
    }

    /**
     * Validate-method for operation time
     *
     * @param ExecutionContext $context
     *
     * @return void
     */
    public function isValid(ExecutionContext $context)
    {
        if ($this->timeFrom === null && $this->timeTo === null
            || ($this->timeFrom !== null && $this->timeTo !== null)
        ) {
            return;
        }

        $context->addViolationAt(
            'timeFrom',
            'restaurantOperationTime.invalidUseOfEmptyTime',
            []
        );
    }
}
