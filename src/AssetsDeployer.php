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
        $this->io->write("Install assets for package " . $package->getName());

        $packageExtra = $package->getExtra();

        if (isset($packageExtra['assets-deployer'])) {
            $sourceDir = $packageExtra['assets-deployer']['source'];
            $target = $this->targetDir . '/' . $package->getName();
            if (file_exists($target)) {
                unlink($target);
            }
            symlink($target, $sourceDir);
        }
    }

    public function update(InstalledRepositoryInterface $repo, PackageInterface $initial, PackageInterface $target)
    {
        parent::update($repo, $initial, $target);
        $this->io->write("Updating assets for package " . $target->getName());
    }
}
