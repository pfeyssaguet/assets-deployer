<?php

namespace Pfeyssaguet\AssetsDeployer;

use Composer\Composer;
use Composer\Installer\LibraryInstaller;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;

class AssetsDeployer extends LibraryInstaller
{
    public function __construct(IOInterface $io, Composer $composer)
    {
        parent::__construct($io, $composer);

        $rootPackage = $this->composer->getPackage();
        $rootPackageExtra = $rootPackage->getExtra();

        if (isset($rootPackageExtra['assets-deployer'])) {
            $assetsDir = $rootPackageExtra['assets-deployer']['target'];
            //if (!file_exists($rootPackage-> $assetsDir))
        }

        $this->io->write("Root package target dir = " . $rootPackage->getTargetDir());
    }

    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        parent::install($repo, $package);
        $this->io->write("Install assets for package " . $package->getName());

        $rootPackage = $this->composer->getPackage();
        $rootPackageExtra = $rootPackage->getExtra();

        if (isset($rootPackageExtra['assets-deployer'])) {
            $assetsDir = $rootPackageExtra['assets-deployer']['target'];
            //if (!file_exists($rootPackage-> $assetsDir))
        }

        $this->io->write("Root package target dir = " . $rootPackage->getTargetDir());

        $packageExtra = $package->getExtra();

        if (isset($packageExtra['assets-deployer'])) {

        }
    }

    public function update(InstalledRepositoryInterface $repo, PackageInterface $initial, PackageInterface $target)
    {
        parent::update($repo, $initial, $target);
        $this->io->write("Updating assets for package " . $target->getName());
    }
}
