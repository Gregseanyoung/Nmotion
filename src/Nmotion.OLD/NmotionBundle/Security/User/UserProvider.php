<?php

namespace Nmotion\NmotionBundle\Security\User;

use FOS\Rest\Util\Codes;
use Symfony\Component\DependencyInjection\ContainerInterface;

use FOS\UserBundle\Security\UserProvider as FOSUserProvider;
use FOS\UserBundle\Model\UserManagerInterface;

use Nmotion\NmotionBundle\Entity\User;
use Nmotion\NmotionBundle\Entity\UserDevice;
use Nmotion\NmotionBundle\Entity\RestaurantGuest;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class UserProvider extends FOSUserProvider
{
    private $container;

    /**
     * @param UserManagerInterface $userManager
     * @param ContainerInterface   $container
     */
    public function __construct(UserManagerInterface $userManager, ContainerInterface $container)
    {
        parent::__construct($userManager);

        $this->container = $container;
    }

    /**
     * @return \Doctrine\Bundle\DoctrineBundle\Registry
     */
    private function getDoctrine()
    {
        return $this->container->get('doctrine');
    }

    /**
     * @return \Nmotion\NmotionBundle\Facebook\Facebook
     */
    private function getFacebook()
    {
        return $this->container->get('nmotion_facebook.api');
    }

    /**
     * @param string $deviceIdentity
     *
     * @return User|null
     */
    private function findUserDevice($deviceIdentity)
    {
        return $this->getDoctrine()->getRepository('NmotionNmotionBundle:UserDevice')->findOneBy(
            ['deviceIdentity' => $deviceIdentity]
        );
    }

    private function createUserDevice($deviceIdentity)
    {
        /** @var $user User */
        $user = (new RestaurantGuest)
            ->setRegistered(false)
            ->setEnabled(true)
            ->setUsername($deviceIdentity)
            ->setEmail($deviceIdentity)
            ->setFirstName('anonymous')
            ->setLastName('anonymous')
            ->setPlainPassword($deviceIdentity);

        $userDevice = new UserDevice();
        $userDevice->setUser($user)
            ->setDeviceIdentity($deviceIdentity);

        /** @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getDoctrine()->getManager();
        $em->persist($userDevice);
        $em->flush();

        return $userDevice;
    }

    private function createUserFromFacebookProfile($facebookProfile)
    {
        /** @var $user User */
        $user = (new RestaurantGuest)
            ->setRegistered(true)
            ->setRegistrationOrigin(User::REGISTRATION_ORIGIN_FACEBOOK)
            ->setEnabled(true)
            ->setEmail($facebookProfile['email'])
            ->setUsername($facebookProfile['email'])
            ->setFirstName($facebookProfile['first_name'])
            ->setLastName($facebookProfile['last_name'])
            ->generateAndSetPlainPassword();

        /** @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return $user;
    }

    /**
     * @param string $facebookAccessToken
     *
     * @return User|null
     * @throws AuthenticationException
     */
    private function getFacebookProfile($facebookAccessToken)
    {
        try {
            $this->getFacebook()->setAccessToken($facebookAccessToken);
            return $this->getFacebook()->api('/me');
        } catch (\FacebookApiException $e) {
            $message = sprintf('[%s] %s', $e->getType(), $e->getMessage());
            if ($e->getType() === 'OAuthException') {
                throw new BadCredentialsException($message);
            } else {
                throw new AuthenticationException($message);
            }
        }
    }

    /**
     * @param string $deviceIdentity
     *
     * @return User
     */
    public function loadUserByDevice($deviceIdentity)
    {
        $userDevice = $this->findUserDevice($deviceIdentity);
        if (! $userDevice) {
            $userDevice = $this->createUserDevice($deviceIdentity);
        }

        return $userDevice->getUser();
    }

    /**
     * @param string $facebookAccessToken
     *
     * @return User
     * @throws HttpException
     */
    public function loadUserByFacebook($facebookAccessToken)
    {
        $facebookProfile = $this->getFacebookProfile($facebookAccessToken);

        if (! ($facebookProfile['email'] && $facebookProfile['first_name'] && $facebookProfile['last_name'])) {
            $errors = [];
            if (! $facebookProfile['email']) {
                $errors[] = 'Email is required';
            }
            if (! $facebookProfile['first_name']) {
                $errors[] = 'First name is required';
            }
            if (! $facebookProfile['last_name']) {
                $errors[] = 'Last name is required';
            }
            throw new HttpException(Codes::HTTP_PRECONDITION_FAILED, $errors);
        }
        try {
            $user = $this->loadUserByUsername($facebookProfile['email']);
        } catch (UsernameNotFoundException $e) {
            $user = $this->createUserFromFacebookProfile($facebookProfile);
        }

        return $user;
    }
}
