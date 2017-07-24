<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new SMS\EstablishmentBundle\SMSEstablishmentBundle(),
            new SMS\StudyPlanBundle\SMSStudyPlanBundle(),
            new SMS\UserBundle\SMSUserBundle(),
            new SMS\AdministrativeBundle\SMSAdministrativeBundle(),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            new Vich\UploaderBundle\VichUploaderBundle(),
            new Sg\DatatablesBundle\SgDatatablesBundle(),
            new DatatablesBundle\DatatablesBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new JMS\TranslationBundle\JMSTranslationBundle(),
            new SMS\UserSpaceBundle\SMSUserSpaceBundle(),
            new SMS\SchoolBundle\SMSSchoolBundle(),
            new SMS\PaymentBundle\SMSPaymentBundle(),
            new SMS\StoreBundle\SMSStoreBundle(),
            new Knp\Bundle\SnappyBundle\KnpSnappyBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'), true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
