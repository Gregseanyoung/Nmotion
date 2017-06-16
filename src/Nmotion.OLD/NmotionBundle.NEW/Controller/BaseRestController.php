<?php

namespace Nmotion\NmotionBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use FOS\Rest\Util\Codes;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\ViewHandler;
use FOS\UserBundle\Doctrine\UserManager;

use Nmotion\NmotionBundle\Entity\User;
use Nmotion\NmotionBundle\Controller\RestaurantTrait;

class BaseRestController extends FOSRestController
{
    use RestaurantTrait;

    private $serializerGroups;

    /**
     * @return User
     * @throws HttpException UNAUTHORIZED if have no authenticated user
     */
    public function getUser()
    {
        $user = parent::getUser();
        
        if (!$user instanceof User) {
            throw new HttpException(Codes::HTTP_UNAUTHORIZED, 'Authentication required');
        }

        return $user;
    }

    /**
     * Get user manager
     *
     * @return UserManager
     */
    public function getUserManager()
    {
        return $this->get('fos_user.user_manager');
    }

    /**
     * Generate unique token and return it
     *
     * @return string
     */
    public function getGeneratedUniqueToken()
    {
        return $this->container->get('fos_user.util.token_generator')->generateToken();
    }

    /**
     * Set`s a group or an array of groups to be used for serialization
     * of an entity properties in the response
     *
     * @param string|array $groups Group name or an array with groups names
     *
     * @see \JMS\SerializerBundle\Serializer\Serializer::setGroups
     */
    public function setSerializerGroups($groups)
    {
        $this->serializerGroups = $groups;
    }

    /**
     * Returns failed rest response in json format
     *
     * @param string $message
     * @param array  $errors
     * @param int    $http_code
     *
     * @return Response json
     */
    public function jsonResponseFailed($message, $errors, $http_code)
    {
        return $this->jsonResponse(false, $http_code, $message, $errors);
    }

    /**
     * Returns sucessful rest response in json format
     *
     * @param string $message
     * @param array  $entries
     * @param int    $http_code
     *
     * @return Response json
     */
    public function jsonResponseSuccessful($message = '', $entries = [], $http_code = Codes::HTTP_OK)
    {
        return $this->jsonResponse(true, $http_code, $message, [], $entries);
    }

    /**
     * Returns rest response in json format
     *
     * @param boolean $success
     * @param int     $http_code
     * @param string  $message
     * @param array   $errors
     * @param array   $entries
     *
     * @return Response json
     */
    public function jsonResponse($success, $http_code, $message = '', $errors = [], $entries = [])
    {
        $jsonResponse = ['success' => (bool) $success];
        if (!empty($message)) {
            $jsonResponse['message'] = $message;
        }
        if (is_array($entries) || ($entries instanceof \ArrayAccess)) {
            $jsonResponse['entries'] = $entries;
        }
        if (!empty($errors)) {
            if (is_array($errors)) {
                $jsonResponse['errors'] = $errors;
            } elseif (is_scalar($errors)) {
                $jsonResponse['errors'] = [$errors];
            }
        }

        return $this->view($jsonResponse, $http_code);
    }

    /**
     * Validates entity against entity's validation rules
     *
     * @param object $entity
     *
     * @return Response in json if error, null if ok
     */
    public function validateEntity($entity, array $validationGroups = [])
    {
        /** @var $errors ConstraintViolationList|ConstraintViolation[] */
        $errors = $this->get('validator')->validate($entity, $validationGroups);

        if (!count($errors)) {
            return null;
        }

        $errorMessages = [];
        foreach ($errors as $error) {
            /** @var $translator \Symfony\Component\Translation\Translator */
            $translator = $this->get('translator');

            if ($error->getMessagePluralization() !== null && strpos($error->getMessage(), '|')) {
                $errorMessages[] = $translator->transChoice(
                    $error->getMessage(),
                    $error->getMessagePluralization(),
                    $error->getMessageParameters(),
                    'validators'
                );
            } else {
                $errorMessages[] = $translator->trans($error->getMessage(), [], 'validators');
            }
        }

        return $this->jsonResponseFailed('Validation failed', $errorMessages, Codes::HTTP_PRECONDITION_FAILED);
    }

    protected function view($data = null, $statusCode = null, array $headers = array())
    {
        $view = parent::view($data, $statusCode, $headers);

        /** @var $viewHandler ViewHandler */
        $viewHandler = $this->container->get('fos_rest.view_handler');

        $view->setSerializationContext(
            $viewHandler->getSerializationContext($view)
        );

        if ($this->serializerGroups) {
            $view->getSerializationContext()->setGroups($this->serializerGroups);
        }

        return $view;
    }
}
