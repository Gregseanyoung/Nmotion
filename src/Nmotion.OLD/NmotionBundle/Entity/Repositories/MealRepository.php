<?php

namespace Nmotion\NmotionBundle\Entity\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Query;
use Nmotion\NmotionBundle\Entity\MenuCategory;

class MealRepository extends EntityRepository
{
    /**
     * @param MenuCategory $category
     * @param integer      $time
     *
     * @return array
     */
    public function getAllMenuMealsForCategoryWithinTimeFrame(MenuCategory $category, $time = null)
    {
        $time = $time ?: time();
        $time = ($time + (int)date('Z')) % 86400;

        $dql = '
            SELECT mm
            FROM NmotionNmotionBundle:Meal mm
            WHERE mm.menuCategory = :categoryId
                AND mm.visible = TRUE
                AND
                (
                    (
                        mm.timeFrom < mm.timeTo
                        AND
                        (:time BETWEEN mm.timeFrom AND mm.timeTo)
                    )
                    OR
                    (
                        mm.timeFrom > mm.timeTo
                        AND
                        ((:time BETWEEN 0 AND mm.timeTo) OR (:time BETWEEN mm.timeFrom AND 86400))
                    )
                    OR
                    (
                        mm.timeFrom = mm.timeTo
                    )
                )
            ORDER BY mm.position ASC
        ';

        $query = $this->_em->createQuery($dql);
        $query->setParameter('categoryId', $category->getId());
        $query->setParameter('time', $time);

        return $query->getResult();
    }
}
