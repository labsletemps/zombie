<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Seriel\UserBundle\SerielUserBundle;
use Seriel\AppliToolboxBundle\SerielAppliToolboxBundle;
use ZombieBundle\ZombieBundle;
use LeTempsSourcesBundle\LeTempsSourcesBundle;
use FOS\UserBundle\FOSUserBundle;
use Symfony\Bundle\AsseticBundle\AsseticBundle;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
        	new AsseticBundle(),
        	new FOSUserBundle(),
        	new SerielUserBundle(),
        	new SerielAppliToolboxBundle(),
        	new LeTempsSourcesBundle(),
            new ZombieBundle(),
        	new Seriel\GoogleAnalyticsBundle\SerielGoogleAnalyticsBundle(),
            new Seriel\ChartbeatBundle\SerielChartbeatBundle(),
            new Seriel\DonReachBundle\SerielDonReachBundle(),
            new Seriel\DandelionBundle\SerielDandelionBundle(),
            new Seriel\LdaBundle\SerielLdaBundle(),
            new Seriel\RelatedwordBundle\SerielRelatedwordBundle(),
            new Seriel\TrendBundle\SerielTrendBundle(),
            new Seriel\CrossIndicatorBundle\SerielCrossIndicatorBundle(),
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function getRootDir()
    {
        return __DIR__;
    }

    public function getCacheDir()
    {
        return dirname(__DIR__).'/var/cache/'.$this->getEnvironment();
    }

    public function getLogDir()
    {
        return dirname(__DIR__).'/var/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
