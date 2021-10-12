<?php

namespace MewesK\TwigSpreadsheetBundle\Tests\Functional\Fixtures;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Class TestAppKernel.
 */
class TestAppKernel extends Kernel
{
    private ?string $cacheDir = null;

    private ?string $logDir = null;

    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        return [
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new \MewesK\TwigSpreadsheetBundle\MewesKTwigSpreadsheetBundle(),
            new \MewesK\TwigSpreadsheetBundle\Tests\Functional\Fixtures\TestBundle\TestBundle(),
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(sprintf('%s/config/config_%s.yml', __DIR__, $this->getEnvironment()));
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheDir()
    : ?string
    {
        return $this->cacheDir;
    }

    /**
     * @param string $cacheDir
     */
    public function setCacheDir(string $cacheDir)
    {
        $this->cacheDir = $cacheDir;
    }

    /**
     * {@inheritdoc}
     */
    public function getLogDir()
    : ?string
    {
        return $this->logDir;
    }

    /**
     * @param string $logDir
     */
    public function setLogDir(string $logDir)
    {
        $this->logDir = $logDir;
    }
}
