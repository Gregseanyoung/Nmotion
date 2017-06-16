<?php
/**
 * @author tiger
 */

namespace Nmotion\NmotionBundle\Security\Authentication\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use Nmotion\NmotionBundle\Entity\User;
use Nmotion\NmotionBundle\Security\Authentication\Token\NmotionToken;
use Nmotion\NmotionBundle\Security\Authentication\Token\FacebookToken;
use Nmotion\NmotionBundle\Security\Authentication\Token\DeviceToken;

class WsseProvider implements AuthenticationProviderInterface
{
    /**
     * @var UserProviderInterface|\Nmotion\NmotionBundle\Security\User\UserProvider
     */
    private $userProvider;
    private $cacheDir;

    public function __construct(UserProviderInterface $userProvider, $cacheDir)
    {
        $this->userProvider = $userProvider;
        $this->cacheDir     = $cacheDir;
    }

    /**
     * @param NmotionToken|TokenInterface $token
     *
     * @return NmotionToken
     * @throws AuthenticationException
     */
    private function authenticateByNmotionToken(TokenInterface $token)
    {
        $user = $this->userProvider->loadUserByUsername($token->getUsername());

        if ($user && $user->getPassword() === $token->password) {
            $authenticatedToken = new NmotionToken($user->getRoles());
            $authenticatedToken->setUser($user);
            return $authenticatedToken;
        }

        throw new AuthenticationException('The WSSE authentication failed.');
    }

    /**
     * @param FacebookToken|TokenInterface $token
     *
     * @return FacebookToken
     * @throws AuthenticationException
     */
    private function authenticateByFacebookToken(TokenInterface $token)
    {
        $user = $this->userProvider->loadUserByFacebook($token->facebookAccessToken);

        if (! $user) {
            throw new AuthenticationException('The WSSE authentication failed.');
        }

        $authenticatedToken = new FacebookToken($user->getRoles());
        $authenticatedToken->setUser($user);
        return $authenticatedToken;
    }

    /**
     * @param DeviceToken|TokenInterface $token
     *
     * @return DeviceToken
     * @throws AuthenticationException
     * @throws BadCredentialsException
     */
    private function authenticateByDeviceToken(TokenInterface $token)
    {
        /** @var User $user */
        $user = $this->userProvider->loadUserByDevice($token->deviceIdentity);

        if (! $user) {
            throw new AuthenticationException('The WSSE authentication failed.');
        }

        if ($user->isRegistered()) {
            throw new BadCredentialsException('Registered user MUST authenticate with login and password.');
        }

        $authenticatedToken = new DeviceToken($user->getRoles());
        $authenticatedToken->setUser($user);
        return $authenticatedToken;
    }

    /**
     * @param NmotionToken|TokenInterface $token
     *
     * @return NmotionToken|TokenInterface
     * @throws AuthenticationException
     */
    public function authenticate(TokenInterface $token)
    {
        if ($token instanceof NmotionToken) {
            return $this->authenticateByNmotionToken($token);
        } elseif ($token instanceof FacebookToken) {
            return $this->authenticateByFacebookToken($token);
        } elseif ($token instanceof DeviceToken) {
            return $this->authenticateByDeviceToken($token);
        }

        throw new AuthenticationException('The WSSE authentication failed.');
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof NmotionToken
            || $token instanceof FacebookToken
            || $token instanceof DeviceToken;
    }
}
