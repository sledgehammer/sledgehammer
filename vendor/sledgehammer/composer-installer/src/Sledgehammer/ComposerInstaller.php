<?php
/**
 * ComposerInstaller
 */
namespace Sledgehammer;

use Composer\Package\PackageInterface;
use Composer\Installer\LibraryInstaller;
/**
 * Install sledgehammer modules via composer.
 * @link http://getcomposer.org 
 */
class ComposerInstaller extends LibraryInstaller {

    /**
    * {@inheritDoc}
    */
    public function getInstallPath(PackageInterface $package) {
        list($vendor, $name) = explode('/', $package->getName());
        return "sledgehammer/".strtolower($name);
    }

    /**
     * {@inheritDoc}
     */
    public function supports($packageType) {
        return $packageType == 'sledgehammer-module';
    }
}