<?php

namespace Nmotion\ApiBundle\Controller\V2;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Nmotion\ApiBundle\Controller\V1 as V1;
use Nmotion\NmotionBundle\Controller\RestaurantTrait;
use Nmotion\NmotionBundle\Controller\FormTrait;
use Nmotion\NmotionBundle\Entity\Meal;
use Nmotion\NmotionBundle\Entity\Order;
use Nmotion\NmotionBundle\Entity\OrderMeal;
use Nmotion\NmotionBundle\Entity\OrderStatus;
use Nmotion\NmotionBundle\Exception\ConflictException;

class OrdersController extends V1\OrdersController
{
    use RestaurantTrait;
    use FormTrait;

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

        // if meal is explicitly unavailable
        if ($meal->getTimeFrom() === null && $meal->getTimeTo() === null) {
            throw new ConflictException($mealId, null, ConflictException::MEAL_NOT_AVAILABLE_MEAL_TIME_UNAVAILABLE);
        }

        $category = $meal->getMenuCategory();

        // if category is explicitly unavailable
        if ($category->getTimeFrom() === null && $category->getTimeTo() === null) {
            throw new ConflictException($mealId, null, ConflictException::MEAL_NOT_AVAILABLE_CATEGORY_TIME_UNAVAILABLE);
        }

        $timeNow = time();
        $midnigth = (int) (new \DateTime('00:00:00'))->format('U');
        $intradayNow = $timeNow - $midnigth;

        if ($meal->getTimeFrom() != 0 || $meal->getTimeTo() != 0) {
            $timeFrom = $meal->getTimeFrom();
            $timeTo   = $meal->getTimeTo();

            if ($timeFrom > $timeTo) {
                // overnight timeframe
                if ($intradayNow > $timeTo && $intradayNow < $timeFrom) {
                    throw new ConflictException(
                        $mealId,
                        null,
                        ConflictException::MEAL_NOT_AVAILABLE_MEAL_TIME_UNAVAILABLE
                    );
                }
            } else {
                // intraday timeframe
                if ($intradayNow < $timeFrom || $intradayNow > $timeTo) {
                    throw new ConflictException(
                        $mealId,
                        null,
                        ConflictException::MEAL_NOT_AVAILABLE_MEAL_TIME_UNAVAILABLE
                    );
                }
            }
        }

        if ($category->getTimeFrom() != 0 || $category->getTimeTo() != 0) {
            $timeFrom = $category->getTimeFrom();
            $timeTo   = $category->getTimeTo();

            if ($timeFrom > $timeTo) {
                // overnight timeframe
                if ($intradayNow > $timeTo && $intradayNow < $timeFrom) {
                    throw new ConflictException(
                        $mealId,
                        null,
                        ConflictException::MEAL_NOT_AVAILABLE_CATEGORY_TIME_UNAVAILABLE
                    );
                }
            } else {
                // intraday timeframe
                if ($intradayNow < $timeFrom || $intradayNow > $timeTo) {
                    throw new ConflictException(
                        $mealId,
                        null,
                        ConflictException::MEAL_NOT_AVAILABLE_CATEGORY_TIME_UNAVAILABLE
                    );
                }
            }
        }
    }

    /**
     * POST /restaurants/{$restaurantId}/orders.json
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
        $order->setTakeawayPickupTime($checkin->getTakeawayPickupTime());

        return $this->processForm($order);
    }
}
