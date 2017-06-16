<?php

namespace Nmotion\NmotionBundle\Entity;

use Symfony\Component\Validator\ExecutionContext;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * MenuCategory
 */
class MenuCategory
{
    use EntityAux;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var integer
     */
    private $timeFrom;

    /**
     * @var integer
     */
    private $timeTo;

    /**
     * @var integer
     */
    private $discountPercent = 0;

    /**
     * @var boolean
     */
    private $visible;

    /**
     * @var integer
     */
    private $position;

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
     * @var Collection|Meal[]
     */
    private $menuMeals;

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
     * Set name
     *
     * @param string $name
     *
     * @return MenuCategory
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set timeFrom
     *
     * @param integer $timeFrom
     *
     * @return MenuCategory
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
     *
     * @return MenuCategory
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
     * Set discountPercent
     *
     * @param int $discountPercent
     *
     * @return MenuCategory
     */
    public function setDiscountPercent($discountPercent)
    {
        $this->discountPercent = $discountPercent;

        return $this;
    }

    /**
     * Get discountPercent
     *
     * @return int
     */
    public function getDiscountPercent()
    {
        return $this->discountPercent;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     *
     * @return MenuCategory
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Get visible
     *
     * @return boolean
     */
    public function isVisible()
    {
        return $this->visible;
    }

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return MenuCategory
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set createdAt
     *
     * @param integer $createdAt
     *
     * @return MenuCategory
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
     * @return MenuCategory
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
     * @return MenuCategory
     */
    public function setRestaurant(Restaurant $restaurant = null)
    {
        $this->restaurant = $restaurant;

        if ($this->menuMeals) {
            foreach ($this->menuMeals as $meal) {
                $meal->setRestaurant($restaurant);
            }
        }

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
     * Set menu meals
     *
     * @param Collection|array|Meal[] $meals
     *
     * @return MenuCategory
     */
    public function setMenuMeals(array $meals = [])
    {
        foreach ($meals as $menuMeal) {
            $menuMeal->setRestaurant($this->getRestaurant());
            $menuMeal->setMenuCategory($this);
        }

        $this->menuMeals = $meals;

        return $this;
    }

    public function setMeals(array $menuMeals = [])
    {
        $this->setMenuMeals($menuMeals);
    }

    /**
     * Add meal
     *
     * @param Meal $menuMeal
     * @return MenuCategory
     */
    public function addMenuMeal(Meal $menuMeal)
    {
        $menuMeal->setRestaurant($this->getRestaurant());
        $menuMeal->setMenuCategory($this);

        $this->menuMeals[] = $menuMeal;

        return $this;
    }

    /**
     * Remove menu meal
     *
     * @param Meal $menuMeal
     */
    public function removeMenuMeal(Meal $menuMeal)
    {
        $this->menuMeals->removeElement($menuMeal);
    }

    /**
     * Get menu meals collection
     *
     * @return Collection|Meal[]
     */
    public function getMeals()
    {
        return $this->menuMeals;
    }

    /**
     * Get menu meals collection
     *
     * @return Collection|Meal[]
     */
    public function getMenuMeals()
    {
        return $this->menuMeals;
    }

    /**
     * Validate-method for operation time
     *
     * @param ExecutionContext $context
     *
     * @return void
     */
    public function isValidOperationTime(ExecutionContext $context)
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
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->menuMeals = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Get visible
     *
     * @return boolean 
     */
    public function getVisible()
    {
        return $this->visible;
    }
}