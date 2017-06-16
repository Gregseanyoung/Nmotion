<?php

namespace Nmotion\ApiBundle\Controller\V1\Restaurants;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Nmotion\NmotionBundle\Controller\BaseRestController;
use Nmotion\NmotionBundle\Controller\RestaurantTrait;

class MenuCategoryController extends BaseRestController
{
    use RestaurantTrait;

    /**
     * GET /restaurants/{restaurantId}/menucategories.json
     *
     * @param int $restaurantId
     *
     * @return Response
     */
    public function getRestaurantMenucategoriesAction($restaurantId)
    {
        $this->setSerializerGroups('api');

        $restaurant     = $this->getRestaurant($restaurantId);
        $menuCategories = $this->getRepository('menuCategory')
            ->getAllMenuCategoriesForRestaurantWithinTimeFrame($restaurant);

        return $this->jsonResponseSuccessful('', $menuCategories);
    }

    /**
     * GET /menucategories/{id}.json
     *
     * @param int $restaurantId
     * @param int $id
     *
     * @return Response
     */
    public function getMenucategoryAction($id)
    {
        $this->setSerializerGroups('api');

        $menuCategory = $this->getMenuCategory($id);

        return $this->jsonResponseSuccessful('', [$menuCategory]);
    }
}
