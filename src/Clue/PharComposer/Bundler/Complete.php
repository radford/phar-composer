<?php

namespace Clue\PharComposer\Bundler;

use Clue\PharComposer\Bundle;
use Clue\PharComposer\Logger;
use Clue\PharComposer\Package;

use Symfony\Component\Finder\Finder;

class Complete implements BundlerInterface
{
    /**
     * package the bundler is for
     *
     * @type  Package
     */
    private $package;
    /**
     *
     * @type  Logger
     */
    private $logger;

    public function __construct(Package $package, Logger $logger)
    {
        $this->package = $package;
        $this->logger  = $logger;
    }

    /**
     * returns a bundle
     *
     * @return  Bundle
     */
    public function bundle()
    {
        $bundle = new Bundle();
        // Determine the vendor dir relative to the package directory (without any trailing /).
        // If the vendor dir is not under the package directory then this does nothing.
        $vendor = preg_replace(
            '#^' . preg_quote($this->package->getDirectory(),'#') . '(.*?)/?$#',
            '\1',
            $this->package->getPathVendor()
        );
        $iterator = Finder::create()
            ->files()
            ->ignoreVCS(true)
            ->filter($this->package->getBlacklistFilter())
            ->exclude($vendor)
            ->in($this->package->getDirectory());
        $this->logger->log('    Adding whole project directory "' . $this->package->getDirectory() . '"');
        return $bundle->addDir($iterator);
    }

}
