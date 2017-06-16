<?php

namespace Nmotion\NmotionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MealExtraIngredient
 */
class MealExtraIngredient
{
    use EntityAux;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var Meal
     */
    private $meal;

    /**
     * @var string
     */
    private $name;

    /**
     * @var float
     */
    private $price;

    /**
     * @var integer
     */
    private $createdAt;

    /**
     * @var integer
     */
    private $updatedAt;

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
     * Set meal
     *
     * @param Meal $meal
     *
     * @return MealExtraIngredient
     */
    public function setMeal($meal)
    {
        $this->meal = $meal;

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
     * Set name
     *
     * @param string $name
     *
     * @return MealExtraIngredient
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
     * @return MealExtraIngredient
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Set createdAt
     *
     * @param integer $createdAt
     *
     * @return MealExtraIngredient
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
     * @return MealExtraIngredient
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
     * @return float
     */
    public function getDiscountPrice()
    {
        return $this->getPrice() - $this->getPrice() * $this->getMeal()->getMealDiscountPercent() / 100;
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
     * Set price including tax
     *
     * @param float $priceIncludingTax
     *
     * @return MealOption
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
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');

        $configRepository = $em->getRepository('NmotionNmotionBundle:Config');
        $configSalesTax   = $configRepository->findOneBy(['name' => 'sales_tax']);
        if (! $configSalesTax instanceof Config) {
            throw new \RuntimeException('Config parameter "sales_tax" not found');
        }

        $salesTaxPercent = (float)$configSalesTax->getValue();
        if ($salesTaxPercent < 0) {
            throw new \RuntimeException('Config parameter "sales_tax" can not be less than 0');
        }

        return $salesTaxPercent;
    }
}