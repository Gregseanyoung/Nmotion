<?php

namespace Nmotion\BackofficeBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use FOS\Rest\Util\Codes;

use Nmotion\NmotionBundle\Entity\Meal;
use Nmotion\NmotionBundle\Entity\RestaurantStaff;
use Nmotion\NmotionBundle\Form\MealType as MealTypeForm;
use Nmotion\NmotionBundle\Form\MealTypeForStaff as MealTypeFormForStaff;
use Nmotion\NmotionBundle\Controller\RestaurantTrait;
use Nmotion\NmotionBundle\Controller\FormTrait;

class MealController extends BackofficeController
{
    use FormTrait;

    /**
     * trait RestaurantAssertAccess uses RestaurantTrait
     */
    use RestaurantAssertAccess;

    private function processForm(Meal $meal)
    {
        $action = $meal->getId() ? 'edit' : 'register';

        $statusCode = $action == 'register' ? Codes::HTTP_CREATED : Codes::HTTP_OK;

        $formType = ($this->isRestaurantStaff() && ($this->getUser() instanceof  RestaurantStaff))
            ? new MealTypeFormForStaff
            : new MealTypeForm;

        $form = $this->createForm($formType, $meal);
        $form->bind($this->getRequest());

        // dirty hack for setting boolean value
        $meal->setVisible((bool)$this->getRequest()->get('visible'));

        if ($form->isValid()) {
            $this->getDoctrine()->getManager()->persist($meal);
            $this->getDoctrine()->getManager()->flush();
            $this->getDoctrine()->getManager()->refresh($meal);

            $request = $this->getRequest()->request;
            if ($request->has('mealOptions')) {
                foreach ($request->get('mealOptions') as $i => $mealOption) {
                    if (array_key_exists('isDefault', $mealOption) && $mealOption['isDefault']) {
                        $meal->setMealOptionDefaultId(
                            $meal->getMealOptions()[$i]->getId()
                        );
                        $this->getDoctrine()->getManager()->flush();
                    }
                }
            }

            return $this->jsonResponseSuccessful('', [$meal], $statusCode);
        }

        return $this->jsonResponseFailed('Validation failed', [$form], Codes::HTTP_PRECONDITION_FAILED);
    }

    /**
     * GET /menucategories/{categoryId}/meals.json
     *
     * @param int $categoryId
     *
     * @return Response
     */
    public function getMenucategoryMealsAction($categoryId)
    {
        $this->assertUserHasAccessToMealsRead($categoryId);

        $this->setSerializerGroups(['backoffice', 'backoffice.list']);

        $meals = $this->getMenuCategory($categoryId)->getMenuMeals();

        return $this->jsonResponseSuccessful('', $meals);
    }

    /**
     * GET /meals/{id}.json
     *
     * @param int $id
     *
     * @return Response
     */
    public function getMealAction($id)
    {
        $this->setSerializerGroups(['backoffice', 'backoffice.entity']);

        $meal = $this->getMeal($id);

        return $this->jsonResponseSuccessful('', [$meal]);
    }

    /**
     * POST /menucategories/{categoryId}/meals.json
     *
     * @param int $categoryId
     *
     * @internal param int $restaurantId
     * @return Response
     */
    public function postMenucategoryMealsAction($categoryId)
    {
        $this->setSerializerGroups(['backoffice', 'backoffice.entity']);

        $category = $this->getMenuCategory($categoryId);
        $meal = new Meal();
        $meal->setMenuCategory($category);

        return $this->processForm($meal);
    }

    /**
     * PUT /meals/{id}.json
     *
     * @param int $id
     *
     * @return Response json
     */
    public function putMealAction($id)
    {
        $this->setSerializerGroups(['backoffice', 'backoffice.entity']);

        $meal = $this->getMeal($id);

        return $this->processForm($meal);
    }

    /**
     * DELETE /meals/{id}.json
     *
     * @param int $id
     *
     * @return Response json
     */
    public function deleteMealAction($id)
    {
        $this->setSerializerGroups('backoffice');

        $meal = $this->getMeal($id);

        $this->getDoctrine()->getManager()->remove($meal);
        $this->getDoctrine()->getManager()->flush();

        return $this->jsonResponseSuccessful();
    }
}
