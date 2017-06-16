<?php

namespace Nmotion\BackofficeBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use FOS\Rest\Util\Codes;

use Nmotion\NmotionBundle\Controller\BaseRestController;
use Nmotion\NmotionBundle\Entity\MenuCategory;
use Nmotion\NmotionBundle\Form\MenuCategoryType as MenuCategoryTypeForm;

class MenuCategoryController extends BaseRestController
{
    use RestaurantAssertAccess;

    private function processForm(MenuCategory $menuCategory)
    {
        $statusCode = $menuCategory->isNew() ? Codes::HTTP_CREATED : Codes::HTTP_OK;

        $form = $this->createForm(new MenuCategoryTypeForm, $menuCategory);
        $form->bind($this->getRequest());
        
        // dirty hack for setting boolean value
        $menuCategory->setVisible((bool)$this->getRequest()->get('visible'));

        if ($form->isValid()) {
            $this->getDoctrine()->getManager()->persist($menuCategory);
            $this->getDoctrine()->getManager()->flush();
            $this->getDoctrine()->getManager()->refresh($menuCategory);

            return $this->jsonResponseSuccessful('', [$menuCategory], $statusCode);
        }

        return $this->jsonResponseFailed('Validation failed', [$form], Codes::HTTP_PRECONDITION_FAILED);
    }

    /**
     * POST /restaurants/{restaurantId}/menucategories.json
     *
     * @param int $restaurantId
     *
     * @return Response
     */
    public function postRestaurantMenucategoriesAction($restaurantId)
    {
        $this->setSerializerGroups(['backoffice', 'backoffice.entity']);

        $restaurant = $this->getRestaurant($restaurantId);

        $menuCategory = new MenuCategory;
        $menuCategory->setRestaurant($restaurant);

        return $this->processForm($menuCategory);
    }

    /**
     * GET /restaurants/{restaurantId}/menucategories.json
     *
     * @param int $restaurantId
     *
     * @return Response
     */
    public function getRestaurantMenucategoriesAction($restaurantId)
    {
        $this->setSerializerGroups(['backoffice', 'backoffice.list']);

        $restaurant = $this->getRestaurant($restaurantId);

        // provide authorization
        $this->assertUserHasAccessToMenuCategoriesRead($restaurant);

        $menuCategories = $this->getDoctrine()
            ->getRepository('NmotionNmotionBundle:MenuCategory')
            ->findBy(['restaurant' => $restaurant], ['position' => 'ASC']);

        return $this->jsonResponseSuccessful('', $menuCategories);
    }

    /**
     * GET /menucategories/{id}.json
     *
     * @param int $id
     *
     * @return Response
     */
    public function getMenucategoryAction($id)
    {
        $this->setSerializerGroups(['backoffice', 'backoffice.entity']);

        $menuCategory = $this->getMenuCategory($id);

        return $this->jsonResponseSuccessful('', [$menuCategory]);
    }

    /**
     * PUT /menucategories/{id}.json
     *
     * @param int $id
     *
     * @return Response json
     */
    public function putMenucategoryAction($id)
    {
        $this->setSerializerGroups(['backoffice', 'backoffice.entity']);

        $menuCategory = $this->getMenuCategory($id);

        // provide authorization
        $this->assertUserHasAccessToMenuCategoryUpdate($menuCategory);

        // verify data and do modification
        return $this->processForm($menuCategory);
    }

    /**
     * DELETE /menucategories/{id}.json
     *
     * @param int $id
     *
     * @return Response json
     */
    public function deleteMenucategoryAction($id)
    {
        $this->setSerializerGroups('backoffice');

        $menuCategory = $this->getMenuCategory($id);

        // provide authorization
        $this->assertUserHasAccessToMenuCategoryDelete($menuCategory);

        $this->getDoctrine()->getManager()->remove($menuCategory);
        $this->getDoctrine()->getManager()->flush();

        return $this->jsonResponseSuccessful();
    }
}
