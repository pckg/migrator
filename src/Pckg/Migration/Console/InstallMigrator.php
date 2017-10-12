<?php

namespace Pckg\Migration\Console;

use Exception;
use Pckg\Concept\Reflect;
use Pckg\Framework\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Throwable;

/**
 * Class InstallMigrator
 *
 * @package Pckg\Migration\Console
 */
class InstallMigrator extends Command
{
    /**
     *
     */
    protected function configure()
    {
        $this->setName('migrator:install')->setDescription('Install migrations from envirtonment')->addOption(
            'only',
            null,
            InputOption::VALUE_OPTIONAL,
            'Install only listed migrations'
        )->addOption('fields', null, null, 'Install only fields (no keys)')
        ;
    }

    /**
     * We
     *
     * @throws Exception
     */
    public function handle()
    {
        $this->app = $this->getApp();

        if (!$this->app) {
            throw new Exception('App name is required in migrator');
        }

        context()->bind(InstallMigrator::class, $this);

        $requestedMigrations = $this->getRequestedMigrations();
        $installedMigrations = (array) $this->getInstalledMigrations();

        $installed = 0;
        $updated   = 0;
        foreach ($requestedMigrations as $requestedMigration) {
            $migrationClass = is_object($requestedMigration) ? get_class($requestedMigration) : $requestedMigration;
            if ($this->option('only') && strpos($migrationClass, $this->option('only')) === false) {
                continue;
            }

            /**
             * @T00D00
             * Implement beforeFirstUp(), beforeUp(), afterUp(), afterFirstUp(), isFirstUp()
             */
            try {
                $this->output('Migration: ' . $requestedMigration, 'info');
                $migration = new $requestedMigration;
                if ($this->option('fields')) {
                    $migration->onlyFields();
                }
                foreach ($migration->dependencies() as $dependency) {
                    if (is_string($dependency)) {
                        $dependency = Reflect::create($dependency);
                    }
                    if ($this->option('fields')) {
                        $dependency->onlyFields();
                    }
                    $this->output(
                        'Dependency: ' . $dependency->getRepository() . ' : ' . get_class($dependency),
                        'info'
                    );
                    $dependency->up();
                }
                $migration->up();
                if (!in_array($requestedMigration, $installedMigrations)) {
                    $migration->afterFirstUp();
                }
                foreach ($migration->partials() as $partial) {
                    if (is_string($partial)) {
                        $partial = Reflect::create($partial);
                    }
                    if ($this->option('fields')) {
                        $partial->onlyFields();
                    }
                    $this->output('Partial: ' . $partial->getRepository() . ' : ' . get_class($partial), 'info');
                    $partial->up();
                }
                $this->output($migration->getRepository() . ' : ' . $requestedMigration, 'info');
                $this->output();
            }
            catch (Throwable $e) {
                dd(exception($e));
            }

            if (in_array($requestedMigration, $installedMigrations)) {
                $updated++;
            } else {
                $installedMigrations[] = $requestedMigration;
                $installed++;
            }
        }

        $this->output('Updated: ' . $updated, 'comment');
        $this->output('Installed: ' . $installed, 'comment');
        $this->output('Total: ' . count($installedMigrations), 'comment');

        $this->putInstalledMigrations($installedMigrations);
    }

    /**
     * @return mixed
     */
    private function getRequestedMigrations()
    {
        return require $this->getConfigPath();
    }

    /**
     * @return array|mixed
     */
    private function getInstalledMigrations()
    {
        return is_file($this->getEnvironmentPath()) ? json_decode(file_get_contents($this->getEnvironmentPath())) : [];
    }

    /**
     * @param array $installedMigrations
     *
     * @return bool|int
     */
    private function putInstalledMigrations(array $installedMigrations = [])
    {
        return file_put_contents($this->getEnvironmentPath(), json_encode($installedMigrations));
    }

    /**
     * @return string
     */
    private function getConfigPath()
    {
        return path('apps') . $this->app . path('ds') . 'config' . path('ds') . 'migrations.php';
    }

    /**
     * @return string
     */
    private function getSrcPath()
    {
        return path('apps') . $this->app . path('ds') . 'src';
    }

    /**
     * @return string
     */
    private function getEnvironmentPath()
    {
        return path('root') . 'storage' . path('ds') . 'environment' . path('ds') . 'migrator' . path(
                'ds'
            ) . $this->app . '.json';
    }
}