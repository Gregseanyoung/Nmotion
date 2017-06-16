<?php

namespace Nmotion\ApiBundle\Controller\V2;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Nmotion\NmotionBundle\Controller\BaseRestController;
use Nmotion\NmotionBundle\Controller\FormTrait;
use Nmotion\NmotionBundle\Controller\RestaurantTrait;
use Nmotion\NmotionBundle\Entity\UserDevice;
use Nmotion\NmotionBundle\Entity\RestaurantGuest;

class UserDevicesController extends BaseRestController
{
    /**
     * @param string $deviceIdentity
     *
     * @throws NotFoundHttpException
     * @return UserDevice
     */
    protected function getUserDevice($deviceIdentity)
    {
        $entity = $this->getRepository('UserDevice')->findOneByDeviceIdentity($deviceIdentity);

        /** @var $this RestaurantTrait | \FOS\RestBundle\Controller\FOSRestController */
        if (! $entity instanceof UserDevice) {
            throw new NotFoundHttpException('UserDevice for specified device token is not found');
        }

        return $entity;
    }

    /**
     * @param string $deviceIdentity
     *
     * @return UserDevice
     */
    protected function createUserDevice($deviceIdentity)
    {
        $entity = (new UserDevice())
            ->setDeviceIdentity($deviceIdentity)
            ->setUser($this->getUser());

        /** @var EntityManager $entityManager */
        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($entity);
        $entityManager->flush($entity);

        return $entity;
    }

    /**
     * GET /api/v2/userdevices/{$deviceIdentity}.json
     *
     * @param string $deviceIdentity
     *
     * @return Response
     */
    public function getUserdeviceAction($deviceIdentity)
    {
        $this->setSerializerGroups(['api']);

        try {
            $userDevice = $this->getUserDevice($deviceIdentity);
        } catch (NotFoundHttpException $e) {
            $userDevice = $this->createUserDevice($deviceIdentity);
        }

        return $this->jsonResponseSuccessful('', [$userDevice]);
    }
}
