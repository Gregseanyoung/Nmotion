<?php

namespace Nmotion\BackofficeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $optimizedDefault = $this->get('kernel')->getEnvironment() !== 'local';
        $appCacheDefault = $this->get('kernel')->getEnvironment() !== 'local';

        return $this->render(
            'NmotionBackofficeBundle::index.html.twig',
            [
                'name'      => 'bugaga',
                'appcache' => $this->getRequest()->get('appcache', $appCacheDefault),
                'optimized' => $this->getRequest()->get('optimized', $optimizedDefault)
            ]
        );
    }
}
