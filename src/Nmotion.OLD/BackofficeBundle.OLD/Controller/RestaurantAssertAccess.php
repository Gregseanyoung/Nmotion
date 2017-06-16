<?php
/**
 * @author tiger
 */

namespace Nmotion\BackofficeBundle\Controller;

use Nmotion\NmotionBundle\Entity\MenuCategory;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Nmotion\NmotionBundle\Controller\RestaurantTrait;
use Nmotion\NmotionBundle\Entity\Restaurant;
use Nmotion\NmotionBundle\Entity\RestaurantAdmin;
use Nmotion\NmotionBundle\Entity\RestaurantStaff;
use Nmotion\NmotionBundle\Entity\Order;
use Nmotion\NmotionBundle\Entity\User;

trait RestaurantAssertAccess
{
    use RestaurantTrait;

    /**
     * @param int|Restaurant $restaurant
     *
     * @return Restaurant
     * @throws \InvalidArgumentException
     */
    private function restaurant($restaurant)
    {
        if ($restaurant instanceof Restaurant) {
            return $restaurant;
        }

        if (! is_numeric($restaurant)) {
            $frame = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2)[1];
            $method = basename($frame['class']) . '::' . $frame['function'] . '()';
            throw new \InvalidArgumentException(
                'Argument 1 passed to '. $method .' must be numeric'
                . ' or an instance of Restaurant, ' . gettype($restaurant) . ' given'
            );
        }

        /** @var $this RestaurantAssertAccess|\FOS\RestBundle\Controller\FOSRestController */
        $restaurant = $this->getRepository()->find($restaurant);

        return $restaurant;
    }

    /**
     * Helper-method
     *
     * @param Restaurant $restaurant
     *
     * @return bool
     * @throws AccessDeniedException
     */
    private function assertUserIsBoundToRestaurant(Restaurant $restaurant)
    {
        /** @var $this RestaurantAssertAccess|\FOS\RestBundle\Controller\FOSRestController */

        /** @var $securityContext SecurityContext */
        $securityContext = $this->get('security.context');

        if ($securityContext->isGranted(User::ROLE_SOLUTION_ADMIN)) {
            return true;
        }

        $user = $this->getUser();

        if ($securityContext->isGranted(User::ROLE_RESTAURANT_ADMIN)
            && $user instanceof RestaurantAdmin
            && $user->getRestaurant() === $restaurant
        ) {
            return true;
        }

        if ($securityContext->isGranted(User::ROLE_RESTAURANT_STAFF)
            && $user instanceof RestaurantStaff
            && $user->getRestaurant() === $restaurant
        ) {
            return true;
        }

        throw new AccessDeniedException;
    }

    /**
     * @param Restaurant|int $restaurant
     *
     * @return void
     * @throws AccessDeniedException
     */
    public function assertUserHasAccessToRestaurantRead($restaurant)
    {
        $this->assertUserIsBoundToRestaurant($this->restaurant($restaurant));
    }

    /**
     * @param Restaurant|int $restaurant
     *
     * @return void
     * @throws AccessDeniedException
     */
    public function assertUserHasAccessToRestaurantUpdate($restaurant)
    {
        /** @var $securityContext SecurityContext */
        $securityContext = $this->get('security.context');
        if ($securityContext->isGranted(User::ROLE_RESTAURANT_ADMIN)) {
            return;
        }

        $this->assertUserIsBoundToRestaurant($this->restaurant($restaurant));

        throw new AccessDeniedException;
    }

    /**
     * @param int|Restaurant $restaurant
     *
     * @return void
     * @throws AccessDeniedException
     */
    public function assertUserHasAccessToMenuCategoriesRead($restaurant)
    {
        $this->assertUserIsBoundToRestaurant($this->restaurant($restaurant));
    }

    /**
     * @param int|MenuCategory $menuCategory
     *
     * @return bool
     * @throws AccessDeniedException
     */
    public function assertUserHasAccessToMenuCategoryUpdate($menuCategory)
    {
        /** @var $this RestaurantAssertAccess|\FOS\RestBundle\Controller\FOSRestController */

        /** @var $securityContext SecurityContext */
        $securityContext = $this->get('security.context');

        if ($securityContext->isGranted(User::ROLE_SOLUTION_ADMIN)) {
            return true;
        }

        if (! $menuCategory instanceof MenuCategory) {
            $menuCategory = $this->getMenuCategory($menuCategory);
        }

        $user = $this->getUser();

        if ($securityContext->isGranted(User::ROLE_RESTAURANT_ADMIN)
            && $user instanceof RestaurantAdmin
            && $user->getRestaurant() === $menuCategory->getRestaurant()
        ) {
            return true;
        }

        throw new AccessDeniedException;
    }

    /**
     * @param int|MenuCategory $menuCategory
     *
     * @return bool
     * @throws AccessDeniedException
     */
    public function assertUserHasAccessToMenuCategoryDelete($menuCategory)
    {
        return $this->assertUserHasAccessToMenuCategoryUpdate($menuCategory);
    }

    /**
     * @param int|MenuCategory $menuCategory
     *
     * @return void
     * @throws AccessDeniedException
     */
    public function assertUserHasAccessToMealsRead($menuCategory)
    {
        if (! $menuCategory instanceof MenuCategory) {
            $menuCategory = $this->getMenuCategory($menuCategory);
        }

        $this->assertUserIsBoundToRestaurant($menuCategory->getRestaurant());
    }

    /**
     * @param integer|integer[] $ids
     *
     * @return void
     * @throws AccessDeniedException
     */
    public function assertUserHasAccessToOrders($ids)
    {
        /** @var $this RestaurantAssertAccess|\FOS\RestBundle\Controller\FOSRestController */

        /** @var $securityContext SecurityContext */
        $securityContext = $this->get('security.context');

        // only sadmin and radmin are allowed to get orders
        if ($securityContext->isGranted(User::ROLE_SOLUTION_ADMIN)) {
            return;
        } elseif (! $securityContext->isGranted(User::ROLE_RESTAURANT_ADMIN) && ! $securityContext->isGranted(User::ROLE_RESTAURANT_STAFF)) {
            throw new AccessDeniedException;
        }

        // ensure we are dealing with an array of integers
        $ids = array_map('intval', (array) $ids);

        $user = $this->getUser();
        if (! $user instanceof RestaurantAdmin && ! $user instanceof RestaurantStaff) {
            throw new AccessDeniedException;
        }

        $repository = $this->getRepository('Order');

        /** @var $orders Order[] */
        $orders = $repository->createQueryBuilder('u')
            ->where('u.id IN ( ?0 )')
            ->getQuery()
            ->execute([$ids]);

        foreach ($orders as $order) {
            if (! $user->getRestaurant() === $order->getRestaurant()) {
                throw new AccessDeniedException;
            }
        }
    }
}
