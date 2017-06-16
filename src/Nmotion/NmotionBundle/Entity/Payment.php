<?php

namespace Nmotion\NmotionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Payment
 */
class Payment
{
    use EntityAux;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $status;

    /**
     * @var float
     */
    private $amount;

    /**
     * @var float
     */
    private $fee;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var string
     */
    private $test;

    /**
     * @var string
     */
    private $transaction;

    /**
     * @var string
     */
    private $acquirer;

    /**
     * @var string
     */
    private $cardNumberMasked;

    /**
     * @var string
     */
    private $expMonth;

    /**
     * @var string
     */
    private $expYear;

    /**
     * @var string
     */
    private $cardTypeName;

    /**
     * @var string
     */
    private $merchant;

    /**
     * @var string
     */
    private $ticket;

    /**
     * @var string
     */
    private $allParameters;

    /**
     * @var string
     */
    private $paymentComment;

    /**
     * @var integer
     */
    private $createdAt;

    /**
     * @var integer
     */
    private $updatedAt;

    /**
     * @var Order
     */
    private $order;


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
     * Set order
     *
     * @param Order $order
     * @return Payment
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
     * Set status
     *
     * @param string $status
     * @return Payment
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set amount
     *
     * @param float $amount
     * @return Payment
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set fee
     *
     * @param float $fee
     * @return Payment
     */
    public function setFee($fee)
    {
        $this->fee = $fee;

        return $this;
    }

    /**
     * Get fee
     *
     * @return float
     */
    public function getFee()
    {
        return $this->fee;
    }

    /**
     * Set currency
     *
     * @param string $currency
     * @return Payment
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set test
     *
     * @param string $test
     * @return Payment
     */
    public function setTest($test)
    {
        $this->test = $test;

        return $this;
    }

    /**
     * Get test
     *
     * @return string
     */
    public function getTest()
    {
        return $this->test;
    }

    /**
     * Set transaction
     *
     * @param string $transaction
     * @return Payment
     */
    public function setTransaction($transaction)
    {
        $this->transaction = $transaction;

        return $this;
    }

    /**
     * Get transaction
     *
     * @return string
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * Set acquirer
     *
     * @param string $acquirer
     * @return Payment
     */
    public function setAcquirer($acquirer)
    {
        $this->acquirer = $acquirer;

        return $this;
    }

    /**
     * Get acquirer
     *
     * @return string
     */
    public function getAcquirer()
    {
        return $this->acquirer;
    }

    /**
     * Set cardNumberMasked
     *
     * @param string $cardNumberMasked
     * @return Payment
     */
    public function setCardNumberMasked($cardNumberMasked)
    {
        $this->cardNumberMasked = $cardNumberMasked;

        return $this;
    }

    /**
     * Get cardNumberMasked
     *
     * @return string
     */
    public function getCardNumberMasked()
    {
        return $this->cardNumberMasked;
    }

    /**
     * Set expMonth
     *
     * @param string $expMonth
     * @return Payment
     */
    public function setExpMonth($expMonth)
    {
        $this->expMonth = $expMonth;

        return $this;
    }

    /**
     * Get expMonth
     *
     * @return string
     */
    public function getExpMonth()
    {
        return $this->expMonth;
    }

    /**
     * Set expYear
     *
     * @param string $expYear
     * @return Payment
     */
    public function setExpYear($expYear)
    {
        $this->expYear = $expYear;

        return $this;
    }

    /**
     * Get expYear
     *
     * @return string
     */
    public function getExpYear()
    {
        return $this->expYear;
    }

    /**
     * Set cardTypeName
     *
     * @param string $cardTypeName
     * @return Payment
     */
    public function setCardTypeName($cardTypeName)
    {
        $this->cardTypeName = $cardTypeName;

        return $this;
    }

    /**
     * Get cardTypeName
     *
     * @return string
     */
    public function getCardTypeName()
    {
        return $this->cardTypeName;
    }

    /**
     * Set merchant
     *
     * @param string $merchant
     * @return Payment
     */
    public function setMerchant($merchant)
    {
        $this->merchant = $merchant;

        return $this;
    }

    /**
     * Get merchant
     *
     * @return string
     */
    public function getMerchant()
    {
        return $this->merchant;
    }

    /**
     * Set ticket
     *
     * @param string $ticket
     * @return Payment
     */
    public function setTicket($ticket)
    {
        $this->ticket = $ticket;

        return $this;
    }

    /**
     * Get ticket
     *
     * @return string
     */
    public function getTicket()
    {
        return $this->ticket;
    }

    /**
     * Set allParameters
     *
     * @param string $allParameters
     * @return Payment
     */
    public function setAllParameters($allParameters)
    {
        $this->allParameters = $allParameters;

        return $this;
    }

    /**
     * Get allParameters
     *
     * @return string
     */
    public function getAllParameters()
    {
        return $this->allParameters;
    }

    /**
     * Set paymentComment
     *
     * @param string $paymentComment
     * @return Payment
     */
    public function setPaymentComment($paymentComment)
    {
        $this->paymentComment = $paymentComment;

        return $this;
    }

    /**
     * Get paymentComment
     *
     * @return string
     */
    public function getPaymentComment()
    {
        return $this->paymentComment;
    }

    /**
     * Set createdAt
     *
     * @param integer $createdAt
     * @return Payment
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
     * @return Payment
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
}
