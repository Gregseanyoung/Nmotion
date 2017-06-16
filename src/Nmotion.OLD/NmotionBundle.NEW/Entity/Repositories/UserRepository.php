<?php

namespace Nmotion\NmotionBundle\Entity\Repositories;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class UserRepository extends EntityRepository
{
    /**
     * Return total number of users
     *
     * @return int
     */
    public function getCountAllUsers()
    {
        return (int) $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Find user with particular role
     *
     * @param string $role
     * @param array  $orderBy
     * @param int    $limit
     * @param int    $offset
     *
     * @return ArrayCollection
     */
    public function findByRole($role, array $orderBy = null, $limit = null, $offset = null)
    {
        $query = $this->createQueryBuilder('u')
            ->where('u INSTANCE OF ?0')
            ->setParameters([$role])
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        if ($orderBy) {
            foreach ($orderBy as $sort => $order) {
                $query->addOrderBy('u.' . $sort, $order);
            }
        }

        return $query->getQuery()
            ->getResult();
    }

    /**
     * Return total number of users with particular role
     *
     * @param string $role
     *
     * @return int
     */
    public function getCountForFindByRole($role)
    {
        return (int) $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u INSTANCE OF ?0')
            ->setParameters([$role])
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findByName($queryText, array $orderBy = null, $limit = null, $offset = null)
    {
        $query = $this->createQueryBuilder('u')
            ->select('u')
            ->where('CONCAT(u.firstName, u.lastName) LIKE ?0')
            ->setParameters(['%' . str_replace(' ', '', $queryText) . '%'])
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        if ($orderBy) {
            foreach ($orderBy as $sort => $order) {
                $query->addOrderBy('u.' . $sort, $order);
            }
        }

        return $query->getQuery()
            ->getResult();
    }

    public function findByUserName($userName) {
        $query = $this->createQueryBuilder('u')
            ->select('u')
            ->where('u.username = ?0')
            ->setParameters($userName)
            ->setMaxResults(1);

        return $query->getQuery()
            ->getResult();
    }

    public function getCountForFindByName($queryText)
    {
        $query = $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('CONCAT(u.firstName, u.lastName) LIKE ?0')
            ->setParameters(['%' . str_replace(' ', '', $queryText) . '%']);

        return $query->getQuery()
            ->getSingleScalarResult();
    }
}
