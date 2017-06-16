<?php

namespace Nmotion\ApiBundle\Controller\V1\Restaurants;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Nmotion\NmotionBundle\Controller\BaseRestController;

class MealExtraIngredientsController extends BaseRestController
{
    /**
     * GET /meals/{mealId}/extraingredients.json
     *
     * @param int $mealId
     * @return Response
     */
    public function getMealExtraingredientsAction($mealId)
    {
        $this->setSerializerGroups(['api', 'api.list']);

        $meal = $this->getMeal($mealId);
        $extraIngredients = null;
        $time = (time() + date('Z')) % 86400;
        if ($time >= $meal->getTimeFrom() && $time <= $meal->getTimeTo()) {
            $extraIngredients = $meal->getMealExtraIngredients();
        }

        return $this->jsonResponseSuccessful('', $extraIngredients);
    }
}
