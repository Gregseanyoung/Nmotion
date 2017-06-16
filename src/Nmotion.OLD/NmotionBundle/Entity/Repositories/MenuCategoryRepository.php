<?php

namespace Nmotion\NmotionBundle\Entity\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Query;
use Nmotion\NmotionBundle\Entity\Restaurant;

class MenuCategoryRepository extends EntityRepository
{
    /**
     * @param Restaurant $restaurant
     * @param integer    $time
     *
     * @return array
     */
    public function getAllMenuCategoriesForRestaurantWithinTimeFrame(Restaurant $restaurant, $time = null)
    {
        $time = $time ?: time();
        $time = ($time + (int)date('Z')) % 86400;

        $dql = '
            SELECT mc
            FROM NmotionNmotionBundle:MenuCategory mc
            WHERE mc.restaurant = :restaurantId
                AND mc.visible = TRUE
                AND
                (
                    (
                        mc.timeFrom < mc.timeTo
                        AND
                        (:time BETWEEN mc.timeFrom AND mc.timeTo)
                    )
                    OR
                    (
                        mc.timeFrom > mc.timeTo
                        AND
                        ((:time BETWEEN 0 AND mc.timeTo) OR (:time BETWEEN mc.timeFrom AND 86400))
                    )
                    OR
                    (
                        mc.timeFrom = mc.timeTo
                    )
                )
            ORDER BY mc.position ASC
        ';

        $query = $this->_em->createQuery($dql);
        $query->setParameter('restaurantId', $restaurant->getId());
        $query->setParameter('time', $time);

        return $query->getResult();
    }
}
