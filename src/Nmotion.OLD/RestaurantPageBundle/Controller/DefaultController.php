<?php

namespace Nmotion\RestaurantPageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use Nmotion\NmotionBundle\Entity\MenuCategory;
use Nmotion\NmotionBundle\Controller\BaseRestController;

class DefaultController extends BaseRestController
{
    private function getRestaurantFromRequest(Request $request)
    {
        $currentHost = $request->getHttpHost();
        // Find restaurant or send 404
        $currentRestaurant = $this->getDoctrine()->getRepository('Nmotion\NmotionBundle\Entity\Restaurant')->findOneBy(['nmSiteUrl' => $currentHost, 'nmWebsite' => true]);
        if ($currentRestaurant === null) {
            //var_dump($this->generateUrl('nmotion_landing_page_index')); die;
            header("HTTP/1.1 301 Moved Permanently");
            header('Location: http:' . $this->generateUrl('nmotion_landing_page_index'));
            die;
        }

        return $currentRestaurant;
    }

    public function indexAction(Request $request)
    {
        $currentHostname = $this->getRestaurantFromRequest($request);
        $currentName = strtolower(substr($request->getHttpHost(), 0, strpos($request->getHttpHost(), '.')));
        if ($this->get('templating')->exists('NmotionRestaurantPageBundle:'.$currentName.':restaurant.html.twig')) {
            setlocale(LC_MONETARY, 'da_DK');
            $categoryViewModel = array();
            $restaurant = $this->getRestaurantFromRequest($request);
            $categories = $this->getDoctrine()
                ->getRepository('Nmotion\NmotionBundle\Entity\MenuCategory')
                ->findBy(['restaurant' => $restaurant, 'visible' => true]);

            foreach($categories as $category) {
                $categoryViewModel[] = array('category' => $category, 'meals' => $this->getDoctrine()->getRepository('Nmotion\NmotionBundle\Entity\Meal')->findBy(['menuCategory' => $category, 'visible' => true]));
            }

            global $kernel;
            if ('AppCache' == get_class($kernel)) {
                $kernel = $kernel->getKernel();
            }
            $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');

            $configRepository = $em->getRepository('NmotionNmotionBundle:Config');
            $configSalesTax   = $configRepository->findOneBy(['name' => 'sales_tax']);

            $salesTaxPercent = (float) $configSalesTax->getValue();

            return $this->render('NmotionRestaurantPageBundle:'.$currentName.':restaurant.html.twig', array('restaurant' => $currentHostname, 'tax_rate' => $salesTaxPercent, 'categoryViewModel' => $categoryViewModel));
        } else {
            return $this->render('NmotionRestaurantPageBundle::restaurant.html.twig', array('restaurant' => $currentHostname));
        }
    }

    public function menuAction(Request $request)
    {
        setlocale(LC_MONETARY, 'da_DK');
        $categoryViewModel = array();
        $restaurant = $this->getRestaurantFromRequest($request);
        $categories = $this->getDoctrine()
                            ->getRepository('Nmotion\NmotionBundle\Entity\MenuCategory')
                            ->findBy(['restaurant' => $restaurant, 'visible' => true]);

        foreach($categories as $category) {
            $categoryViewModel[] = array('category' => $category, 'meals' => $this->getDoctrine()->getRepository('Nmotion\NmotionBundle\Entity\Meal')->findBy(['menuCategory' => $category, 'visible' => true]));
        }

        return $this->render('NmotionRestaurantPageBundle::menu.html.twig',
            array('restaurant' => $restaurant, 'categoryViewModel' => $categoryViewModel));
    }
}
