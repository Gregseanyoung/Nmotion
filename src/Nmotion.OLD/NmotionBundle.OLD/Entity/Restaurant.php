<?php

namespace Nmotion\NmotionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\ExecutionContext;

/**
 * Restaurant
 */
class Restaurant
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
    private $facebookPlaceId;

    /**
     * @var string
     */
    private $fullDescription;

    /**
     * @var Asset
     */
    private $logoAsset;

    /**
     * @var string
     */
    private $feedbackUrl;

    /**
     * @var string
     */
    private $videoUrl;

    /**
     * @var string
     */
    private $timeZone;

    /**
     * @var int
     */
    private $checkOutTime = 480;

    /**
     * @var boolean
     */
    private $visible = false;

    /**
     * @var boolean
     */
    private $inHouse = false;

    /**
     * @var boolean
     */
    private $takeaway = false;

    /**
     * @var boolean
     */
    private $roomService = false;

    /**
     * @var boolean
     */
    private $taMember = false;

    /**
     * @var string
     */
    private $phone;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $siteUrl;

    /**
     * @var string
     */
    private $contactPersonName;

    /**
     * @var string
     */
    private $contactPersonEmail;

    /**
     * @var string
     */
    private $contactPersonPhone;

    /**
     * @var string
     */
    private $legalEntity;

    /**
     * @var string
     */
    private $invoicingPeriod = 'monthly';
    
    /**
     * @var Currency
     */
    private $currency;
    
    /**
     * @var int
     */
    private $currencyId;
    
    /**
     * @var string
     */
    private $vatNo;

    /**
     * @var string
     */
    private $regNo;

    /**
     * @var string
     */
    private $kontoNo;

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
    private $adminUser;

    /**
     * @var RestaurantAddress
     */
    private $address;

    /**
     * @var Collection
     */
    private $operationTimes;

    /**
     * @var Collection
     */
    private $menuCategories;

    /**
     * @var Collection
     */
    private $menuMeals;

    /**
     * @var Collection
     */
    private $checkins;

    /**
     * @var Collection
     */
    private $staff;

    /**
     * @var float
     */
    private $distance;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->operationTimes = new ArrayCollection();
        $this->menuCategories = new ArrayCollection();
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
     * @return Restaurant
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
     * Set facebookPlaceId
     *
     * @param string $facebookPlaceId
     * @return Restaurant
     */
    public function setFacebookPlaceId($facebookPlaceId)
    {
        $this->facebookPlaceId = $facebookPlaceId;

        return $this;
    }

    /**
     * Get facebookPlaceId
     *
     * @return string
     */
    public function getFacebookPlaceId()
    {
        return $this->facebookPlaceId;
    }

    /**
     * Set fullDescription
     *
     * @param string $fullDescription
     * @return Restaurant
     */
    public function setFullDescription($fullDescription)
    {
        $this->fullDescription = $fullDescription;

        return $this;
    }

    /**
     * Get fullDescription
     *
     * @return string
     */
    public function getFullDescription()
    {
        return $this->fullDescription;
    }

    /**
     * Set logoAsset
     *
     * @param Asset $logoAsset
     * @return Restaurant
     */
    public function setLogoAsset(Asset $logoAsset = null)
    {
        $this->logoAsset = $logoAsset;

        return $this;
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
     * Set feedbackUrl
     *
     * @param string $feedbackUrl
     * @return Restaurant
     */
    public function setFeedbackUrl($feedbackUrl)
    {
        $this->feedbackUrl = $feedbackUrl;

        return $this;
    }

    /**
     * Get feedbackUrl
     *
     * @return string
     */
    public function getFeedbackUrl()
    {
        return $this->feedbackUrl;
    }

    /**
     * Set videoUrl
     *
     * @param string $videoUrl
     * @return Restaurant
     */
    public function setVideoUrl($videoUrl)
    {
        $this->videoUrl = $videoUrl;

        return $this;
    }

    /**
     * Get videoUrl
     *
     * @return string
     */
    public function getVideoUrl()
    {
        return $this->videoUrl;
    }

    /**
     * Set timeZone
     *
     * @param string $timeZone
     * @return Restaurant
     */
    public function setTimeZone($timeZone)
    {
        $this->timeZone = $timeZone;

        return $this;
    }

    /**
     * Get timeZone
     *
     * @return string
     */
    public function getTimeZone()
    {
        return $this->timeZone;
    }

    /**
     * Set checkOutTime
     *
     * @param int $checkOutTime
     * @return Restaurant
     */
    public function setCheckOutTime($checkOutTime)
    {
        $this->checkOutTime = $checkOutTime;

        return $this;
    }

    /**
     * Get checkOutTime
     *
     * @return int
     */
    public function getCheckOutTime()
    {
        return $this->checkOutTime;
    }

    /**
     * Get checkOutTime
     *
     * @return int
     */
    public function getCheckOutTimeInSeconds()
    {
        return $this->checkOutTime * 60;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     *
     * @return $this
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isVisible()
    {
        return (boolean) $this->visible;
    }

    /**
     * Set inHouse service type
     *
     * @param boolean $inHouse
     *
     * @return $this
     */
    public function setInHouse($inHouse)
    {
        $this->inHouse = $inHouse;

        return $this;
    }

    /**
     * Is inHouse restaurant?
     *
     * @return boolean
     */
    public function isInHouse()
    {
        return (boolean) $this->inHouse;
    }

    /**
     * Set takeaway
     *
     * @param boolean $takeaway
     *
     * @return $this
     */
    public function setTakeaway($takeaway)
    {
        $this->takeaway = $takeaway;

        return $this;
    }

    /**
     * Is takeaway restaurant?
     *
     * @return boolean
     */
    public function isTakeaway()
    {
        return (boolean) $this->takeaway;
    }

    /**
     * Set roomService service type
     *
     * @param boolean $roomService
     *
     * @return $this
     */
    public function setRoomService($roomService)
    {
        $this->roomService = $roomService;

        return $this;
    }

    /**
     * Is roomService restaurant?
     *
     * @return boolean
     */
    public function isRoomService()
    {
        return (boolean) $this->roomService;
    }

    /**
     * Set trade association member flag
     *
     * @param boolean $taMember
     * @return Restaurant
     */
    public function setTAMember($taMember)
    {
        $this->taMember = $taMember;

        return $this;
    }

    /**
     * Is trade association member?
     *
     * @return boolean
     */
    public function isTAMember()
    {
        return $this->taMember;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return Restaurant
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Restaurant
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set siteUrl
     *
     * @param string $siteUrl
     * @return Restaurant
     */
    public function setSiteUrl($siteUrl)
    {
        $this->siteUrl = $siteUrl;

        return $this;
    }

    /**
     * Get siteUrl
     *
     * @return string
     */
    public function getSiteUrl()
    {
        return $this->siteUrl;
    }

    /**
     * Set contactPersonName
     *
     * @param string $contactPersonName
     * @return Restaurant
     */
    public function setContactPersonName($contactPersonName)
    {
        $this->contactPersonName = $contactPersonName;

        return $this;
    }

    /**
     * Get contactPersonName
     *
     * @return string
     */
    public function getContactPersonName()
    {
        return $this->contactPersonName;
    }

    /**
     * Set contactPersonEmail
     *
     * @param string $contactPersonEmail
     * @return Restaurant
     */
    public function setContactPersonEmail($contactPersonEmail)
    {
        $this->contactPersonEmail = $contactPersonEmail;

        return $this;
    }

    /**
     * Get contactPersonEmail
     *
     * @return string
     */
    public function getContactPersonEmail()
    {
        return $this->contactPersonEmail;
    }

    /**
     * Set contactPersonPhone
     *
     * @param string $contactPersonPhone
     * @return Restaurant
     */
    public function setContactPersonPhone($contactPersonPhone)
    {
        $this->contactPersonPhone = $contactPersonPhone;

        return $this;
    }

    /**
     * Get contactPersonPhone
     *
     * @return string
     */
    public function getContactPersonPhone()
    {
        return $this->contactPersonPhone;
    }

    /**
     * Set legalEntity
     *
     * @param string $legalEntity
     * @return Restaurant
     */
    public function setLegalEntity($legalEntity)
    {
        $this->legalEntity = $legalEntity;

        return $this;
    }

    /**
     * Get legalEntity
     *
     * @return string
     */
    public function getLegalEntity()
    {
        return $this->legalEntity;
    }

    /**
     * Set invoicingPeriod
     *
     * @param string $invoicingPeriod
     * @return Restaurant
     */
    public function setInvoicingPeriod($invoicingPeriod)
    {
        $this->invoicingPeriod = $invoicingPeriod;

        return $this;
    }

    /**
     * Get invoicingPeriod
     *
     * @return string
     */
    public function getInvoicingPeriod()
    {
        return $this->invoicingPeriod;
    }
    
    /**
     * Set Currency
     *
     * @param string $currency
     * @return Currency
     */
    public function setCurrency(Currency $currency = null)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get Currency
     *
     * @return Currency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set vatNo
     *
     * @param string $vatNo
     * @return Restaurant
     */
    public function setVatNo($vatNo)
    {
        $this->vatNo = $vatNo;

        return $this;
    }

    /**
     * Get vatNo
     *
     * @return string
     */
    public function getVatNo()
    {
        return $this->vatNo;
    }

    /**
     * Set regNo
     *
     * @param string $regNo
     * @return Restaurant
     */
    public function setRegNo($regNo)
    {
        $this->regNo = $regNo;

        return $this;
    }

    /**
     * Get regNo
     *
     * @return string
     */
    public function getRegNo()
    {
        return $this->regNo;
    }

    /**
     * Set kontoNo
     *
     * @param string $kontoNo
     * @return Restaurant
     */
    public function setKontoNo($kontoNo)
    {
        $this->kontoNo = $kontoNo;

        return $this;
    }

    /**
     * Get kontoNo
     *
     * @return string
     */
    public function getKontoNo()
    {
        return $this->kontoNo;
    }

    /**
     * Set createdAt
     *
     * @param integer $createdAt
     * @return Restaurant
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
     * @return Restaurant
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
     * Set adminUser
     *
     * @param User $adminUser
     * @return Restaurant
     */
    public function setAdminUser(User $adminUser = null)
    {
        $this->adminUser = $adminUser;

        return $this;
    }

    /**
     * Get adminUser
     *
     * @return User
     */
    public function getAdminUser()
    {
        return $this->adminUser;
    }

    /**
     * Set address
     *
     * @param RestaurantAddress $address
     * @return Restaurant
     */
    public function setAddress(RestaurantAddress $address = null)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return RestaurantAddress
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set operation times
     *
     * @param Collection|RestaurantOperationTime[] $operationTimes
     *
     * @return Restaurant
     */
    public function setOperationTimes(array $operationTimes = [])
    {
        foreach ($operationTimes as $operationTime) {
            $operationTime->setRestaurant($this);
        }

        $this->operationTimes = $operationTimes;

        return $this;
    }

    /**
     * Add operationTimes
     *
     * @param RestaurantOperationTime $operationTime
     *
     * @return Restaurant
     */
    public function addOperationTime(RestaurantOperationTime $operationTime)
    {
        $operationTime->setRestaurant($this);

        $this->operationTimes[] = $operationTime;

        return $this;
    }

    /**
     * Remove operationTimes
     *
     * @param RestaurantOperationTime $operationTime
     */
    public function removeOperationTime(RestaurantOperationTime $operationTime)
    {
        $this->operationTimes->removeElement($operationTime);
    }

    /**
     * Get restaurant operation times collection
     *
     * @return Collection
     */
    public function getOperationTimes()
    {
        return $this->operationTimes;
    }

    /**
     * Set operation times
     *
     * @param Collection|array|MenuCategory[] $menuCategories
     *
     * @return Restaurant
     */
    public function setMenuCategories(array $menuCategories = [])
    {
        foreach ($menuCategories as $menuCategory) {
            $menuCategory->setRestaurant($this);
        }

        $this->menuCategories = $menuCategories;

        return $this;
    }

    /**
     * Add menu category
     *
     * @param MenuCategory $menuCategory
     * @return Restaurant
     */
    public function addMenuCategory(MenuCategory $menuCategory)
    {
        $menuCategory->setRestaurant($this);
        $this->menuCategories[] = $menuCategory;

        return $this;
    }

    /**
     * Remove menu category
     *
     * @param MenuCategory $menuCategory
     */
    public function removeMenuCategory(MenuCategory $menuCategory)
    {
        $this->menuCategories->removeElement($menuCategory);
    }

    /**
     * Get menu categories collection
     *
     * @return Collection
     */
    public function getMenuCategories()
    {
        return $this->menuCategories;
    }

    /**
     * Set menu meals
     *
     * @param Collection|Meal[] $menuMeals
     *
     * @return MenuCategory
     */
    public function setMenuMeals(array $menuMeals = [])
    {
        foreach ($menuMeals as $menuMeal) {
            $menuMeal->setRestaurant($this);
        }

        $this->menuMeals = $menuMeals;

        return $this;
    }

    /**
     * Add menu meal
     *
     * @param Meal $menuMeal
     * @return MenuCategory
     */
    public function addMenuMeal(Meal $menuMeal)
    {
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
    public function getMenuMeals()
    {
        return $this->menuMeals;
    }

    /**
     * Add check-in
     *
     * @param RestaurantCheckin $checkin
     * @return Restaurant
     */
    public function addCheckin(RestaurantCheckin $checkin)
    {
        $this->checkins[] = $checkin;

        return $this;
    }

    /**
     * Remove check-in
     *
     * @param RestaurantCheckin $checkin
     */
    public function removeCheckin(RestaurantCheckin $checkin)
    {
        $this->checkins->removeElement($checkin);
    }

    /**
     * Get checkins
     *
     * @return Collection
     */
    public function getCheckins()
    {
        return $this->checkins;
    }

    /**
     * Get distance to restaurant
     * Property will be used and contain float result if search is called
     *
     * @param float $distance Distance to restaurant in kilometers
     *
     * @return Restaurant
     */
    public function setDistance($distance)
    {
        $this->distance = ($distance === null) ? null : (float) $distance;

        return $this;
    }

    /**
     * Set distance to restaurant
     * Property will be used and set float result if search is called
     *
     * @return float
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * Add staff
     *
     * @param RestaurantStaff $staff
     * @return Restaurant
     */
    public function addStaff(RestaurantStaff $staff)
    {
        $staff->setRestaurant($this);
        $this->staff[] = $staff;

        return $this;
    }

    /**
     * Remove staff
     *
     * @param RestaurantStaff $staff
     */
    public function removeStaff(RestaurantStaff $staff)
    {
        $this->staff->removeElement($staff);
    }

    /**
     * Get staff
     *
     * @return Collection|RestaurantStaff[]
     */
    public function getStaff()
    {
        return $this->staff;
    }

    /**
     * Set staff
     *
     * @param Collection|RestaurantStaff[] $staff
     *
     * @return Restaurant
     */
    public function setStaff(array $staff)
    {
        foreach ($staff as $entry) {
            $entry->setRestaurant($this);
        }

        $this->staff = $staff;

        return $this;
    }

    /**
     * @param integer $time Time against which to check that the restaurant is open
     * @return boolean
     */
    public function isOpen($time = null)
    {
        $currentDayTime       = $time ? : time();
        $currentDayOfTheWeek  = date('N', $currentDayTime);
        $time                 = ($currentDayTime + (int)date('Z')) % 86400;
        $previousDayTime      = strtotime('-1 day', $currentDayTime);
        $previousDayOfTheWeek = date('N', $previousDayTime);

        foreach ($this->getOperationTimes() as $operationTime) {

            // if time = NULL restaurant is closed
            if ($operationTime->getTimeFrom() === null || $operationTime->getTimeTo() === null) {
                continue;
            }

            if (
                    // check previous day overnight operation time
                    (
                        $operationTime->getDayOfTheWeek() == $previousDayOfTheWeek
                        &&
                        $operationTime->getTimeFrom() >= $operationTime->getTimeTo()
                        &&
                        $this->checkTimeInRange($time, 0, $operationTime->getTimeTo())
                    )
                    ||
                    // check current day interval set between 00:00 and 24:00
                    (
                        $operationTime->getDayOfTheWeek() == $currentDayOfTheWeek
                        &&
                        $operationTime->getTimeFrom() < $operationTime->getTimeTo()
                        &&
                        $this->checkTimeInRange($time, $operationTime->getTimeFrom(), $operationTime->getTimeTo())
                    )
                    ||
                    // check current day overnight operation time
                    (
                        $operationTime->getDayOfTheWeek() == $currentDayOfTheWeek
                        &&
                        $operationTime->getTimeFrom() >= $operationTime->getTimeTo()
                        &&
                        $this->checkTimeInRange($time, $operationTime->getTimeFrom(), 86400)
                    )
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check time inside given period
     *
     * @param int $time
     * @param int $timeFrom
     * @param int $timeTo
     * @return boolean
     */
    private function checkTimeInRange($time, $timeFrom, $timeTo)
    {
        return (($time >= $timeFrom) && ($time <= $timeTo));
    }

    /**
     * Validate-method for restaurant service type
     *
     * @param ExecutionContext $context
     *
     * @return void
     */
    public function isValidServiceType(ExecutionContext $context)
    {
        if ($this->inHouse || $this->takeaway || $this->roomService) {
            return ;
        }

        $context->addViolationAt(
            'inHouse',
            'restaurant.serviceType.atLeastOneType',
            []
        );
    }
}
