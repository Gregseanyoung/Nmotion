<?php

namespace Nmotion\NmotionBundle\Entity\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Query;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Nmotion\NmotionBundle\Entity\Restaurant;
use Nmotion\NmotionBundle\Entity\RestaurantCheckin;
use Nmotion\NmotionBundle\Entity\RestaurantServiceType;
use Nmotion\NmotionBundle\Entity\User;

class RestaurantCheckinRepository extends EntityRepository
{
    /**
     * @param int|User       $user
     * @param int|Restaurant $restaurant
     *
     * @return RestaurantCheckin
     * @throws \InvalidArgumentException
     */
    public function getUserLastCheckinInRestaurant($user, $restaurant)
    {
        if ((!is_numeric($user)) && (!$user instanceof User)) {
            throw new \InvalidArgumentException(
                'Argument 1 passed to ' . __FUNCTION__ . ' must be numeric'
                . ' or an instance of User, ' . gettype($user) . ' given'
            );
        }

        if ((!is_numeric($restaurant)) && (!$restaurant instanceof Restaurant)) {
            throw new \InvalidArgumentException(
                'Argument 2 passed to ' . __FUNCTION__ . ' must be numeric'
                . ' or an instance of Restaurant, ' . gettype($restaurant) . ' given'
            );
        }

        return $this->findOneBy(
            ['user' => $user, 'restaurant' => $restaurant],
            ['id' => 'DESC']
        );
    }

    /**
     * @param RestaurantCheckin $checkin
     *
     * @return array
     */
    public function getTableMates(RestaurantCheckin $checkin)
    {
        $activeCheckins = $this->createQueryBuilder('c')
            ->where('c.tableNumber = :tableNumber')
            ->andWhere('c.serviceType = :serviceType')
            ->andWhere('c.restaurant = :restaurant')
            ->andWhere('c.checkedOut = :checkedOut')
            ->andWhere('c.user != :user')
            ->setParameters(
                [
                    'tableNumber' => $checkin->getTableNumber(),
                    'serviceType' => is_null($checkin->getServiceType()) ? null : $checkin->getServiceType()->getId(),
                    'checkedOut' => false,
                    'restaurant' => $checkin->getRestaurant()->getId(),
                    'user' => $checkin->getUser()->getId()
                ]
            )
            ->orderBy('c.id', 'DESC')
            ->getQuery()->getResult();

        $result      = [];
        $hackUserIds = [];

        /** @var $checkin RestaurantCheckin */
        foreach ($activeCheckins as $checkin) {
            $userId = $checkin->getUser()->getId();
            if (!in_array($userId, $hackUserIds)) {
                $result[]      = $checkin;
                $hackUserIds[] = $userId;
            }
        }

        return $result;
    }

    /**
     * @param int|User $user
     *
     * @return RestaurantCheckin
     * @throws \InvalidArgumentException
     * @throws NotFoundHttpException
     */
    public function getUserActiveCheckin($user)
    {
        if ((!is_numeric($user)) && (!$user instanceof User)) {
            throw new \InvalidArgumentException(
                'Argument 1 passed to ' . __FUNCTION__ . ' must be numeric'
                . ' or an instance of User, ' . gettype($user) . ' given'
            );
        }

        $checkin = $this->findOneBy(
            ['user' => $user, 'checkedOut' => false],
            ['id' => 'DESC']
        );

        if (!($checkin instanceof RestaurantCheckin)) {
            throw new NotFoundHttpException('Active Restaurant Checkin not found.');
        }

        return $checkin;
    }

    /**
     * @param Restaurant            $restaurant
     * @param string                $tableNumber
     * @param RestaurantServiceType $serviceType
     *
     * @return array
     */
    public function getAllCheckedInFromTable(Restaurant $restaurant, $tableNumber, $serviceType = null)
    {
        return $this->createQueryBuilder('c')
            ->where('c.restaurant = :restaurant')
            ->andWhere('c.serviceType = :serviceType')
            ->andWhere('c.tableNumber = :tableNumber')
            ->andWhere('c.checkedOut = :checkedOut')
            ->setParameters(
                [
                    'restaurant'  => $restaurant->getId(),
                    'serviceType' => is_null($serviceType) ? RestaurantServiceType::IN_HOUSE : $serviceType->getId(),
                    'tableNumber' => $tableNumber,
                    'checkedOut'  => false
                ]
            )
            ->orderBy('c.id', 'DESC')
            ->getQuery()->getResult();
    }


    public function isCheckInActual(RestaurantCheckin $checkin, $time = null)
    {
        if ($checkin->isCheckedOut()) {
            return false;
        }
        $time         = $time ? : time();
        $checkOutTime = $checkin->getRestaurant()->getCheckOutTimeInSeconds();

        $result = ($time >= $checkin->getUpdatedAt()) && ($time <= $checkin->getUpdatedAt() + $checkOutTime);

        if (!$result) {
            $query = $this->getEntityManager()->createQuery(
                'SELECT o
                FROM NmotionNmotionBundle:Order o
                WHERE o.restaurant = :restaurantId
                    AND o.user = :userId
                    AND o.serviceType = :serviceType
                    AND o.tableNumber = :tableNumber
                    AND (o.createdAt >= :borderTime OR o.updatedAt >= :borderTime)
                ORDER BY o.id DESC'
            )->setParameters(
                [
                    'restaurantId' => $checkin->getRestaurant()->getId(),
                    'userId'       => $checkin->getUser()->getId(),
                    'serviceType'  => is_null($checkin->getServiceType()) ? null : $checkin->getServiceType()->getId(),
                    'tableNumber'  => $checkin->getTableNumber(),
                    'borderTime'   => $time - $checkOutTime
                ]
            )->setMaxResults(1);

            $orders = $query->getResult();
            $result = !empty($orders);
        }

        return $result;
    }
}
