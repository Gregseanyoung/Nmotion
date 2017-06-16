<?php

namespace Nmotion\NmotionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrderMealExtraIngredient
 */
class OrderMealExtraIngredient
{
    use EntityAux;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var OrderMeal
     */
    private $orderMeal;

    /**
     * @var MealExtraIngredient
     */
    private $mealExtraIngredient;

    /**
     * @var string
     */
    private $name;

    /**
     * @var float
     */
    private $price;

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
     * Set orderMeal
     *
     * @param OrderMeal $orderMeal
     * @return OrderMealExtraIngredient
     */
    public function setOrderMeal(OrderMeal $orderMeal = null)
    {
        $this->orderMeal = $orderMeal;

        return $this;
    }

    /**
     * Get orderMeal
     *
     * @return OrderMeal
     */
    public function getOrderMeal()
    {
        return $this->orderMeal;
    }

    /**
     * Set mealExtraIngredient
     *
     * @param MealExtraIngredient $mealExtraIngredient
     * @return OrderMealExtraIngredient
     */
    public function setMealExtraIngredient(MealExtraIngredient $mealExtraIngredient = null)
    {
        $this->mealExtraIngredient = $mealExtraIngredient;

        if (!empty($mealExtraIngredient)) {
            $this->name = $this->name ? : $mealExtraIngredient->getName();
            $this->price = $this->price ? : $mealExtraIngredient->getPrice();
        }

        return $this;
    }

    /**
     * Get mealExtraIngredient
     *
     * @return MealExtraIngredient
     */
    public function getMealExtraIngredient()
    {
        return $this->mealExtraIngredient;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return OrderMealExtraIngredient
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
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set price
     *
     * @param float $price
     * @return OrderMealExtraIngredient
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return float
     */
    public function getDiscountPrice()
    {
        $discount = $this->getPrice() * $this->getOrderMeal()->getMealDiscountPercent() / 100;
        return $this->getPrice() - $discount;
    }

    /**
     * @return float
     */
    public function getDiscountPriceIncludingTax()
    {
        return round(
            $this->getDiscountPrice()
            + $this->getDiscountPrice() * $this->getOrderMeal()->getMeal()->getSalesTaxPercent() / 100,
            2
        );
    }

    /**
     * Get price including tax
     *
     * @return float
     */
    public function getPriceIncludingTax()
    {
        return round(
            $this->getPrice() + $this->getPrice() * $this->getOrderMeal()->getMeal()->getSalesTaxPercent() / 100,
            2
        );
    }
}