<?php

namespace Nmotion\NmotionBundle\Entity;

use Symfony\Component\Validator\ExecutionContext;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Meal
 */
class Meal
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
     * @var boolean
     */
    private $visible;

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
     * @var Asset
     */
    private $logoAsset;

    /**
     * @var Asset
     */
    private $thumbLogoAsset;


    /**
     * @var Restaurant
     */
    private $restaurant;

    /**
     * @var MenuCategory
     */
    private $menuCategory;

    /**
     * @var Collection
     */
    private $mealOptions;

    /**
     * @var integer
     */
    private $mealOptionDefaultId;

    /**
     * @var Collection
     */
    private $mealExtraIngredients;

    /**
     * @var integer
     */
    private $discountPercent = 0;

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
     * @return Meal
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
     * @return Meal
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
     * @return Meal
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
     * Set visible
     *
     * @param boolean $visible
     *
     * @return Meal
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
     * Set position
     *
     * @param integer $position
     *
     * @return Meal
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
     * @return Meal
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
     * @return Meal
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
     * Set logoAsset
     *
     * @param Asset $logoAsset
     * @return Meal
     */
    public function setLogoAsset(Asset $logoAsset = null)
    {
        $this->logoAsset = $logoAsset;

        return $this;
    }

    /**
     * Check if logoAsset is not a null
     *
     * @return boolean
     */
    public function hasLogoAsset()
    {
        return $this->logoAsset !== null;
    }

    /**
     * Get logoAsset
     *
     * @return Asset
     */
    public function getLogoAsset()
    {
        return $this->logoAsset;
    }

    /**
     * Set thumbLogoAsset
     *
     * @param Asset $thumbLogoAsset
     * @return Meal
     */
    public function setThumbLogoAsset(Asset $thumbLogoAsset = null)
    {
        $this->thumbLogoAsset = $thumbLogoAsset;

        return $this;
    }

    /**
     * Check if thumbLogoAsset is not a null
     *
     * @return boolean
     */
    public function hasThumbLogoAsset()
    {
        return $this->thumbLogoAsset !== null;
    }

    /**
     * Get thumbLogoAsset
     *
     * @return Asset
     */
    public function getThumbLogoAsset()
    {
        return $this->thumbLogoAsset;
    }

    /**
     * Set restaurant
     *
     * @param Restaurant $restaurant
     *
     * @return Meal
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
     * Set restaurant menu category
     *
     * @param MenuCategory $menuCategory
     *
     * @return Meal
     */
    public function setMenuCategory(MenuCategory $menuCategory = null)
    {
        $this->menuCategory = $menuCategory;
        $this->setRestaurant($menuCategory->getRestaurant());

        return $this;
    }

    /**
     * Get restaurant menu category
     *
     * @return MenuCategory
     */
    public function getMenuCategory()
    {
        return $this->menuCategory;
    }

    /**
     * Set meal options
     *
     * @param Collection|array|MealOption[] $mealOptions
     *
     * @return Meal
     */
    public function setMealOptions(array $mealOptions = [])
    {
        foreach ($mealOptions as $mealOption) {
            $mealOption->setMeal($this);
        }

        $this->mealOptions = $mealOptions;

        return $this;
    }

    /**
     * Add meal option
     *
     * @param MealOption $mealOption
     * @return Meal
     */
    public function addMealOption(MealOption $mealOption)
    {
        $mealOption->setMeal($this);

        $this->mealOptions[] = $mealOption;

        return $this;
    }

    /**
     * Remove meal option
     *
     * @param MealOption $mealOption
     */
    public function removeMealOption(MealOption $mealOption)
    {
        $this->mealOptions->removeElement($mealOption);
    }

    /**
     * Get meal options collection
     *
     * @return MealOption[]|Collection
     */
    public function getMealOptions()
    {
        return $this->mealOptions;
    }

    /**
     * Set mealOptionDefaultId
     *
     * @param integer $mealOptionDefaultId
     *
     * @return Meal
     */
    public function setMealOptionDefaultId($mealOptionDefaultId)
    {
        $this->mealOptionDefaultId = $mealOptionDefaultId;

        return $this;
    }

    /**
     * Get mealOptionDefaultId
     *
     * @return integer
     */
    public function getMealOptionDefaultId()
    {
        return $this->mealOptionDefaultId;
    }

    /**
     * Get default MealOption
     * @return MealOption|null
     */
    public function getMealOptionDefault()
    {
        $mealOptionDefault = null;
        $optionDefaultId = $this->getMealOptionDefaultId();
        if ($optionDefaultId) {
            /** @var $mealOption MealOption */
            foreach ($this->getMealOptions() as $mealOption) {
                if ($mealOption->getId() == $optionDefaultId) {
                    $mealOptionDefault = $mealOption;
                    break;
                }
            }
        }

        return $mealOptionDefault;
    }

    /**
     * Set first meal option as default
     *
     * @return Meal
     */
    public function setFirstMealOptionDefault()
    {
        if (!$this->mealOptionDefaultId && count($this->mealOptions) > 0) {
            foreach ($this->mealOptions as $mealOption) {
                $this->setMealOptionDefaultId($mealOption->getId());
                break;
            }
        }

        return $this;
    }

    /**
     * Set meal extra ingredients
     *
     * @param Collection|array|MealExtraIngredient[] $mealExtraIngredients
     *
     * @return Meal
     */
    public function setMealExtraIngredients(array $mealExtraIngredients = [])
    {
        foreach ($mealExtraIngredients as $mealExtraIngredient) {
            $mealExtraIngredient->setMeal($this);
        }

        $this->mealExtraIngredients = $mealExtraIngredients;

        return $this;
    }

    /**
     * Add meal extra ingredient
     *
     * @param MealExtraIngredient $mealExtraIngredient
     * @return Meal
     */
    public function addMealExtraIngredient(MealExtraIngredient $mealExtraIngredient)
    {
        $mealExtraIngredient->setMeal($this);

        $this->mealExtraIngredients[] = $mealExtraIngredient;

        return $this;
    }

    /**
     * Remove meal extra ingredient
     *
     * @param MealExtraIngredient $mealExtraIngredient
     */
    public function removeMealExtraIngredient(MealExtraIngredient $mealExtraIngredient)
    {
        $this->mealExtraIngredients->removeElement($mealExtraIngredient);
    }

    /**
     * Get meal extra ingredients collection
     *
     * @return Collection
     */
    public function getMealExtraIngredients()
    {
        return $this->mealExtraIngredients;
    }

    /**
     * Set discountPercent
     *
     * @param int $discountPercent
     *
     * @return Meal
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
     * Get mealDiscount
     *
     * @return int
     */
    public function getMealDiscountPercent()
    {
        return $this->discountPercent ?: $this->menuCategory->getDiscountPercent();
    }

    /**
     * @return float
     */
    public function getDiscountPrice()
    {
        return $this->getPrice() - $this->getPrice() * $this->getMealDiscountPercent() / 100;
    }

    /**
     * @return float
     */
    public function getDiscountPriceIncludingTax()
    {
        return round($this->getDiscountPrice() + $this->getDiscountPrice() * $this->getSalesTaxPercent() / 100, 2);
    }

    /**
     * Set price including tax
     *
     * @param float $priceIncludingTax
     *
     * @return Meal
     */
    public function setPriceIncludingTax($priceIncludingTax)
    {
        $this->price = round((float)$priceIncludingTax / (1 + $this->getSalesTaxPercent() / 100), 4);

        return $this;
    }

    /**
     * Get price including tax
     *
     * @return float
     */
    public function getPriceIncludingTax()
    {
        return round($this->getPrice() + $this->getPrice() * $this->getSalesTaxPercent() / 100, 2);
    }

    /**
     * Get sales tax percent
     *
     * @return float
     * @throws \RuntimeException
     */
    public function getSalesTaxPercent()
    {
        // not recommended dirty hack for getting EntityManager inside entity
        global $kernel;
        if ('AppCache' == get_class($kernel)) {
            $kernel = $kernel->getKernel();
        }
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');

        $configRepository = $em->getRepository('NmotionNmotionBundle:Config');
        $configSalesTax   = $configRepository->findOneBy(['name' => 'sales_tax']);
        if (!$configSalesTax instanceof Config) {
            throw new \RuntimeException('Config parameter "sales_tax" not found');
        }

        $salesTaxPercent = (float) $configSalesTax->getValue();
        if ($salesTaxPercent < 0) {
            throw new \RuntimeException('Config parameter "sales_tax" can not be less than 0');
        }

        return $salesTaxPercent;
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
        $this->mealOptions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->mealExtraIngredients = new \Doctrine\Common\Collections\ArrayCollection();
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