<?php

namespace Nmotion\ApiBundle\Controller\V1;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

use FOS\Rest\Util\Codes;

use Nmotion\NmotionBundle\Controller\BaseRestController;
use Nmotion\NmotionBundle\Entity\User;
use Nmotion\NmotionBundle\Entity\RestaurantGuest;
use Nmotion\NmotionBundle\Form\UserType as UserTypeForm;
use Nmotion\NmotionBundle\Controller\FormTrait;

class UsersController extends BaseRestController
{
    use FormTrait;

    protected function processForm(User $user)
    {
        $action = $user->getId() ? 'edit' : 'new';

        $statusCode = $action == 'new' ? Codes::HTTP_CREATED : Codes::HTTP_OK;

        $formOptions = [];
        if (! $user->isNew()) {
            $formOptions['validation_groups'] = ['profile'];
        }

        $form = $this->createForm(new UserTypeForm, $user, $formOptions);

        $request = $this->getRequest();
        $request->request->set('plainPassword', $request->request->get('password'));
        $request->request->remove('password');

        $form->bind($request);

        if ($form->isValid()) {

            $this->getUserManager()->updateUser($user, true);

            if ($action == 'new') {
                // send confirmation email to user that account was created
                $this->sendEmailToNewUser($user);
            }

            return $this->jsonResponseSuccessful('', [$user], $statusCode);
        }

        return $this->jsonResponseFailed(
            'Validation failed',
            [$this->getFormErrorMessages($form)],
            Codes::HTTP_PRECONDITION_FAILED
        );
    }

    /**
     * Sends confirmation email to just created user
     *
     * @param User $user
     */
    private function sendEmailToNewUser($user)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('Nmotion account created')
            ->setFrom($this->container->getParameter('mailer_user'))
            ->setTo($user->getEmail())
            ->setBody($this->renderView('NmotionApiBundle:User:email_account_created.txt.twig', ['user' => $user]));

        $this->get('mailer')->send($message);
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
            ->setEnabled(true)
            ->setUsername($this->getRequest()->request->get('email'));

        return $this->processForm($user);
    }

    /**
     * GET /users/me.json
     *
     * @return Response json
     * @throws HttpException
     */
    public function getUsersMeAction()
    {
        $user = $this->getUser();

        return $this->jsonResponseSuccessful('', [$user]);
    }

    /**
     * Edit user's profile
     * PUT /api/v1/users/{id}.json
     * request body: {"firstName": "value"}
     * request header: Auth: email|md5(password)
     *
     * @param int $userId
     *
     * @return Response json
     * @throws HttpException
     */
    public function putUserAction($userId)
    {
        $user = $this->getUser();

        if ($user->getId() != (int) $userId) {
            throw new AccessDeniedException('You don\'t have rights to update requested user');
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

        $entityValidation = $this->validateEntity($user);
        if (null !== $entityValidation) {
            return $entityValidation;
        }

        $this->getUserManager()->updateUser($user, true);

        return $this->jsonResponseSuccessful('', [$user]);
    }

    /**
     * Reset forgotten password
     * POST /api/v1/users/forgot.json
     * request body: {email: "email@email.com"}
     *
     * @throws NotFoundHttpException
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

        $user = $this->getDoctrine()->getRepository('NmotionNmotionBundle:User')->findOneBy(['email' => $email]);

        // see if user with such email exists
        if (!$user) {
            return $this->jsonResponseFailed(
                'Not found',
                ['User with requested email not found'],
                Codes::HTTP_NOT_FOUND
            );
        }

        // generate unique token and save to user field
        $user->setConfirmationToken($this->getGeneratedUniqueToken());

        // send message to user with link with new token
        $message = \Swift_Message::newInstance()
            ->setSubject('Reset forgotten password')
            ->setFrom($this->container->getParameter('mailer_user'))
            ->setTo($email)
            ->setBody(
                $this->renderView(
                    'NmotionApiBundle:User:email_reset_password.txt.twig',
                    ['user' => $user]
                )
            );

        try {
            // save user to DB
            $this->getDoctrine()->getManager()->flush();
            // actual send
            $this->get('mailer')->send($message);
        } catch (\Exception $exc) {
            return $this->jsonResponseFailed('Internal error', [$exc->getMessage()], Codes::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->jsonResponseSuccessful();
    }

    /**
     * Validates email
     *
     * @param string $email
     * @return Response if error, null if ok
     */
    protected function validateEmail($email)
    {
        $errorMessages = [];

        $notBlankConstraint = new NotBlank();
        $notBlankConstraint->message = 'Email address should not be blank';
        $errorNotBlank = $this->get('validator')->validateValue($email, $notBlankConstraint);
        if (count($errorNotBlank) > 0) {
            $errorMessages[] = $errorNotBlank[0]->getMessage();
        }

        $emailConstraint = new Email();
        $emailConstraint->message = 'Invalid email address';
        $errorEmail = $this->get('validator')->validateValue($email, $emailConstraint);
        if (count($errorEmail) > 0) {
            $errorMessages[] = $errorEmail[0]->getMessage();
        }

        if (count($errorMessages) > 0) {
            return $this->jsonResponseFailed(
                'Email validation failed',
                $errorMessages,
                Codes::HTTP_PRECONDITION_FAILED
            );
        }
        return null;
    }
}
