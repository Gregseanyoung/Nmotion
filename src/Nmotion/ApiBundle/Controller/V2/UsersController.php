<?php

namespace Nmotion\ApiBundle\Controller\V2;

use Nmotion\ApiBundle\Controller\V1 as V1;
use Nmotion\NmotionBundle\Entity\RestaurantGuest;
use Nmotion\NmotionBundle\Entity\User;
use Nmotion\NmotionBundle\Entity\UserDevice;
use Nmotion\NmotionBundle\Exception\PreconditionFailedException;

use FOS\Rest\Util\Codes;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UsersController extends V1\UsersController
{
    private function getEntitiesFromLink()
    {
        $result  = [];
        $request = $this->getRequest();

        if (! $request->attributes->has('links')) {
            throw new PreconditionFailedException('Header "Links" is required');
        }

        foreach ($request->attributes->get('links') as $userDevice) {
            if (! $userDevice instanceof UserDevice) {
                throw new NotFoundHttpException('UserDevice is not found');
            }

            $result[] = $userDevice;
        }

        return $result;
    }

    /**
     * @param int $userId User id or special value "me", which is used to indicate the authenticated user.
     *
     * @throws BadRequestHttpException
     *
     * @deprecated
     */
    public function linkUserAction($userId)
    {
        throw new BadRequestHttpException(
            'This endpoint is being deprecated. Please do respective modifications to the client.'
        );
    }

    /**
     * Register new user
     * POST /users.json
     * request body: {"email": "email@email.com", "password": "value", "firstName": "John", "lastName": "Last"}
     *
     * @return Response json
     */
    public function postUsersAction()
    {
        /** @var $user User */
        $user = (new RestaurantGuest)
            ->setRegistered(true)
            ->setRegistrationOrigin(User::REGISTRATION_ORIGIN_NMOTION)
            ->setEnabled(true)
            ->setUsername($this->getRequest()->request->get('email'));

        return $this->processForm($user);
    }

    /**
     * Edit user's profile
     * PUT /users/{id}.json
     * request body: {"firstName": "value"}
     * request header: Auth: NmotionToken email|md5(password)
     *
     * @param int $userId
     *
     * @return Response json
     * @throws AccessDeniedHttpException
     */
    public function putUserAction($userId)
    {
        $user = $this->getUser();

        if ($user->getId() != (int) $userId) {
            throw new AccessDeniedHttpException('You don\'t have rights to update requested user');
        }

        $need_update = false;
        $email     = $this->getRequest()->request->get('email');
        $password  = $this->getRequest()->request->get('password');
        $firstName = $this->getRequest()->request->get('firstName');
        $lastName  = $this->getRequest()->request->get('lastName');

        if ($email) {
            $user->setEmail($email);
            $user->setUsername($email);
            $need_update = true;
        }
        if ($password) {
            $user->setPlainPassword($password);
            $need_update = true;
        }
        if ($firstName) {
            $user->setFirstName($firstName);
            $need_update = true;
        }
        if ($lastName) {
            $user->setLastName($lastName);
            $need_update = true;
        }

        if (!$need_update) {
            return $this->jsonResponse(true, Codes::HTTP_NOT_MODIFIED, 'Data not modified');
        }

        $entityValidation = $this->validateEntity($user, ['profile']);
        if (null !== $entityValidation) {
            return $entityValidation;
        }

        $this->getUserManager()->updateUser($user, true);

        return $this->jsonResponseSuccessful('', [$user]);
    }

    /**
     * Reset forgotten password
     * POST /users/forgot.json
     * request body: {email: "email@email.com"}
     *
     * @throws NotFoundHttpException
     * @throws AccessDeniedHttpException
     *
     * @return Response json
     */
    public function postUsersForgotAction()
    {
        $email = $this->getRequest()->request->get('email');

        // validate email
        $validation = $this->validateEmail($email);
        if (null !== $validation) {
            return $validation;
        }

        /** @var $user User */
        $user = $this->getDoctrine()->getRepository('NmotionNmotionBundle:User')->findOneBy(['email' => $email]);

        // see if user with such email exists
        if (!$user instanceof User) {
            throw new NotFoundHttpException('User with requested email not found');
        }

        if ($user->getRegistrationOrigin() == User::REGISTRATION_ORIGIN_FACEBOOK) {
            throw new AccessDeniedHttpException(
                'You cannot reset Facebook password, please do it on corresponding Facebook page'
            );
        }

        // generate unique token and save to user field
        $user->setConfirmationToken($this->getGeneratedUniqueToken());

        // send message to user with link with new token
        $message = \Swift_Message::newInstance()
            ->setSubject('Reset forgotten password')
            ->setFrom($this->container->getParameter('mailer_user'))
            ->setTo($email)
            ->setBody($this->renderView('NmotionApiBundle:User:email_reset_password.txt.twig', ['user' => $user]));

        // save user to DB
        $this->getDoctrine()->getManager()->flush();
        // actual send
        $this->get('mailer')->send($message);

        return $this->jsonResponseSuccessful();
    }
}
