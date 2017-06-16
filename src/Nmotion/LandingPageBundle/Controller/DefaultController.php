<?php

namespace Nmotion\LandingPageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('NmotionLandingPageBundle:Default:index.html.twig');
    }
    public function aboutAction()
    {
        return $this->render('NmotionLandingPageBundle:Default:about.html.twig');
    }
    public function businessAction()
    {
        return $this->render('NmotionLandingPageBundle:Default:business.html.twig');
    }
    public function contactsAction()
    {
        return $this->render('NmotionLandingPageBundle:Default:contacts.html.twig');
    }
}
