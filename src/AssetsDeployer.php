<?php

namespace Pfeyssaguet\AssetsDeployer;

use Composer\Installer\LibraryInstaller;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;

class AssetsDeployer extends LibraryInstaller
{
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

        fwrite(STDERR, "Root package target dir = " . $rootPackage->getTargetDir());

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
