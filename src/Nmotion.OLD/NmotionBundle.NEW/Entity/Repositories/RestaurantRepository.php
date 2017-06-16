<?php

namespace Nmotion\NmotionBundle\Entity\Repositories;

use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\LockMode;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Nmotion\NmotionBundle\Entity\Restaurant;

class RestaurantRepository extends EntityRepository
{
    private $availableAggregatePeriods = ['d', 'w', '2w', 'm', 'y'];

    /**
     * @return Restaurant
     * @inheritDoc
     */
    public function find($id, $lockMode = LockMode::NONE, $lockVersion = null)
    {
        return parent::find($id, $lockMode, $lockVersion);
    }

    /**
     * Function searches nearby restaurants
     *
     * @param string $name partial name is used for search LIKE "%{name}%"
     * @param float  $latitude User location's latitude
     * @param float  $longitude User location's longitude
     * @param float  $radius Search radius in kilometers (default=20, maxValue=1000)
     * @param int    $limit Limit (default=1000, maxValue=1000)
     * @param int    $offset Limit's offset
     *
     * @return array of Restaurant
     */
    public function getNearby($name, $latitude, $longitude, $radius = 20, $limit = 1000, $offset = 0)
    {
        $em   = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare(
            'CALL GEO_KM_NEARBY_RESTAURANTS('
            . ':name, '
            . (float) $latitude . ', '
            . (float) $longitude . ', '
            . (float) $radius . ', '
            . min((int) $limit, 1000) . ', '
            . (int) $offset . ')'
        );
        $stmt->bindValue(':name', $name, \PDO::PARAM_STR);
        $stmt->execute();
        $data  = $stmt->fetchAll(Query::HYDRATE_ARRAY);
        $stmt->closeCursor();

        if (empty($data)) {
            return [];
        }

        $restaurantDistances = [];
        foreach ($data as $row) {
            $restaurantDistances[$row['id']] = (float) $row['distance'];
        }

        $queryBuilder = $em->createQueryBuilder();
        $queryBuilder
            ->select('restaurant')
            ->from('NmotionNmotionBundle:Restaurant', 'restaurant')
            ->where('restaurant.id IN (' . join(', ', array_keys($restaurantDistances)) . ')');

        $restaurants = [];
        foreach ($queryBuilder->getQuery()->getResult() as $restaurant) {
            /** @var Restaurant $restaurant */
            $restaurantId = $restaurant->getId();
            if (isset($restaurantDistances[$restaurantId])) {
                $restaurant->setDistance($restaurantDistances[$restaurantId]);
                $restaurants[$restaurantId] = $restaurant;
            }
        }

        $result = [];
        foreach ($restaurantDistances as $restaurantId => $distance) {
            if (isset($restaurants[$restaurantId])) {
                $result[] = $restaurants[$restaurantId];
            }
        }

        return $result;
    }

    /**
     * Return total number of restaurants
     *
     * @return int
     */
    public function getCountAllRestaurants()
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findByNameAndPostalCode($name, $postalCode)
    {
        return $this->createQueryBuilder('r')
            ->select('r')
            ->join('r.address', 'a')
            ->where('r.name = :name')
            ->andWhere('a.postalCode = :postalCode')
            ->setParameter('name', $name)
            ->setParameter('postalCode', $postalCode)
            ->getQuery()
            ->getResult();
    }

    public function findByIdOrNameOrAdminEmail($queryText, array $orderBy = null, $limit = null, $offset = null)
    {
        $parameters = [
            '%' . $queryText . '%',
            '%' . $queryText . '%'
        ];

        $query = $this->createQueryBuilder('r')
            ->select('r')
            ->join('r.adminUser', 'u')
            ->where("r.name LIKE ?0")
            ->orWhere("u.email LIKE ?1");

        if (is_numeric($queryText)) {
            $parameters[] = $queryText;
            $query->orWhere("r.id = ?2");
        }

        $query->setParameters($parameters)
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        if ($orderBy) {
            foreach ($orderBy as $sort => $order) {
                $query->addOrderBy('r.' . $sort, $order);
            }
        }

        return $query->getQuery()
            ->getResult();
    }

    public function getCountForFindByIdOrNameOrAdminEmail($queryText)
    {
        $parameters = [
            '%' . $queryText . '%',
            '%' . $queryText . '%'
        ];

        $query = $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->join('r.adminUser', 'u')
            ->where("r.name LIKE ?0")
            ->orWhere("u.email LIKE ?1");

        if (is_numeric($queryText)) {
            $parameters[] = $queryText;
            $query->orWhere("r.id = ?2");
        }

        return $query->setParameters($parameters)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * get payments aggregated data for frestaurant
     *
     * @param $restaurantId
     * @param $period
     * @return array
     * @throws \Nmotion\NmotionBundle\Exception\PreconditionFailedException
     */
    public function getRestaurantIncome($restaurantId, $period)
    {
        if (!in_array($period, $this->availableAggregatePeriods)) {
            throw new \Nmotion\NmotionBundle\Exception\PreconditionFailedException('Unknown aggregation period!');
        }

        $em   = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare('CALL AGGREGATE_PAYMENTS(:period, :restaurant)');
        $stmt->bindValue(':period', $period, \PDO::PARAM_STR);
        $stmt->bindValue(':restaurant', $restaurantId, \PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(Query::HYDRATE_ARRAY);
        $stmt->closeCursor();

        if (empty($data)) {
            return [];
        }

        $data = array_reverse($data);

        $i = 1;
        foreach ($data as &$periodData) {
            $periodData = $this->calcPeriodDates($period, $periodData);
            $periodData['SN'] = $i++;
            $periodData['orderIds'] = explode(',', $periodData['orderIds']);
        }

        return $data;
    }

    /**
     * calculate dateFrom and dateTo for aggregated income data
     *
     * @param $period
     * @param $periodData
     * @return mixed
     */
    protected function calcPeriodDates($period, $periodData)
    {
        switch ($period) {
            case 'y':
                $periodData['dateFrom'] = strtotime($periodData['year'] . '-01-01');
                $periodData['dateTo'] = strtotime($periodData['year'] . '-12-31');
                break;
            case 'm':
                $date = new \DateTime($periodData['year'] . '-' . $periodData['month'] . '-01');
                $periodData['dateFrom'] = $date->format('U');
                $periodData['dateTo'] = $date->modify('last day of this month')->format('U');
                break;
            case 'w':
            case '2w':
                $date = new \DateTime($periodData['year'] . '-01-01');

                if ($period == 'w') {
                    $week = $weekDiff = (int)$periodData['week'] - 1;
                } else {
                    $week = $weekDiff = (int)$periodData['2week'] * 2 - 2;
                }

                if ($date->format('W') != 53) {
                    $weekDiff--;
                }

                if ($week != 1) {
                    $nextWeekIn = 8 - $date->format('N');
                    $date->modify('+' . $nextWeekIn . ' day')
                        ->modify('+' . $weekDiff . ' week');
                }

                $periodData['dateFrom'] = $date->format('U');

                if ($week == 1) {
                    $nextWeekIn = 8 - $date->format('N');
                    $date->modify('+' . $nextWeekIn . ' day');
                }

                if ($period == '2w') {
                    $date->modify('+1 week');
                }

                $periodData['dateTo'] = $date->modify('+6 day')->format('U');
                break;
            default:
                $periodData['dateFrom'] = strtotime(
                    $periodData['year'] . '-' . $periodData['month'] . '-' . $periodData['day'] . ' 00:00:00'
                );
                $periodData['dateTo'] = strtotime(
                    $periodData['year'] . '-' . $periodData['month'] . '-' . $periodData['day'] . ' 23:59:59'
                );
        }

        unset($periodData['year']);
        unset($periodData['month']);
        unset($periodData['week']);
        unset($periodData['day']);
        if ($period == '2w') {
            unset($periodData['2week']);
        }

        return $periodData;
    }
}
