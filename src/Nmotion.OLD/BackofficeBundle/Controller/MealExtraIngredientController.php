<?php

namespace Nmotion\BackofficeBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use FOS\Rest\Util\Codes;

use Nmotion\NmotionBundle\Controller\BaseRestController;
use Nmotion\NmotionBundle\Entity\MealExtraIngredient;
use Nmotion\NmotionBundle\Form\MealExtraIngredientType as MealExtraIngredientTypeForm;
use Nmotion\NmotionBundle\Controller\FormTrait;

/**
 * Controller for managing extra meal ingredients
 */
class MealExtraIngredientController extends BaseRestController
{
    use FormTrait;

    private function processForm(MealExtraIngredient $ingredient)
    {
        $action = $ingredient->getId() ? 'edit' : 'register';

        $statusCode = $action == 'register' ? Codes::HTTP_CREATED : Codes::HTTP_OK;

        $form = $this->createForm(new MealExtraIngredientTypeForm, $ingredient);
        $form->bind($this->getRequest());

        if ($form->isValid()) {
            $this->getDoctrine()->getManager()->persist($ingredient);
            $this->getDoctrine()->getManager()->flush();

            return $this->jsonResponseSuccessful('', [$ingredient], $statusCode);
        }

        return $this->jsonResponseFailed('Validation failed', [$form], Codes::HTTP_PRECONDITION_FAILED);
    }

    /**
     * POST /meals/{mealId}/extraingredient.json
     *
     * @param int $mealId
     * @return Response
     */
    public function postMealExtraingredientAction($mealId)
    {
        $this->setSerializerGroups('backoffice');

        $meal = $this->getMeal($mealId);
        $this->checkRestaurantAccess($meal->getMenuCategory()->getRestaurant()->getId());

        $ingredient = new MealExtraIngredient();
        $ingredient->setMeal($meal);

        return $this->processForm($ingredient);
    }

    /**
     * GET /meals/{mealId}/extraingredients.json
     *
     * @param int $mealId
     *
     * @return Response
     */
    public function getMealExtraingredientsAction($mealId)
    {
        $this->setSerializerGroups('backoffice', 'backoffice.list');
        $meal = $this->getMeal($mealId);

        $this->checkRestaurantAccess($meal->getMenuCategory()->getRestaurant()->getId());

        $mealExtraIngredients = $meal->getMealExtraIngredients();

        return $this->jsonResponseSuccessful('', $mealExtraIngredients);
    }

    /**
     * GET /mealextraingredients/{id}.json
     *
     * @param int $id
     *
     * @return Response
     */
    public function getMealextraingredientAction($id)
    {
        $this->setSerializerGroups('backoffice', 'backoffice.entity');

        $mealExtraIngredient = $this->getMealExtraIngredient($id);
        $this->checkRestaurantAccess($this->getRestaurantId($mealExtraIngredient));

        return $this->jsonResponseSuccessful('', [$mealExtraIngredient]);
    }

    /**
     * DELETE /mealextraingredients/{id}.json
     *
     * @param int $id
     *
     * @return Response json
     */
    public function deleteMealextraingredientAction($id)
    {
        $this->setSerializerGroups('backoffice');

        $mealExtraIngredient = $this->getMealExtraIngredient($id);
        $this->checkRestaurantAccess($this->getRestaurantId($mealExtraIngredient));

        $this->getDoctrine()->getManager()->remove($mealExtraIngredient);
        $this->getDoctrine()->getManager()->flush();

        return $this->jsonResponseSuccessful();
    }

    /**
     * PUT /mealextraingredients/{id}.json
     *
     * @param int $id
     *
     * @return Response json
     */
    public function putMealextraingredientAction($id)
    {
        $this->setSerializerGroups('backoffice', 'backoffice.entity');

        $mealExtraIngredient = $this->getMealExtraIngredient($id);
        $this->checkRestaurantAccess($this->getRestaurantId($mealExtraIngredient));

        return $this->processForm($mealExtraIngredient);
    }

    private function getRestaurantId(MealExtraIngredient $mealExtraIngredient)
    {
        return $mealExtraIngredient->getMeal()->getMenuCategory()->getRestaurant()->getId();
    }
}
