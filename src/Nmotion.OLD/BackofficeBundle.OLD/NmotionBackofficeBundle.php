<?php

namespace Nmotion\BackofficeBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Nmotion\NmotionBundle\DependencyInjection\Security\Factory\WsseFactory;
use Symfony\Bundle\SecurityBundle\DependencyInjection\SecurityExtension;

class NmotionBackofficeBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        /** @var $extension SecurityExtension */
        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new WsseFactory());
    }
}
