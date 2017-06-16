<?php

namespace Nmotion\LandingPageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        return $this->render('NmotionLandingPageBundle:Default:index.html.twig');

        /*$restaurantManager = $this->container->get('nmotion.restaurant_manager');
        if ($restaurantManager->getIsNmotionMainSite())
        {
        }
        else 
        {
            echo $restaurantManager->getCurrentRestaurant()->getName();
        }*/
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
