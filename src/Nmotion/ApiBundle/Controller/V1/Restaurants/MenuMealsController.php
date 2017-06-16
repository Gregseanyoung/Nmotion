<?php

namespace Nmotion\ApiBundle\Controller\V1\Restaurants;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Nmotion\NmotionBundle\Controller\RestaurantTrait;
use Nmotion\NmotionBundle\Controller\BaseRestController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MenuMealsController extends BaseRestController
{
    use RestaurantTrait;

    /**
     * GET /menucategories/{categoryId}/meals.json
     *
     * @param int $categoryId
     * @return Response
     */
    public function getMenucategoryMealsAction($categoryId)
    {
        $this->setSerializerGroups(['api', 'api.list']);

        $category  = $this->getMenuCategory($categoryId);
        $menuMeals = $this->getRepository('Meal')
            ->getAllMenuMealsForCategoryWithinTimeFrame($category);

        return $this->jsonResponseSuccessful('', $menuMeals);
    }

    /**
     * GET /meals/{id}.json
     *
     * @param int $id
     *
     * @return Response
     * @throws NotFoundHttpException
     */
    public function getMealAction($id)
    {
        $this->setSerializerGroups(['api', 'api.entity']);

        $menuMealRepository = $this->getRepository('Meal');

        $menuMeal = $menuMealRepository->find($id);

        if (!$menuMeal) {
            throw new NotFoundHttpException('Menu meal not found for given menu category');
        }

        return $this->jsonResponseSuccessful('', [$menuMeal]);
    }
}
