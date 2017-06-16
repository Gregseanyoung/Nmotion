<?php

namespace Nmotion\BackofficeBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use FOS\Rest\Util\Codes;

use Nmotion\NmotionBundle\Controller\BaseRestController;
use Nmotion\NmotionBundle\Entity\MealOption;
use Nmotion\NmotionBundle\Form\MealOptionType as MealOptionTypeForm;
use Nmotion\NmotionBundle\Controller\RestaurantTrait;
use Nmotion\NmotionBundle\Controller\FormTrait;

class MealOptionController extends BaseRestController
{
    use RestaurantTrait;
    use FormTrait;

    private function processForm(MealOption $mealOption)
    {
        $action = $mealOption->getId() ? 'edit' : 'register';

        $statusCode = $action == 'register' ? Codes::HTTP_CREATED : Codes::HTTP_OK;

        $form = $this->createForm(new MealOptionTypeForm, $mealOption);
        $form->bind($this->getRequest());

        if ($form->isValid()) {
            $this->getDoctrine()->getManager()->persist($mealOption);
            $this->getDoctrine()->getManager()->flush();

            // make first added option default
            if ($action == 'register') {
                if (count($mealOption->getMeal()->getMealOptions()) == 1) {
                    $mealOption->getMeal()->setMealOptionDefaultId($mealOption->getId());
                    $this->getDoctrine()->getManager()->flush();
                }
            }

            return $this->jsonResponseSuccessful('', [$mealOption], $statusCode);
        }

        return $this->jsonResponseFailed(
            'Validation failed',
            [$this->getFormErrorMessages($form)],
            Codes::HTTP_PRECONDITION_FAILED
        );
    }

    /**
     * GET /meals/{mealId}/options.json
     *
     * @param int $mealId
     *
     * @return Response
     */
    public function getMealOptionsAction($mealId)
    {
        $this->setSerializerGroups('backoffice');

        $mealOptions = $this->getMeal($mealId)->getMealOptions();

        return $this->jsonResponseSuccessful('', $mealOptions);
    }

    /**
     * GET /mealoptions/{id}.json
     *
     * @param int $id
     *
     * @return Response
     */
    public function getMealoptionAction($id)
    {
        $this->setSerializerGroups('backoffice');

        $mealOption = $this->getMealOption($id);

        return $this->jsonResponseSuccessful('', [$mealOption]);
    }

    /**
     * POST /meals/{mealId}/options.json
     *
     * @param int $mealId
     *
     * @return Response
     */
    public function postMealOptionsAction($mealId)
    {
        $this->setSerializerGroups('backoffice');

        $meal = $this->getMeal($mealId);
        $mealOption = new MealOption();
        $mealOption->setMeal($meal);

        return $this->processForm($mealOption);
    }

    /**
     * PUT /mealoptions/{id}.json
     *
     * @param int $id
     *
     * @return Response json
     */
    public function putMealoptionAction($id)
    {
        $this->setSerializerGroups('backoffice');

        $mealOption = $this->getMealOption($id);

        return $this->processForm($mealOption);
    }

    /**
     * DELETE /mealoptions/{id}.json
     *
     * @param int $id
     *
     * @return Response json
     */
    public function deleteMealoptionAction($id)
    {
        $this->setSerializerGroups('backoffice');

        $mealOption = $this->getMealOption($id);
        $meal = $mealOption->getMeal();

        $this->getDoctrine()->getManager()->remove($mealOption);
        $this->getDoctrine()->getManager()->flush();

        // deleting default meal option
        if ($id == $meal->getMealOptionDefaultId()) {
            $meal->setMealOptionDefaultId(null);
            $meal->setFirstMealOptionDefault();
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->jsonResponseSuccessful();
    }
}
