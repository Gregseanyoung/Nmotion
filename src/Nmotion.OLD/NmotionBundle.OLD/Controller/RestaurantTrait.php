<?php

namespace Nmotion\NmotionBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Nmotion\NmotionBundle\Entity\Meal;
use Nmotion\NmotionBundle\Entity\MealExtraIngredient;
use Nmotion\NmotionBundle\Entity\MealOption;
use Nmotion\NmotionBundle\Entity\MenuCategory;
use Nmotion\NmotionBundle\Entity\Order;
use Nmotion\NmotionBundle\Entity\OrderStatus;
use Nmotion\NmotionBundle\Entity\Repositories\MealRepository;
use Nmotion\NmotionBundle\Entity\Repositories\MenuCategoryRepository;
use Nmotion\NmotionBundle\Entity\Repositories\OrderRepository;
use Nmotion\NmotionBundle\Entity\Repositories\RestaurantCheckinRepository;
use Nmotion\NmotionBundle\Entity\Repositories\RestaurantRepository;
use Nmotion\NmotionBundle\Entity\Restaurant;
use Nmotion\NmotionBundle\Entity\RestaurantCheckin;
use Nmotion\NmotionBundle\Entity\User;
use Nmotion\NmotionBundle\Exception\PreconditionFailedException;
use Nmotion\NmotionBundle\Exception\RestaurantCheckinExpiredException;

trait RestaurantTrait
{
    /**
     * @param string $repositoryName
     *
     * @return RestaurantRepository|MenuCategoryRepository|MealRepository|RestaurantCheckinRepository|OrderRepository
     */
    protected function getRepository($repositoryName = 'Restaurant')
    {
        /** @var $this RestaurantTrait|\FOS\RestBundle\Controller\FOSRestController */
        return $this->getDoctrine()->getRepository('NmotionNmotionBundle:' . $repositoryName);
    }

    /**
     * @param int  $restaurantId
     * @param bool $onlyVisible
     *
     * @return Restaurant
     * @throws NotFoundHttpException
     */
    protected function getRestaurant($restaurantId, $onlyVisible = false)
    {
        /** @var $this RestaurantTrait|\FOS\RestBundle\Controller\FOSRestController */
        $restaurant = $this->getRepository()->find($restaurantId);

        if (!($restaurant instanceof Restaurant) || ($onlyVisible && !$restaurant->isVisible())) {
            throw new NotFoundHttpException('Restaurant not found.');
        }

        return $restaurant;
    }

    /**
     * @param int            $id
     * @param int|Restaurant $restaurant (optional)
     *
     * @throws \InvalidArgumentException
     * @throws NotFoundHttpException
     *
     * @return MenuCategory
     */
    protected function getMenuCategory($id, $restaurant = null)
    {
        /** @var $this RestaurantTrait|\FOS\RestBundle\Controller\FOSRestController */

        if ($restaurant !== null) {
            if ((! is_numeric($restaurant)) && (! $restaurant instanceof Restaurant)) {
                throw new \InvalidArgumentException(
                    'Argument 2 passed to MenuCategoryController::getMenuCategory() must be numeric'
                    . ' or an instance of Restaurant, ' . gettype($restaurant) . ' given'
                );
            }
        }

        $categoryRepository = $this->getRepository('MenuCategory');

        if ($restaurant !== null) {
            $menuCategory = $categoryRepository->findOneBy(['id' => $id, 'restaurant' => $restaurant]);
        } else {
            $menuCategory = $categoryRepository->find($id);
        }

        if (! $menuCategory instanceof MenuCategory) {
            throw new NotFoundHttpException('Menu category not found for given restaurant');
        }

        return $menuCategory;
    }

    /**
     * @param int                        $id
     * @param int|MenuCategory $menuCategory (optional)
     * @param int|Restaurant             $restaurant (optional)
     *
     * @throws \InvalidArgumentException
     * @throws NotFoundHttpException
     *
     * @return Meal
     */
    protected function getMeal($id, $menuCategory = null, $restaurant = null)
    {
        if ($restaurant !== null) {
            if ((! is_numeric($restaurant)) && (! $restaurant instanceof Restaurant)) {
                throw new \InvalidArgumentException(
                    'Argument 3 passed to MealController::getMeal() must be numeric'
                    . ' or an instance of Restaurant, ' . gettype($restaurant) . ' given'
                );
            }
        }

        if ($menuCategory !== null) {
            if ((! is_numeric($menuCategory)) && (! $menuCategory instanceof MenuCategory)) {
                throw new \InvalidArgumentException(
                    'Argument 2 passed to MealController::getMeal() must be numeric'
                    . ' or an instance of MenuCategory, ' . gettype($menuCategory) . ' given'
                );
            }
        }

        $mealRepository = $this->getRepository('Meal');

        if ($restaurant !== null && $menuCategory !== null) {
            $meal = $mealRepository->findOneBy(
                ['id' => $id, 'menuCategory' => $menuCategory, 'restaurant' => $restaurant]
            );
        } else {
            $meal = $mealRepository->find($id);
        }

        if (! $meal instanceof Meal) {
            throw new NotFoundHttpException('Meal is not found');
        }

        return $meal;
    }

    /**
     * @param int $id
     *
     * @throws NotFoundHttpException
     *
     * @return MealOption
     */
    protected function getMealOption($id)
    {
        $mealOption = $this->getRepository('MealOption')->find($id);

        if (! $mealOption instanceof MealOption) {
            throw new NotFoundHttpException('Meal option not found for given id');
        }

        return $mealOption;
    }

    private function checkRestaurantAdminAccess($userId, $restaurantId)
    {
        $restaurant = $this->getRepository()->findOneBy(['adminUser' => $userId, 'id' => $restaurantId]);
        if (! $restaurant instanceof Restaurant) {
            throw new AccessDeniedException;
        }
    }

    /**
     * @param int $id
     *
     * @throws NotFoundHttpException
     *
     * @return MealExtraIngredient
     */
    protected function getMealExtraIngredient($id)
    {
        $mealExtraIngredient = $this->getRepository('MealExtraIngredient')->find($id);

        if (! $mealExtraIngredient instanceof MealExtraIngredient) {
            throw new NotFoundHttpException('Meal extra ingredient not found for given id');
        }

        return $mealExtraIngredient;
    }

    protected function checkRestaurantAccess($restaurantId)
    {
        /** @var $this RestaurantTrait|\FOS\RestBundle\Controller\FOSRestController */
        if ($this->get('security.context')->isGranted(User::ROLE_SOLUTION_ADMIN)) {
            return true;
        }
        if ($this->get('security.context')->isGranted(User::ROLE_RESTAURANT_ADMIN)) {
            $this->checkRestaurantAdminAccess($this->getUser(), $restaurantId);
        }
        return true;
    }

    protected function getUserOrders($userId, array $orderBy = null, $start = 0, $limit = 100)
    {
        /** @var $this RestaurantTrait |\FOS\RestBundle\Controller\FOSRestController */
        $exposableOrders = [OrderStatus::PAID, OrderStatus::SENT_TO_PRINTER];

        return $this->getRepository('Order')
            ->findBy(['user' => $userId, 'orderStatus' => $exposableOrders], $orderBy, $limit, $start);
    }

    /**
     * @param User|int $user
     * @param int $orderId
     *
     * @return Order
     * @throws NotFoundHttpException
     */
    protected function getUserOrder($user, $orderId)
    {
        $order = $this->getRepository('Order')->findOneBy(['id' => $orderId, 'user' => $user]);

        /** @var $this RestaurantTrait |\FOS\RestBundle\Controller\FOSRestController */
        if (! $order instanceof Order) {
            throw new NotFoundHttpException('Order for specified user is not found');
        }

        return $order;
    }

    /**
     *
     * @param int $orderId
     * @return array
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getTableOrders(RestaurantCheckin $checkin)
    {
        /** @var $this RestaurantTrait |\FOS\RestBundle\Controller\FOSRestController */
        $friendCheckins  = $this->getRepository('RestaurantCheckin')->getTableMates($checkin);
        $orderRepository = $this->getRepository('Order');

        $i      = 1;
        $orders = [];
        /** @var $friendCheckin RestaurantCheckin */
        foreach ($friendCheckins as $friendCheckin) {
            $order = $orderRepository->findOneBy(
                [
                    'user'        => $friendCheckin->getUser()->getId(),
                    'tableNumber' => $friendCheckin->getTableNumber(),
                    'restaurant'  => $friendCheckin->getRestaurant()->getId(),
                    'orderStatus' => OrderStatus::NEW_ORDER
                ],
                ['id' => 'DESC']
            );

            if ($order instanceof Order) {
                if (!$order->getUser()->isRegistered()) {
                    $order->getUser()->setFirstName('Guest ' . $i);
                }
                $orders[] = $order;
                $i++;
            }
        }

        return $orders;
    }

    protected function checkinToRestaurantLocation(Restaurant $restaurant)
    {
        /** @var $this RestaurantTrait |\FOS\RestBundle\Controller\FOSRestController */

        /** @var $fb \Nmotion\NmotionBundle\Facebook\Facebook */
        $fb = $this->get('nmotion_facebook.api');

        if (!$fb->getAccessToken()) {
            throw new PreconditionFailedException('User is not authorized in Facebook');
        }

        $place = $restaurant->getFacebookPlaceId();
        $longitude = $restaurant->getAddress()->getLongitude();
        $latitude = $restaurant->getAddress()->getLatitude();

        if (empty($place) || empty($longitude) || empty($latitude)) {
            return false;
        }

        /** @var \Psr\Log\LoggerInterface $logger */
        $logger = $this->get('logger');

        try {
            $params = [
                'place'    => $place,
                'location' => [
                    'latitude'  => $latitude,
                    'longitude' => $longitude
                ]
            ];
            $logger->debug('Calling Facebook Graph API: POST /me/feed; ' . json_encode($params));
            $fb->api('me/feed', 'POST', $params);
        } catch (\FacebookApiException $e) {
            $logger->error('Facebook Graph API call resulted in failure: ' . $e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * Returns User's last checkin in the Restaurant if that is checked in, otherwise throws PreconditionFailedException
     *
     * @param int|User       $user
     * @param int|Restaurant $restaurant
     *
     * @return RestaurantCheckin
     *
     * @throws PreconditionFailedException
     * @throws RestaurantCheckinExpiredException
     */
    protected function getUserLastCheckinInRestaurant($user, $restaurant)
    {
        $checkin = $this->getRepository('RestaurantCheckin')->getUserLastCheckinInRestaurant($user, $restaurant);

        if (!($checkin instanceof RestaurantCheckin)) {
            throw new NotFoundHttpException('Active Restaurant Checkin not found.');
        }

        if ($checkin->isExpired()) {
            throw new RestaurantCheckinExpiredException('Restaurant checkin is expired.');
        }

        return $checkin;
    }
}
