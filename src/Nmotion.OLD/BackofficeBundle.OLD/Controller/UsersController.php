<?php

namespace Nmotion\BackofficeBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Nmotion\NmotionBundle\Entity\User;
use Nmotion\NmotionBundle\Entity\Repositories\UserRepository;

class UsersController extends BackofficeController
{
    /**
     * @return User
     * @throws AccessDeniedException
     */
    public function getUser()
    {
        $user = parent::getUser();

        if (! $user instanceof User) {
            throw new AccessDeniedException;
        }

        return $user;
    }

    /**
     * GET /users
     *
     * @throws AccessDeniedException
     * @return Response json
     */
    public function getUsersAction()
    {
        $this->setSerializerGroups(['backoffice', 'backoffice.list']);

        if (! $this->isSolutionAdmin()) {
            throw new AccessDeniedException;
        }

        /** @var $repository UserRepository */
        $repository = $this->getDoctrine()->getRepository('NmotionNmotionBundle:User');

        $filters = $this->getRequestFilters();
        $start   = $this->getRequest()->get('start', null);
        $limit   = $this->getRequest()->get('limit', null);

        if (array_key_exists('search', $filters)) {
            $total = $repository->getCountForFindByName($filters['search']);
            $users = $repository->findByName($filters['search'], null, $limit, $start);
        } elseif (array_key_exists('userType', $filters)) {
            $total = $repository->getCountForFindByRole($filters['userType']);
            $users = $repository->findByRole($filters['userType'], null, $limit, $start);
        } else {
            $total = $repository->getCountAllUsers();
            $users = $repository->findBy([], null, $limit, $start);
        }

        return $this->entriesResponse($users, $total);
    }
}
