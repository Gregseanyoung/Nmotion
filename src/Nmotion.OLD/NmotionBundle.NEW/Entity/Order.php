<?php

namespace Nmotion\NmotionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use \Nmotion\NmotionBundle\Exception\PreconditionFailedException;

/**
 * Order
 */
class Order
{
    use EntityAux;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $resourceUrl;

    /**
     * @var Order
     */
    private $master = null;

    /**
     * @var integer
     */
    private $createdAt;

    /**
     * @var integer
     */
    private $updatedAt;

    /**
     * @var User
     */
    private $user;

    /**
     * @var Restaurant
     */
    private $restaurant;

    /**
     * @var OrderStatus
     */
    private $orderStatus;

    /**
     * @var RestaurantServiceType
     */
    private $serviceType;

    /**
     * @var integer
     */
    private $tableNumber;

    /**
     * @var float
     */
    private $productTotal;

    /**
     * @var float
     */
    private $discountPercent;

    /**
     * @var float
     */
    private $discount;

    /**
     * @var float
     */
    private $taxPercent;

    /**
     * @var float
     */
    private $salesTax;

    /**
     * @var float
     */
    private $tips = 0;

    /**
     * @var float
     */
    private $orderTotal;

    /**
     * @var integer
     */
    private $takeawayPickupTime = 0;
    
    /**
     * @var string
     */
    private $pushToken;
    
    /**
     * @var string
     */
    private $contactPhoneNumber;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $orderMeals;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $slaves;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $payments;

    private $statusFlow = [
        [
            'null' => OrderStatus::NEW_ORDER
        ],
        [
            OrderStatus::NEW_ORDER => OrderStatus::PENDING_PAYMENT
        ],
        [
            OrderStatus::NEW_ORDER => OrderStatus::CANCELLED
        ],
        [
            OrderStatus::PENDING_PAYMENT => OrderStatus::CANCELLED
        ]
    ];

    /**
     * @var array Contains allowed properties for consolation (key = property, value = getterName)
     */
    private $allowedConsolidatedProperties = [
        'productTotal' => 'getProductTotal',
        'discount'     => 'getDiscount',
        'salesTax'     => 'getSalesTax',
        'orderTotal'   => 'getOrderTotal'
    ];

    private function getConsolidatedValue($property)
    {
        if (!isset($this->allowedConsolidatedProperties[$property])) {
            throw new \RuntimeException(sprintf('Property "%s" is not allowed for consolidation.', $property));
        }

        $getterName = $this->allowedConsolidatedProperties[$property];
        $total      = $this->{$getterName}();

        if ($this->getSlaves()) {
            /** @var $order Order */
            foreach ($this->getSlaves() as $order) {
                $total += $order->{$getterName}();
            }
        }

        return $total;
    }

    private function evaluate()
    {
        $total    = 0;
        $discount = 0;
	$defaultPrice = 0 ;
	
        foreach ($this->getOrderMeals() as $orderMeal) {
            $meal              = $orderMeal->getMeal();
            $defaultMealOption = $meal ? $meal->getMealOptionDefault() : null;
	if(!empty($defaultMealOption)){
            $defaultPrice +=  $meal->getPrice() * $orderMeal->getQuantity();
		
	}
            $price  = $orderMeal->getMealOptionPrice()
                ? : ($defaultMealOption ? $defaultMealOption->getPrice() : $orderMeal->getPrice());
            $productDiscount = $price * $orderMeal->getMealDiscountPercent() / 100;

            /** @var $orderMealExtraIngredient  OrderMealExtraIngredient */
            foreach ($orderMeal->getOrderMealExtraIngredients() as $orderMealExtraIngredient) {
                $extraIngredientPrice = $orderMealExtraIngredient->getPrice();
                $price += $extraIngredientPrice;
                $productDiscount += $extraIngredientPrice * $orderMeal->getMealDiscountPercent() / 100;
            }
		
            $total += $price * $orderMeal->getQuantity();
            $discount += $productDiscount * $orderMeal->getQuantity();
        }

        $this->setProductTotal($total + $defaultPrice);
	//$this->setProductTotal($total);
        $discount += ($this->getProductTotal() - $discount) * $this->getDiscountPercent() / 100;
        $this->setDiscount($discount);

        $this->setSalesTax(($this->getProductTotal() - $this->getDiscount()) * $this->getTaxPercent() / 100);

        if ($this->getMaster() != null) {
            $this->setTips(0);
        }

        $this->setOrderTotal($this->getProductTotal() - $this->getDiscount() + $this->getSalesTax() + $this->getTips());
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->orderMeals = new ArrayCollection();
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
     * Set resourceUrl
     *
     * @param string $resourceUrl
     *
     * @return Order
     */
    public function setResourceUrl($resourceUrl)
    {
        $this->resourceUrl = $resourceUrl;

        return $this;
    }

    /**
     * Get resourceUrl
     *
     * @return string
     */
    public function getResourceUrl()
    {
        return $this->resourceUrl;
    }

    /**
     * Set master
     *
     * @param Order $master
     *
     * @return Order
     */
    public function setMaster(Order $master = null)
    {
        $this->master = $master;

        return $this;
    }

    /**
     * Get master
     *
     * @return Order
     */
    public function getMaster()
    {
        return $this->master;
    }

    /**
     * Returns boolean regarding order has a linked master order or not
     *
     * @return boolean
     */
    public function hasMaster()
    {
        return $this->master === null;
    }

    /**
     * Set createdAt
     *
     * @param integer $createdAt
     *
     * @return Order
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
     * @return Order
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
     * Set user
     *
     * @param User $user
     *
     * @return Order
     */
    public function setUser(User $user)
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
     * Set restaurant
     *
     * @param Restaurant $restaurant
     *
     * @return Order
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
     * Set orderStatus
     *
     * @param OrderStatus $orderStatus
     *
     * @return Order
     * @throws PreconditionFailedException
     */
    public function setOrderStatus(OrderStatus $orderStatus)
    {
        $this->orderStatus = $orderStatus;

        $updateStatuses = [OrderStatus::CANCELLED, OrderStatus::FAILED, OrderStatus::PAID];
        if ($this->getMaster() == null && in_array($orderStatus->getId(), $updateStatuses)) {
            /** @var Order $slave */
            foreach ($this->getSlaves() as $slave) {
                if ($slave->getOrderStatus()->getId() != OrderStatus::NEW_ORDER
                    && $orderStatus->getId() != OrderStatus::PAID
                ) {
                    throw new PreconditionFailedException('Slaves status should be renewed!');
                } elseif ($slave->getOrderStatus()->getId() != OrderStatus::PAID
                    && $orderStatus->getId() == OrderStatus::PAID
                ) {
                    throw new PreconditionFailedException('Slaves status should be set to PAID!');
                }
            }
        }

        return $this;
    }

    /**
     * Get orderStatus
     *
     * @return OrderStatus
     */
    public function getOrderStatus()
    {
        return $this->orderStatus;
    }

    /**
     * Set serviceType
     *
     * @param RestaurantServiceType $serviceType
     *
     * @return Order
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
     * Set tableNumber
     *
     * @param int $tableNumber
     *
     * @return $this
     */
    public function setTableNumber($tableNumber)
    {
        $this->tableNumber = $tableNumber;

        return $this;
    }

    /**
     * Get tableNumber
     *
     * @return int
     */
    public function getTableNumber()
    {
        return $this->tableNumber;
    }

    /**
     * Set productTotal
     *
     * @param float $productTotal
     *
     * @return Order
     */
    public function setProductTotal($productTotal)
    {
        $this->productTotal = round($productTotal, 2);

        return $this;
    }

    /**
     * Get productTotal
     *
     * @return float
     */
    public function getProductTotal()
    {
        return (double) $this->productTotal;
    }

    /**
     * Set discountPercent
     *
     * @param float $discountPercent
     *
     * @return Order
     */
    public function setDiscountPercent($discountPercent)
    {
        $this->discountPercent = round($discountPercent, 2);

        return $this;
    }

    /**
     * Get discountPercent
     *
     * @return float
     */
    public function getDiscountPercent()
    {
        return (double) $this->discountPercent;
    }

    /**
     * Set discount
     *
     * @param float $discount
     *
     * @return Order
     */
    public function setDiscount($discount)
    {
        $this->discount = round($discount, 2);

        return $this;
    }

    /**
     * Get discount
     *
     * @return float
     */
    public function getDiscount()
    {
        return (double) $this->discount;
    }

    /**
     * Set taxPercent
     *
     * @param float $taxPercent
     *
     * @return Order
     */
    public function setTaxPercent($taxPercent)
    {
        $this->taxPercent = round($taxPercent, 2);

        return $this;
    }

    /**
     * Get taxPercent
     *
     * @return float
     */
    public function getTaxPercent()
    {
        return (double) $this->taxPercent;
    }

    /**
     * Set salesTax
     *
     * @param float $salesTax
     *
     * @return Order
     */
    public function setSalesTax($salesTax)
    {
        $this->salesTax = round($salesTax, 2);

        return $this;
    }

    /**
     * Get salesTax
     *
     * @return float
     */
    public function getSalesTax()
    {
        return (double) $this->salesTax;
    }

    /**
     * Set tips
     *
     * @param float $tips
     *
     * @return Order
     */
    public function setTips($tips)
    {
        $this->tips = round($tips, 2);

        return $this;
    }

    /**
     * Get tips
     *
     * @return float
     */
    public function getTips()
    {
        return (double) $this->tips;
    }

    /**
     * Set orderTotal
     *
     * @param float $orderTotal
     *
     * @return Order
     */
    public function setOrderTotal($orderTotal)
    {
        $this->orderTotal = round($orderTotal, 2);

        return $this;
    }

    /**
     * Get orderTotal
     *
     * @return float
     */
    public function getOrderTotal()
    {
        return (double) $this->orderTotal;
    }

    /**
     * Set takeaway pickup time
     *
     * @param integer $takeawayPickupTime
     * @return Order
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
     * Set push token
     *
     * @param string $pushToken
     * @return Order
     */
    public function setPushToken($pushToken)
    {
        $this->pushToken = $pushToken;

        return $this;
    }

    /**
     * Get push token
     *
     * @return integer
     */
    public function getPushToken()
    {
        return $this->pushToken;
    }
    
    /**
     * Set contact phone number
     *
     * @param string $contactPhoneNumber
     * @return Order
     */
    public function setContactPhoneNumber($contactPhoneNumber)
    {
        $this->contactPhoneNumber = $contactPhoneNumber;

        return $this;
    }

    /**
     * Get contact phone number
     *
     * @return integer
     */
    public function getContactPhoneNumber()
    {
        return $this->contactPhoneNumber;
    }

    public function getOrderTotalInCents()
    {
        return round($this->getOrderTotal() * 100, 0);
    }

    public function getConsolidatedProductTotal()
    {
        return (double) $this->getConsolidatedValue('productTotal');
    }

    public function getConsolidatedDiscount()
    {
        return (double) $this->getConsolidatedValue('discount');
    }

    public function getConsolidatedSalesTax()
    {
        return (double) $this->getConsolidatedValue('salesTax');
    }

    public function getConsolidatedTips()
    {
        return (double) $this->getTips();
    }

    public function getConsolidatedOrderTotal()
    {
        return (double) $this->getConsolidatedValue('orderTotal');
    }

    public function getConsolidatedOrderTotalInCents()
    {
        return round($this->getConsolidatedOrderTotal() * 100, 0);
    }

    public function getOrderTotalWhenSlave()
    {
        return (double) ($this->getOrderTotal() - $this->getTips());
    }

    public function getIsMaster()
    {
        return is_null($this->master);
    }

    /**
     * Add orderMeals
     *
     * @param OrderMeal $orderMeals
     *
     * @return Order
     */
    public function addOrderMeal(OrderMeal $orderMeals)
    {
        $orderMeals->setOrder($this);

        $this->orderMeals[] = $orderMeals;

        return $this;
    }

    /**
     * Remove orderMeals
     *
     * @param OrderMeal $orderMeals
     */
    public function removeOrderMeal(OrderMeal $orderMeals)
    {
        $this->orderMeals->removeElement($orderMeals);
    }

    /**
     * Get orderMeals
     *
     * @return OrderMeal[]|Collection
     */
    public function getOrderMeals()
    {
        return $this->orderMeals;
    }

    /**
     * Get order meals as array
     *
     * @return OrderMeal[]
     */
    public function getOrderMealsAsArray()
    {
        $orderMeals = $this->orderMeals instanceof Collection ? $this->orderMeals->toArray() : $this->orderMeals;

        return array_values($orderMeals);
    }

    /**
     * Add slave
     *
     * @param Order $slave
     * @return Order
     */
    public function addSlave(Order $slave)
    {
        $this->slaves[] = $slave;

        return $this;
    }

    /**
     * Remove slave
     *
     * @param Order $slave
     */
    public function removeSlave(Order $slave)
    {
        $this->slaves->removeElement($slave);
    }

    /**
     * Set slaves
     *
     * @param Order[]|\Doctrine\Common\Collections\Collection $slaves
     *
     * @return Order
     */
    public function setSlaves($slaves)
    {
        $this->slaves = $slaves;

        return $this;
    }

    /**
     * Set payments
     *
     * @param Payment[]|\Doctrine\Common\Collections\Collection $payments
     *
     * @return Order
     */
    public function setPayments($payments)
    {
        $this->payments = $payments;

        return $this;
    }

    /**
     * Add payments
     *
     * @param Payment $payments
     * @return Order
     */
    public function addPayment(Payment $payments)
    {
        $this->payments[] = $payments;

        return $this;
    }

    /**
     * Remove payments
     *
     * @param Payment $payments
     */
    public function removePayment(Payment $payments)
    {
        $this->payments->removeElement($payments);
    }

    /**
     * Get payments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPayments()
    {
        return $this->payments;
    }

    /**
     * Get slaves
     *
     * @return Order[]|\Doctrine\Common\Collections\Collection
     */
    public function getSlaves()
    {
        return $this->slaves;
    }

    public function canGuestChangeStatusTo($orderStatusId)
    {
        $thisOrderStatusId = $this->getOrderStatus() ? $this->getOrderStatus()->getId() : 'null';
        $orderStatusId     = $orderStatusId ? : 'null';

        return ($thisOrderStatusId === $orderStatusId)
            || in_array([$thisOrderStatusId => $orderStatusId], $this->statusFlow);
    }

    public function onPrePersist()
    {
        if ($this->getOrderStatus()
            && in_array($this->getOrderStatus()->getId(), [OrderStatus::NEW_ORDER, OrderStatus::PENDING_PAYMENT])
        ) {
            $this->evaluate();
        }
    }

    public function onPreUpdate()
    {
        if ($this->getOrderStatus()
            && in_array($this->getOrderStatus()->getId(), [OrderStatus::NEW_ORDER, OrderStatus::PENDING_PAYMENT])
        ) {
            $this->evaluate();
        }
    }

    /**
     * Returns true whenever this order status is NEW_ORDER
     *
     * @return bool
     */
    public function isInNewStatus()
    {
        return $this->getOrderStatus() && ($this->getOrderStatus()->getId() == OrderStatus::NEW_ORDER);
    }

    /**
     * Returns true whenever this order status is PAID
     *
     * @return bool
     */
    public function isInPaidStatus()
    {
        return $this->getOrderStatus() && ($this->getOrderStatus()->getId() == OrderStatus::PAID);
    }

    /**
     * Returns true whenever this order status is SENT_TO_PRINTER
     *
     * @return bool
     */
    public function isInSentToPrinterStatus()
    {
        return $this->getOrderStatus() && ($this->getOrderStatus()->getId() == OrderStatus::SENT_TO_PRINTER);
    }
}