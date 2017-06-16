<?php

namespace Nmotion\NmotionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\ExecutionContext;

/**
 * OrderMeal
 */
class OrderMeal
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
     * @var string
     */
    private $description;

    /**
     * @var float
     */
    private $price;

    /**
     * @var integer
     */
    private $discountPercent;

    /**
     * @var string
     */
    private $mealComment;

    /**
     * @var integer
     */
    private $quantity;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $orderMealExtraIngredients;

    /**
     * @var Order
     */
    private $order;

    /**
     * @var Meal
     */
    private $meal;

    /**
     * @var MealOption
     */
    private $mealOption;

    /**
     * @var string
     */
    private $mealOptionName;

    /**
     * @var float
     */
    private $mealOptionPrice;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->orderMealExtraIngredients = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return OrderMeal
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
     * Set description
     *
     * @param string $description
     *
     * @return OrderMeal
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set price
     *
     * @param float $price
     *
     * @return OrderMeal
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
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
     * Set discountPercent
     *
     * @param int $discountPercent
     *
     * @return OrderMeal
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
     * Set mealComment
     *
     * @param string $mealComment
     * @return OrderMeal
     */
    public function setMealComment($mealComment)
    {
        $this->mealComment = $mealComment;

        return $this;
    }

    /**
     * Get mealComment
     *
     * @return string
     */
    public function getMealComment()
    {
        return $this->mealComment;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     * @return OrderMeal
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Add orderMealExtraIngredients
     *
     * @param OrderMealExtraIngredient $orderMealExtraIngredients
     * @return OrderMeal
     */
    public function addOrderMealExtraIngredient(OrderMealExtraIngredient $orderMealExtraIngredients)
    {
        $orderMealExtraIngredients->setOrderMeal($this);

        $this->orderMealExtraIngredients[] = $orderMealExtraIngredients;

        return $this;
    }

    /**
     * Remove orderMealExtraIngredients
     *
     * @param OrderMealExtraIngredient $orderMealExtraIngredients
     */
    public function removeOrderMealExtraIngredient(OrderMealExtraIngredient $orderMealExtraIngredients)
    {
        $this->orderMealExtraIngredients->removeElement($orderMealExtraIngredients);
    }

    /**
     * Get orderMealExtraIngredients
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrderMealExtraIngredients()
    {
        return $this->orderMealExtraIngredients;
    }

    /**
     * Set order
     *
     * @param Order $order
     * @return OrderMeal
     */
    public function setOrder(Order $order = null)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set meal
     *
     * @param Meal $meal
     * @return OrderMeal
     */
    public function setMeal(Meal $meal = null)
    {
        $this->meal = $meal;

        if (!empty($meal)) {
            $this->name                = $this->name                ? : $meal->getName();
            $this->price               = $this->price               ? : $meal->getPrice();
            $this->description         = $this->description         ? : $meal->getDescription();
            $this->discountPercent     = $this->discountPercent     ? : $meal->getDiscountPercent();
        }

        return $this;
    }

    /**
     * Get meal
     *
     * @return Meal
     */
    public function getMeal()
    {
        return $this->meal;
    }

    /**
     * Set mealOption
     *
     * @param MealOption $mealOption
     * @return OrderMeal
     */
    public function setMealOption(MealOption $mealOption = null)
    {
        $this->mealOption = $mealOption;

        if (!empty($mealOption)) {
            $this->mealOptionName = $mealOption->getName();
            $this->mealOptionPrice = $mealOption->getPrice();
        }

        return $this;
    }

    /**
     * Get mealOption
     *
     * @return MealOption
     */
    public function getMealOption()
    {
        return $this->mealOption;
    }

    /**
     * Set mealOptionName
     *
     * @param string $mealOptionName
     *
     * @return OrderMeal
     */
    public function setMealOptionName($mealOptionName)
    {
        $this->mealOptionName = $mealOptionName;

        return $this;
    }

    /**
     * Get mealOptionName
     *
     * @return string
     */
    public function getMealOptionName()
    {
        return $this->mealOptionName;
    }

    /**
     * Set mealOptionPrice
     *
     * @param float $mealOptionPrice
     *
     * @return OrderMeal
     */
    public function setMealOptionPrice($mealOptionPrice)
    {
        $this->mealOptionPrice = $mealOptionPrice;

        return $this;
    }

    /**
     * Get mealOptionPrice
     *
     * @return float
     */
    public function getMealOptionPrice()
    {
        return $this->mealOptionPrice;
    }

    public function isValidMeal(ExecutionContext $context)
    {
        if (!$this->getMeal() || !$this->getOrder()) {
            return ;
        }

        if ($this->getMeal()->getRestaurant() != $this->getOrder()->getRestaurant()) {
            $context->addViolation(
                'orderMeal.meal.restaurantMismatched',
                [
                    '{{ mealName }}' => $this->getMeal()->getName()
                ]
            );
        }
    }

    public function isValidOption(ExecutionContext $context)
    {
        if (!$this->getMeal() || !$this->getMealOption()) {
            return ;
        }

        if ($this->getMeal() != $this->getMealOption()->getMeal()) {
            $context->addViolation(
                'orderMeal.option.mealMismatched',
                [
                    '{{ mealName }}'   => $this->getMeal()->getName(),
                    '{{ optionName }}' => $this->getMealOption()->getName()
                ]
            );
        }
    }

    /**
     * @return float
     */
    public function getDiscountPrice()
    {
        $discount = $this->getPrice() * $this->getMealDiscountPercent() / 100;

        return $this->getPrice() - $discount;
    }

    /**
     * @return float
     */
    public function getMealOptionDiscountPrice()
    {
        $discount = $this->getMealOptionPrice() * $this->getMealDiscountPercent() / 100;

        return $this->getMealOptionPrice() - $discount;
    }

    /**
     * Get mealDiscountPercent
     *
     * @return int
     */
    public function getMealDiscountPercent()
    {
        return $this->getMeal()->getMealDiscountPercent();
    }

    /**
     * @return float
     */
    public function getDiscountPriceIncludingTax()
    {
        return round(
            $this->getDiscountPrice() + $this->getDiscountPrice() * $this->getMeal()->getSalesTaxPercent() / 100,
            2
        );
    }

    /**
     * @return float
     */
    public function getMealOptionDiscountPriceIncludingTax()
    {
        return round(
            $this->getMealOptionDiscountPrice()
            + $this->getMealOptionDiscountPrice() * $this->getMeal()->getSalesTaxPercent() / 100,
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
        return round($this->getPrice() + $this->getPrice() * $this->getMeal()->getSalesTaxPercent() / 100, 2);
    }

    /**
     * Get price including tax
     *
     * @return float
     */
    public function getMealOptionPriceIncludingTax()
    {
        return round(
            $this->getMealOptionPrice() + $this->getMealOptionPrice() * $this->getMeal()->getSalesTaxPercent() / 100,
            2
        );
    }
}