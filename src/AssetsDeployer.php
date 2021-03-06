<?php

namespace Pfeyssaguet\AssetsDeployer;

use Composer\Composer;
use Composer\Installer\LibraryInstaller;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;

class AssetsDeployer extends LibraryInstaller
{
    const STRATEGY_COPY = 'copy';
    const STRATEGY_SYMLINK = 'symlink';

    protected $targetDir;

    protected $strategy;

    public function __construct(IOInterface $io, Composer $composer)
    {
        parent::__construct($io, $composer);

        $rootPackage = $this->composer->getPackage();
        $rootPackageExtra = $rootPackage->getExtra();

        if (isset($rootPackageExtra['assets-deployer']) && isset($rootPackageExtra['assets-deployer']['target'])) {
            $options = $rootPackageExtra['assets-deployer'];

            $this->targetDir = $options['target'];
            if (!file_exists($this->targetDir)) {
                mkdir($this->targetDir, 0777, true);
            }
            if ($this->io->isVerbose()) {
                $this->io->write("Assets target dir = " . $this->targetDir);
            }

            if (isset($options['strategy'])) {
                if ($options['strategy'] == self::STRATEGY_COPY) {
                    $this->strategy = self::STRATEGY_COPY;
                } else {
                    $this->strategy = self::STRATEGY_SYMLINK;
                    if ($options['strategy'] != self::STRATEGY_SYMLINK) {
                        throw new \InvalidArgumentException("Assets deployer unknown strategy " . $options['strategy']);
                    }
                }
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
            $this->io->write("  - Deploying assets for <info>" . $package->getPrettyName() . "</info> (<comment>" . $package->getPrettyVersion() . "</comment>)");

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

            if ($this->strategy == self::STRATEGY_SYMLINK) {
                symlink($sourceDir, $target);
            } elseif ($this->strategy == self::STRATEGY_COPY) {
                $directoryIterator = new \RecursiveDirectoryIterator($sourceDir, \RecursiveDirectoryIterator::SKIP_DOTS);
                $iterator = new \RecursiveIteratorIterator($directoryIterator, \RecursiveIteratorIterator::SELF_FIRST);
                foreach ($iterator as $item) {
                    if ($item->isDir()) {
                        mkdir($target . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
                    } else {
                        copy($item, $target . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
                    }
                }
            }
        }
    }
}
