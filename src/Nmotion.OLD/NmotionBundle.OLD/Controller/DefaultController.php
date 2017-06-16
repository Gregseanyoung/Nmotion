<?php

namespace Nmotion\NmotionBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Mobile_Detect;
use Nmotion\NmotionBundle\Entity\Config;

class DefaultController extends BaseRestController
{
    const APP_URL_FOR_IOS = 'http://itunes.com/apps/nmotion';
    const APP_URL_FOR_ANDROID = 'https://play.google.com/store/apps/details?id=com.nmotion.android';

    private function getAppUrls()
    {
        $configToArrayMapping = [
            'android_app_url' => 'androidUrl',
            'ios_app_url'     => 'iosUrl'
        ];

        $urls = [];

        /** @var Config[] $configs */
        $configs = $this->getRepository('Config')
                   ->findBy(['name' => ['android_app_url', 'ios_app_url']]);

        if (count($configs) < 2) {
            throw new \RuntimeException('Could not get config for market urls');
        }

        $urls[$configToArrayMapping[$configs[0]->getName()]] = $configs[0]->getValue();
        $urls[$configToArrayMapping[$configs[1]->getName()]] = $configs[1]->getValue();

        return $urls;
    }

    public function indexAction()
    {
        $appUrls = $this->getAppUrls();
        return $this->render('NmotionNmotionBundle:Default:index.html.twig', $appUrls);
    }

    public function loginAction()
    {
        return $this->render(
            'NmotionNmotionBundle:Default:login.html.twig',
            [
                'isMobile' => (new Mobile_Detect())->isMobile()
            ]
        );
    }

    public function faqAction()
    {
        return $this->render('NmotionNmotionBundle:Default:faq.html.twig');
    }

    public function termsAction()
    {
        return $this->render('NmotionNmotionBundle:Default:terms.html.twig');
    }

    public function contactsAction()
    {
        return $this->render('NmotionNmotionBundle:Default:contacts.html.twig');
    }

    public function downloadAction()
    {
        $mobileDetect = new Mobile_Detect();
        $appUrls = $this->getAppUrls();

        switch (true) {
            case $mobileDetect->is('AndroidOS'):
                return new RedirectResponse($appUrls['androidUrl']);
            case $mobileDetect->is('iOS'):
                return new RedirectResponse($appUrls['iosUrl']);
            default:
                return $this->render('NmotionNmotionBundle:Default:download.html.twig', $appUrls);
        }
    }

    public function facebookDownloadAction()
    {
        $appUrls = $this->getAppUrls();
        return $this->render('NmotionNmotionBundle:Default:facebook-download.html.twig', $appUrls);
    }
}
