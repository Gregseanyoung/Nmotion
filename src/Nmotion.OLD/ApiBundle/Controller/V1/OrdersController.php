<?php

namespace Nmotion\ApiBundle\Controller\V1;

use FOS\Rest\Util\Codes;

use Nmotion\NmotionBundle\Controller\RestaurantTrait;
use Nmotion\NmotionBundle\Controller\FormTrait;
use Nmotion\NmotionBundle\Controller\BaseRestController;
use Nmotion\NmotionBundle\Entity\Config;
use Nmotion\NmotionBundle\Entity\Order;
use Nmotion\NmotionBundle\Entity\OrderMeal;
use Nmotion\NmotionBundle\Entity\Meal;
use Nmotion\NmotionBundle\Entity\OrderStatus;
use Nmotion\NmotionBundle\Exception\PreconditionFailedException;
use Nmotion\NmotionBundle\Exception\ConflictException;
use Nmotion\NmotionBundle\Form\OrderType as OrderTypeForm;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class OrdersController extends BaseRestController
{
    use RestaurantTrait;
    use FormTrait;

    protected function processForm(Order $order)
    {
        $action = $order->getId() ? 'edit' : 'new';
        $request = $this->getRequest();

        $statusCode = $action == 'new' ? Codes::HTTP_CREATED : Codes::HTTP_OK;

        $form = $this->createForm(new OrderTypeForm(), $order);
        if (!$request->request->has('tips')) {
            $form->remove('tips');
        }
        
        if (!$request->request->has('pushToken')) {
            $form->remove('pushToken');
        }
        
        if (!$request->request->has('contactPhoneNumber')) {
            $form->remove('contactPhoneNumber');
        }
        
        $form->bind($request);

        if ($form->isValid()) {
            // spike-fix for field default value
            if ($order->getTakeawayPickupTime() === null) {
                $order->setTakeawayPickupTime(0);
            }

            $configRepository = $this->getRepository('Config');
            $configSalesTax   = $configRepository->findOneBy(['name' => 'sales_tax']);
            if (!$configSalesTax instanceof Config) {
                throw new \RuntimeException('Config parameter "sales_tax" not found');
            }

            $salesTaxPercent = (float) $configSalesTax->getValue();
            if ($salesTaxPercent < 0) {
                throw new \RuntimeException('Config parameter "sales_tax" can not be less than 0');
            }

            $order->setTaxPercent($salesTaxPercent);

            $configDiscount= $configRepository->findOneBy(['name' => 'nmotion_discount']);
            if (!$configDiscount instanceof Config) {
                throw new \RuntimeException('Config parameter "nmotion_discount" not found');
            }

            $discountPercent = (float) $configDiscount->getValue();
            if ($discountPercent < 0 || $discountPercent > 100) {
                throw new \RuntimeException('Config parameter "nmotion_discount" must be in range [0..100]');
            }

            $order->setDiscountPercent($discountPercent);

            foreach ($order->getOrderMealsAsArray() as $orderMeal) {
                $this->checkMealAvailableForOrdering($orderMeal);
            }

            $this->getDoctrine()->getManager()->persist($order);
            $this->getDoctrine()->getManager()->flush();

            return $this->jsonResponseSuccessful('', [$order], $statusCode);
        }

        return $this->jsonResponseFailed(
            'Validation failed',
            [$this->getFormErrorMessages($form)],
            Codes::HTTP_PRECONDITION_FAILED
        );
    }

    /**
     * Check orderMeal for meal ordering availability
     *
     * @param \Nmotion\NmotionBundle\Entity\OrderMeal $orderMeal
     *
     * @throws ConflictException
     * @throws NotFoundHttpException
     */
    protected function checkMealAvailableForOrdering(OrderMeal $orderMeal)
    {
        $mealId = $orderMeal->getMeal()->getId();
        $meal = $this->getRepository('Meal')->find($mealId);
        if (!$meal instanceof Meal) {
            throw new NotFoundHttpException('Meal ' . $mealId . ' not found');
        }

        if (!$meal->isVisible()) {
            throw new ConflictException($mealId, null, ConflictException::MEAL_NOT_AVAILABLE_MEAL_NOT_VISIBLE);
        }
        if (!$meal->getMenuCategory()->isVisible()) {
            throw new ConflictException($mealId, null, ConflictException::MEAL_NOT_AVAILABLE_CATEGORY_NOT_VISIBLE);
        }

        $today = new \DateTime('00:00:00');
        $now = time() - (int)$today->format('U');

        if (
            ($meal->getTimeFrom() && $now < $meal->getTimeFrom())
            || ($meal->getTimeTo() && $now > $meal->getTimeTo())
        ) {
            throw new ConflictException($mealId, null, ConflictException::MEAL_NOT_AVAILABLE_MEAL_TIME_UNAVAILABLE);
        }

        if (
            ($meal->getMenuCategory()->getTimeFrom() && $now < $meal->getMenuCategory()->getTimeFrom())
            || ($meal->getMenuCategory()->getTimeTo() && $now > $meal->getMenuCategory()->getTimeTo())
        ) {
            throw new ConflictException($mealId, null, ConflictException::MEAL_NOT_AVAILABLE_CATEGORY_TIME_UNAVAILABLE);
        }
    }

    /**
     * @param Request $request
     *
     * @return array of Order
     * @throws PreconditionFailedException
     * @throws NotFoundHttpException
     */
    private function getOrdersForLink(Request $request)
    {
        $result = [];

        if (!$request->attributes->has('links')) {
            throw new PreconditionFailedException(
                $this->get('translator')->trans('order.link.action.headersLinksNotFound')
            );
        }

        /** @var $order Order */
        foreach ($request->attributes->get('links') as $order) {
            if (!$order instanceof Order) {
                throw new NotFoundHttpException(
                    $this->get('translator')->trans('order.link.action.invalidResource')
                );
            }

            $result[] = $order;
        }

        return $result;
    }

    /**
     * POST /api/v1/restaurants/{$restaurantId}/orders.json
     *
     * @return Response json
     */
    public function postRestaurantsOrdersAction($restaurantId)
    {
        $this->setSerializerGroups(['api.orders.entity']);

        $user        = $this->getUser();
        $checkin     = $this->getUserLastCheckinInRestaurant($user->getId(), (int) $restaurantId);
        $orderStatus = $this->getRepository('OrderStatus')->find(OrderStatus::NEW_ORDER);

        $order = new Order();
        $order->setUser($user);
        $order->setRestaurant($checkin->getRestaurant());
        $order->setServiceType($checkin->getServiceType());
        $order->setTableNumber($checkin->getTableNumber());
        $order->setOrderStatus($orderStatus);

        return $this->processForm($order);
    }

    /**
     * PUT /api/v1/orders/{$orderId}.json
     *
     * @return Response json
     */
    public function putOrderAction($orderId)
    {
        $this->setSerializerGroups(['api.orders.entity']);

        $user  = $this->getUser();
        $order = $this->getRepository('Order')->getById($orderId);

        if (!$order) {
            throw new NotFoundHttpException($this->get('translator')->trans('order.entry.notFound'));
        }

        if ($order->getUser() != $user) {
            throw new AccessDeniedHttpException($this->get('translator')->trans('order.entry.update.denied'));
        }

        $this->getUserLastCheckinInRestaurant($user->getId(), $order->getRestaurant()->getId());

        $this->checkOrderEditConditions($order);

        return $this->processForm($order);
    }

    /**
     * PATCH /api/v1/orders/{$orderId}.json
     *
     * @return Response json
     */
    public function patchOrderAction($orderId)
    {
        $this->setSerializerGroups(['api.orders.entity']);

        $order = $this->getRepository('Order')->getById($orderId);

        if (!$order) {
            throw new NotFoundHttpException(
                $this->get('translator')->trans('order.entry.notFound')
            );
        }

        if ($order->getUser() != $this->getUser()) {
            throw new AccessDeniedHttpException(
                $this->get('translator')->trans('order.entry.update.denied')
            );
        }

        $this->checkOrderEditConditions($order);

        $orderStatusId = $this->getRequest()->get('status');

        if (!$order->canGuestChangeStatusTo($orderStatusId)) {
            throw new AccessDeniedHttpException(
                $this->get('translator')->trans(
                    'order.entry.patch.status.denied',
                    [
                        '{{ statusFrom }}' => $order->getOrderStatus()->getId(),
                        '{{ statusTo }}'   => $orderStatusId
                    ]
                )
            );
        }

        $order->setOrderStatus($this->getRepository('OrderStatus')->find($orderStatusId));

        $this->getDoctrine()->getManager()->persist($order);
        $this->getDoctrine()->getManager()->flush();

        return $this->jsonResponseSuccessful('', [$order], Codes::HTTP_OK);
    }

    private function checkOrderEditConditions(Order $order)
    {
        $master = $order->getMaster();
        $masterStatusId = !empty($master) ? $master->getOrderStatus()->getId() : false;

        if ($masterStatusId && $masterStatusId == OrderStatus::PENDING_PAYMENT) {
            throw new PreconditionFailedException(
                $this->get('translator')->trans('order.entry.update.orderIsBeingPaidByOther'),
                null,
                PreconditionFailedException::ORDER_PAYING_BY_OTHER
            );
        }

        if ($masterStatusId && in_array($masterStatusId, [OrderStatus::PAID, OrderStatus::SENT_TO_PRINTER])) {
            throw new PreconditionFailedException(
                $this->get('translator')->trans('order.entry.update.orderHasBeenPaidByOther'),
                null,
                PreconditionFailedException::ORDER_PAID_BY_OTHER
            );
        }
    }

    /**
     * LINK /api/v1/orders/{$orderId}.json
     *
     * @param $orderId
     *
     * @return Response json
     */
    public function linkOrderAction($orderId, Request $request)
    {
        $this->setSerializerGroups(['api.orders.entity']);

        $userOrder = $this->getUserOrder($this->getUser()->getId(), (int) $orderId);

        if ($userOrder->getMaster()) {
            throw new ConflictException(
                $this->get('translator')->trans('order.link.action.resourceAlreadyHasOwnMaster')
            );
        }

        $linkOrders = $this->getOrdersForLink($request);

        $result = [];

        /** @var $order Order */
        foreach ($linkOrders as $order) {
            if ($order->getMaster() && ($order->getMaster() != $userOrder)) {
                throw new ConflictException(
                    $this->get('translator')->trans('order.link.action.slaveAlreadyHasMaster')
                );
            }

            if (!$order->isInNewStatus()) {
                throw new ConflictException(
                    $this->get('translator')->trans('order.link.action.slaveNotLinkableOrderStatus')
                );
            }

            $result[] = $order;
        }

        foreach ($result as $order) {
            $order->setMaster($userOrder);
            $this->getDoctrine()->getManager()->persist($order);
        }

        $this->getDoctrine()->getManager()->flush();

        return $this->jsonResponseSuccessful('', [$userOrder]);
    }

    /**
     * UNLINK /api/v1/orders/{$orderId}.json
     *
     * @param $orderId
     *
     * @return Response json
     */
    public function unlinkOrderAction($orderId, Request $request)
    {
        $this->setSerializerGroups(['api.orders.entity']);

        $userOrder = $this->getUserOrder($this->getUser()->getId(), (int) $orderId);

        $linkOrders = $this->getOrdersForLink($request);

        $result = [];

        /** @var $order Order */
        foreach ($linkOrders as $order) {
            if (!$order->getMaster() || ($order->getMaster() != $userOrder)) {
                continue ;
            }

            if (!$order->isInNewStatus()) {
                throw new ConflictException(
                    $this->get('translator')->trans('order.unlink.action.slaveNotUnlinkableOrderStatus')
                );
            }

            $result[] = $order;
        }

        foreach ($result as $order) {
            $order->setMaster(null);
            $this->getDoctrine()->getManager()->persist($order);
        }

        $this->getDoctrine()->getManager()->flush();

        return $this->jsonResponseSuccessful('', [$userOrder]);
    }

    /**
     * GET /api/v1/users/me/orders.json
     *
     * @param $user
     *
     * @return Response json
     */
    public function getUserOrdersAction($user = 'me')
    {
        $request = $this->getRequest();
        $this->setSerializerGroups(['api.list']);

        $start = $this->getRequest()->get('start', 0);
        $limit = $this->getRequest()->get('limit', 100);

        $allowedSortProperties = [
            'tableNumber',
            'productTotal',
            'discount',
            'tips',
            'orderTotal',
            'createdAt',
            'updatedAt'
        ];

        $orderBy = [];

        $clientSort = $request->get('sort') ? : [];
        if (is_array($clientSort) && !empty($clientSort)) {
            foreach ($clientSort as $property => $sortOrder) {
                if (in_array($property, $allowedSortProperties)) {
                    $orderBy[$property] = (strtoupper($sortOrder) === 'DESC') ? 'DESC' : 'ASC';
                }
            }
        }

        if (!isset($orderBy['updatedAt'])) {
            $orderBy['updatedAt'] = 'DESC';
        }

        if ($user == 'me') {
            $userId = $this->getUser()->getId();
        } else {
            $userId = (int) $user;
        }

        $orders = $this->getUserOrders($userId, $orderBy, $start, $limit);

        return $this->jsonResponseSuccessful('', $orders);
    }

    /**
     * GET /api/v1/orders/{$orderId}.json
     *
     * @param $orderId
     * @return Response json
     */
    public function getOrderAction($orderId)
    {
        $this->setSerializerGroups(['api.orders.entity']);

        $userId = $this->getUser()->getId();

        $order = null;

        try {
            $order = $this->getUserOrder($userId, (int)$orderId);
        } catch (NotFoundHttpException $exc) {
            $userCheckin    = $this->getRepository('RestaurantCheckin')->getUserActiveCheckin($userId);
            $newTableOrders = $this->getTableOrders($userCheckin);
            /** @var $tableOrder Order */
            foreach ($newTableOrders as $tableOrder) {
                if ($tableOrder->getId() == $orderId) {
                    $order = $tableOrder;
                    break;
                }
            }

            if (!$order) {
                throw $exc;
            }
        }

        return $this->jsonResponseSuccessful('', [$order]);
    }

    /**
     * POST /api/v1/orders/{$orderId}/sendtoemail.json
     *
     * @param $orderId
     *
     * @return Response json
     */
    public function postOrderSendtoemailAction($orderId)
    {
        $user = $this->getUser();
        $order = $this->getUserOrder($user->getId(), (int)$orderId);

        // send confirmation email to user that account was created
        $message = \Swift_Message::newInstance()
            ->setSubject('Your order #' . $order->getId())
            ->setFrom($this->container->getParameter('mailer_user'))
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    'NmotionApiBundle:Order:email_order_send_to_user.html.twig',
                    ['user' => $user, 'order' => $order]
                ),
                'text/html'
            );

        // actual send
        $this->get('mailer')->send($message);

        return $this->jsonResponseSuccessful();
    }

    /**
     * GET /api/v1/restaurants/{restaurantId}/checkin/orders
     *
     * @param $restaurantId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getRestaurantCheckinOrdersAction($restaurantId)
    {
        $this->setSerializerGroups(['api.list']);

        $user    = $this->getUser();
        $checkin = $this->getUserLastCheckinInRestaurant($user->getId(), (int) $restaurantId);
        $orders  = $this->getTableOrders($checkin);

        $url = ''; // $this->getRequest()->getSchemeAndHttpHost();
        /** @var $order Order */
        foreach ($orders as $order) {
            $order->setResourceUrl($url . '/api/v1/orders/' . $order->getId() . '.json');
        }

        return $this->jsonResponseSuccessful('', $orders);
    }
}
