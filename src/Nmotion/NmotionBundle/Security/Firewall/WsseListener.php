<?php

/**
 * @author tiger
 */

namespace Nmotion\NmotionBundle\Security\Firewall;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;

use Nmotion\NmotionBundle\Security\Authentication\Provider\WsseProvider;
use Nmotion\NmotionBundle\Security\Authentication\Token\DeviceToken;
use Nmotion\NmotionBundle\Security\Authentication\Token\FacebookToken;
use Nmotion\NmotionBundle\Security\Authentication\Token\NmotionToken;

class WsseListener implements ListenerInterface
{
    protected $securityContext;

    /**
     * @var WsseProvider
     */
    protected $authenticationManager;

    public function __construct(
        SecurityContextInterface $securityContext,
        AuthenticationManagerInterface $authenticationManager
    ) {
        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
    }

    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if (! $request->headers->has('auth')) {
            return;
        }

        if (1 === preg_match('/^NmotionToken (.*)\|(\w*)$/', $request->headers->get('auth'), $matches)) {
            $token = new NmotionToken();
            $token->setUser($matches[1]);
            $token->email    = $matches[1];
            $token->password = $matches[2];
        } elseif (1 === preg_match('/^FacebookToken (\w*)$/', $request->headers->get('auth'), $matches)) {
            $token = new FacebookToken();
            $token->facebookAccessToken = $matches[1];
        } elseif (1 === preg_match('/^DeviceToken (\w*)$/', $request->headers->get('auth'), $matches)) {
            $token = new DeviceToken();
            $token->deviceIdentity = $matches[1];
        } else {
            return;
        }

        try {
            $authToken = $this->authenticationManager->authenticate($token);
            $this->securityContext->setToken($authToken);
        } catch (AuthenticationException $failed) {
            throw new HttpException(401, 'Unauthorized');
        }
    }
}
