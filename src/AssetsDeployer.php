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

        if (isset($rootPackageExtra['assets-deployer']) && isset($rootPackageExtra['assets-deployer']['target'])) {
            $this->targetDir = $rootPackageExtra['assets-deployer']['target'];
            if (!file_exists($this->targetDir)) {
                mkdir($this->targetDir, 0777, true);
            }
            if ($this->io->isVerbose()) {
                $this->io->write("Assets target dir = " . $this->targetDir);
            }
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
        if ($this->targetDir === null) {
            return;
        }

        $packageExtra = $package->getExtra();

        if (isset($packageExtra['assets-deployer']) && isset($packageExtra['assets-deployer']['source'])) {
            $this->io->write("    Deploying assets for " . $package->getPrettyName() . " " . $package->getPrettyVersion());

            $sourceDir = $this->getInstallPath($package) . DIRECTORY_SEPARATOR . $packageExtra['assets-deployer']['source'];
            if ($this->io->isVerbose()) {
                $this->io->write("    Source dir is " . $sourceDir);
            }

            $target = $this->targetDir . DIRECTORY_SEPARATOR . str_replace(DIRECTORY_SEPARATOR, '-', $package->getName());
            if ($this->io->isVerbose()) {
                $this->io->write("    Target is " . $target);
            }

            if (file_exists($target)) {
                unlink($target);
            }

            symlink($sourceDir, $target);
        }
    }
}
