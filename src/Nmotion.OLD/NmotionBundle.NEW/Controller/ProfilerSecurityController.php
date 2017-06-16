<?php
/**
 * @author tiger
 */

namespace Nmotion\NmotionBundle\Controller;

use FOS\UserBundle\Controller\SecurityController as UserBundleSecurityController;

class ProfilerSecurityController extends UserBundleSecurityController
{
    protected function renderLogin(array $data)
    {
        $template = sprintf(
            'NmotionNmotionBundle:Security:profiler_login.html.%s',
            $this->container->getParameter('fos_user.template.engine')
        );

        return $this->container->get('templating')->renderResponse($template, $data);
    }
}
