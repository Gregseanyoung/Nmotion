<?php
// @codingStandardsIgnoreFile

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    protected function isDevEnvironment()
    {
        $devEnvironments = [
            'demo',
            'demo_test',
            'dev',
            'dev_test',
            'dev2',
            'dev2_test',
            'stage',
            'stage_test',
            'stage2',
            'stage2_test',
            'local',
            'local_test',
            'test',
            'nami_test'
        ];

        return in_array($this->getEnvironment(), $devEnvironments);
    }

    protected function getContainerBaseClass()
    {
        if ($this->isDevEnvironment()) {
            //return '\JMS\DebuggingBundle\DependencyInjection\TraceableContainer';
        }

        return parent::getContainerBaseClass();
    }

    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new JMS\AopBundle\JMSAopBundle(),
            new JMS\DiExtraBundle\JMSDiExtraBundle($this),
            new JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle($this),
            new FOS\RestBundle\FOSRestBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new Liuggio\ExcelBundle\LiuggioExcelBundle(),

            new Tiger\TigerJsBundle\TigerJsBundle(),
            new Nmotion\NmotionBundle\NmotionNmotionBundle(),
            new Nmotion\ApiBundle\NmotionApiBundle(),
            new Nmotion\BackofficeBundle\NmotionBackofficeBundle(),
	    new Nmotion\RestaurantPageBundle\NmotionRestaurantPageBundle(),
            new Nmotion\LandingPageBundle\NmotionLandingPageBundle(),
        );

        if ($this->isDevEnvironment()) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
         //   $bundles[] = new JMS\DebuggingBundle\JMSDebuggingBundle($this);
        }

        if (substr($this->getEnvironment(), 0, 4) === 'prod') {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
