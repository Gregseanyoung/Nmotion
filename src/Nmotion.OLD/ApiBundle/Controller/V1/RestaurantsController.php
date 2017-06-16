<?php

namespace Nmotion\ApiBundle\Controller\V1;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\Rest\Util\Codes;

use Nmotion\NmotionBundle\Controller\RestaurantTrait;
use Nmotion\NmotionBundle\Controller\BaseRestController;
use Nmotion\NmotionBundle\Entity\RestaurantCheckin;
use Nmotion\NmotionBundle\Entity\RestaurantServiceType;
use Nmotion\NmotionBundle\Entity\OrderStatus;
use Nmotion\NmotionBundle\Exception\NotModifiedException;
use Nmotion\NmotionBundle\Exception\ConflictException;
use Nmotion\NmotionBundle\Exception\PreconditionFailedException;

class RestaurantsController extends BaseRestController
{
    use RestaurantTrait;

    protected function updateCheckoutOrders(RestaurantCheckin $checkin)
    {
        $orderRepository = $this->getRepository('Order');

        $orders = $orderRepository->findBy(
            [
            'user' => $checkin->getUser()->getId(),
            'tableNumber' => $checkin->getTableNumber(),
            'restaurant' => $checkin->getRestaurant()->getId(),
            'orderStatus' => [OrderStatus::NEW_ORDER, OrderStatus::PENDING_PAYMENT]
            ]
        );

        foreach ($orders as $order) {
            $orderRepository->setOrderStatus($order, OrderStatus::CANCELLED);
        }
    }

    /**
     * GET /restaurants
     *
     * @throws AccessDeniedException
     * @return Response json
     */
    public function getRestaurantsAction()
    {
        $this->setSerializerGroups('api');

        if ($this->get('security.context')->isGranted('ROLE_SOLUTION_ADMIN')) {
            $restaurants = $this->getRepository()->findAll();
        } elseif ($this->get('security.context')->isGranted('ROLE_RESTAURANT_ADMIN')) {
            $user        = $this->getUser();
            $restaurants = array($this->getRepository()->findOneBy(['adminUser' => $user]));
        } else {
            throw new AccessDeniedException;
        }

        return $this->jsonResponseSuccessful('', $restaurants);
    }

    /**
     * @param  int $id
     *
     * @return int json
     */
    public function getRestaurantAction($id)
    {
        $this->setSerializerGroups('api');

        $restaurant = $this->getRestaurant($id);

        return $this->jsonResponseSuccessful('', [$restaurant]);
    }

    /**
     * Pre-conditions:
     * - Authenticated user
     * - Restaurant must be open
     * - Table is provided
     * - Table parameter is of type integer
     * - Table must be free
     *
     * @param int $restaurantId
     *
     * @return Response json
     * @throws PreconditionFailedException
     */
    public function postRestaurantCheckinAction($restaurantId)
    {
        $this->setSerializerGroups('api');

        $user        = $this->getUser();
        $restaurant  = $this->getRestaurant($restaurantId, true);
        $tableNumber = $this->getRequest()->get('table');
        $fbRestaurantCheckin = $this->getRequest()->get('fbRestaurantCheckin');

        if (!$tableNumber) {
            throw new PreconditionFailedException('Parameter "table" is required.');
        }

        if (!is_numeric($tableNumber)) {
            throw new PreconditionFailedException('Parameter "table" must be of type integer.');
        }

        if (!$restaurant->isOpen()) {
            throw new PreconditionFailedException('Restaurant is closed.');
        }

        $serviceType = $this->getRepository('RestaurantServiceType')->find(RestaurantServiceType::IN_HOUSE);

        $checkinRepository = $this->getRepository('RestaurantCheckin');
        $tableCheckins     = $checkinRepository->getAllCheckedInFromTable($restaurant, $tableNumber, $serviceType);

        if (!empty($tableCheckins)) {
            $hasActualCheckin = false;
            $time             = time();
            /** @var $tableCheckin RestaurantCheckin */
            foreach ($tableCheckins as $tableCheckin) {
                if ($checkinRepository->isCheckInActual($tableCheckin, $time)) {
                    if ($tableCheckin->getUser() === $user) {
                        $tableCheckin->setUpdatedAt(time());
                        $this->getDoctrine()->getManager()->persist($tableCheckin);
                        $this->getDoctrine()->getManager()->flush();

                        if ($fbRestaurantCheckin) {
                            $this->checkinToRestaurantLocation($restaurant);
                        }

                        return $this->jsonResponseSuccessful('', [$tableCheckin], Codes::HTTP_OK);
                    }
                    $hasActualCheckin = true;
                    // break; - import break should be absent, because we compare all checkin->user with user
                }
            }

            if ($hasActualCheckin) {
                if (!$this->getRequest()->get('force')) {
                    // table is not empty, you can just force JOIN;
                    throw new ConflictException(
                        $this->get('translator')->trans(
                            'restaurant.checkin.tableNotEmptyWonnaForceJoin',
                            ['{{ tableNumber }}' => $tableNumber]
                        )
                    );
                }
            } else {
                if (!$this->getRequest()->get('empty')) {
                    // does table empty? system checks out all and check me in;
                    throw new PreconditionFailedException(
                        $this->get('translator')->trans(
                            'restaurant.checkin.submitCheckinIfTableEmpty',
                            ['{{ tableNumber }}' => $tableNumber]
                        ),
                        null,
                        RestaurantCheckin::TABLE_MAYBE_EMPTY
                    );
                } else {
                    foreach ($tableCheckins as $tableCheckin) {
                        $tableCheckin->setCheckedOut(true);
                        $this->updateCheckoutOrders($tableCheckin);
                    }

                }
            }
        }

        $checkin = new RestaurantCheckin($restaurant, $user);
        $checkin->setServiceType($serviceType);
        $checkin->setTableNumber($tableNumber);
        $this->getDoctrine()->getManager()->persist($checkin);
        $this->getDoctrine()->getManager()->flush();

        if ($fbRestaurantCheckin) {
            $this->checkinToRestaurantLocation($restaurant);
        }

        return $this->jsonResponseSuccessful('', [$checkin], Codes::HTTP_CREATED);
    }

    /**
     * Pre-conditions:
     * - Authenticated user
     *
     * @param int $restaurantId
     *
     * @return Response json
     * @throws NotModifiedException
     */
    public function postRestaurantCheckoutAction($restaurantId)
    {
        $this->setSerializerGroups('api');

        $user       = $this->getUser();
        $restaurant = $this->getRestaurant($restaurantId);

        /** @var $activeCheckins RestaurantCheckin[] */
        $activeCheckins = $this->getRepository('RestaurantCheckin')->findBy(
            [
                'user'       => $user,
                'restaurant' => $restaurant,
                'checkedOut' => false
            ]
        );

        if (! $activeCheckins) {
            throw new NotModifiedException('there is no active check-ins for current user and provided restaurant');
        }

        foreach ($activeCheckins as $checkin) {
            $checkin->setCheckedOut(true);
            $this->updateCheckoutOrders($checkin);
        }

        $this->getDoctrine()->getManager()->flush();

        return $this->jsonResponseSuccessful();
    }
}
