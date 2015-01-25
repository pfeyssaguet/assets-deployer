<?php

namespace Pfeyssaguet\AssetsDeployer;

use Composer\Composer;
use Composer\Installer\LibraryInstaller;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;

class AssetsDeployer extends LibraryInstaller
{
    protected $targetDir;

    public function __construct(IOInterface $io, Composer $composer)
    {
        parent::__construct($io, $composer);

        $rootPackage = $this->composer->getPackage();
        $rootPackageExtra = $rootPackage->getExtra();

        if (isset($rootPackageExtra['assets-deployer'])) {
            $this->targetDir = $rootPackageExtra['assets-deployer']['target'];
            if (!file_exists($this->targetDir)) {
                mkdir($this->targetDir, 0777, true);
            }
            $this->io->write("Assets target dir = " . $this->targetDir);
        }
    }

    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        parent::install($repo, $package);
        $this->deployAssets($package);
    }

    public function update(InstalledRepositoryInterface $repo, PackageInterface $initial, PackageInterface $target)
    {
        parent::update($repo, $initial, $target);
        $this->deployAssets($target);
    }

    private function deployAssets(PackageInterface $package)
    {
        $packageExtra = $package->getExtra();

        if (isset($packageExtra['assets-deployer'])) {
            $this->io->write("Install or update assets for package " . $package->getName());

            $sourceDir = 'vendor/' . $package->getName() . '/' . $packageExtra['assets-deployer']['source'];
            $this->io->write("Source dir is " . $sourceDir);

            $target = $this->targetDir . '/' . $package->getName();
            $this->io->write("Target is " . $target);

            if (file_exists($target)) {
                unlink($target);
            }
            symlink($target, $sourceDir);
        }
    }
}
