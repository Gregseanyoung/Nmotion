<?php

namespace Nmotion\BackofficeBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\Rest\Util\Codes;

use Nmotion\NmotionBundle\Entity\RestaurantStaff;
use Nmotion\NmotionBundle\Form\RestaurantUserType;

class StaffController extends BackofficeController
{
    const RESTAURANT_STAFF_DEFAULT_PASSWORD = '456456';

    private function processForm(RestaurantStaff $user)
    {
        $statusCode = $user->isNew() ? Codes::HTTP_CREATED : Codes::HTTP_OK;

        $formOptions = [];
        if (! $user->isNew()) {
            $formOptions['validation_groups'] = ['profile'];
        }

        $form = $this->createForm(new RestaurantUserType(), $user, $formOptions)
            ->bind($this->getRequest());

        if ($form->isValid()) {
            $user->setUsername($user->getEmail());
            $this->getUserManager()->updateUser($user, true);

            if ($statusCode === Codes::HTTP_CREATED) {
                $this->sendEmailWithConfirmationToken($user);
            }

            return $this->jsonResponseSuccessful('', [$user], $statusCode);
        }

        return $this->jsonResponseFailed('Validation failed', [$form], Codes::HTTP_PRECONDITION_FAILED);
    }

    /**
     * Send email with confirmation token to just created restaurant staff user
     *
     * @param RestaurantStaff $staffUser
     */
    private function sendEmailWithConfirmationToken(RestaurantStaff $staffUser)
    {
        // generate unique token and save to user field
        $staffUser->setConfirmationToken($this->getGeneratedUniqueToken());
        $this->getUserManager()->updateUser($staffUser, true);

        // prepare message to user with confirmation link
        $message = \Swift_Message::newInstance()
            ->setSubject('Nmotion restaurant staff account created')
            ->setFrom($this->container->getParameter('mailer_user'))
            ->setTo($staffUser->getEmail())
            ->setBody(
                $this->renderView(
                    'NmotionBackofficeBundle:Restaurant:restaurant_staff_account_created.txt.twig',
                    ['staffUser' => $staffUser]
                )
            );

        // actual send
        $this->get('mailer')->send($message);
    }

    /**
     * POST /restaurants/{restaurantId}/staff
     *
     * @param integer $restaurantId
     *
     * @return Response
     */
    public function postRestaurantStaffAction($restaurantId)
    {
        $this->checkRestaurantAccess($restaurantId);

        $this->setSerializerGroups(['backoffice', 'backoffice.entity']);

        $staffUser = (new RestaurantStaff())
            ->setPlainPassword(self::RESTAURANT_STAFF_DEFAULT_PASSWORD);

        // flush for restaurant will be done along with user one
        $restaurant = $this->getRestaurant($restaurantId);
        $restaurant->addStaff($staffUser);

        return $this->processForm($staffUser);
    }

    /**
     * GET /restaurants/{restaurantId}/staff
     *
     * @param integer $restaurantId
     *
     * @return Response
     */
    public function getRestaurantStaffAction($restaurantId)
    {
        $this->checkRestaurantAccess($restaurantId);

        $this->setSerializerGroups(['backoffice', 'backoffice.list']);

        $staff = $this->getRestaurant($restaurantId)->getStaff();

        return $this->entriesResponse($staff, count($staff));
    }

    /**
     * PUT /staff/{id}
     *
     * @param int $id
     *
     * @return Response json
     * @throws NotFoundHttpException
     */
    public function putStaffAction($id)
    {
        // check if user exist and is of a restaurant staff role
        $staffUser = $this->getRepository('RestaurantStaff')->find($id);
        if (! $staffUser instanceof RestaurantStaff) {
            throw new NotFoundHttpException('Restaurant staff user not found for given id');
        }

        $this->checkRestaurantAccess(
            $staffUser->getRestaurant()->getId()
        );

        $this->setSerializerGroups(['backoffice', 'backoffice.entity']);

        return $this->processForm($staffUser);
    }

    /**
     * DELETE /staff/{id}
     *
     * @param int $id
     *
     * @return Response json
     * @throws NotFoundHttpException
     */
    public function deleteStaffAction($id)
    {
        // check if user exist and is of a restaurant staff role
        $staffUser = $this->getRepository('RestaurantStaff')->find($id);
        if (! $staffUser instanceof RestaurantStaff) {
            throw new NotFoundHttpException('Restaurant staff user not found for given id');
        }

        $this->checkRestaurantAccess(
            $staffUser->getRestaurant()->getId()
        );

        $this->setSerializerGroups(['backoffice', 'backoffice.entity']);

        $this->getDoctrine()->getManager()->remove($staffUser);
        $this->getDoctrine()->getManager()->flush();

        return $this->jsonResponseSuccessful();
    }
}
