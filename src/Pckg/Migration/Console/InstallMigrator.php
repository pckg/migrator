<?php

namespace Pckg\Migration\Console;

use Exception;
use Pckg\Concept\Reflect;
use Pckg\Database\Repository;
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

    protected $app;

    /**
     *
     */
    protected function configure()
    {
        $this->setName('migrator:install')
             ->setDescription('Install migrations from envirtonment')
             ->addOption('only', null, InputOption::VALUE_OPTIONAL, 'Install only listed migrations')
             ->addOption('fields', null, null, 'Install only fields (no keys)')
             ->addOption('indexes', null, null, 'Install only indexes (no keys)')
             ->addOption('yes', null, null, 'Say yes to all questions')
             ->addOption('clear', null, null, 'Clear cache before and after')
             ->addOption('retry', null, InputOption::VALUE_REQUIRED, 'Retry iterations')
             ->addOption('repository', null, InputOption::VALUE_REQUIRED, 'Install only repository');
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
        $installedMigrations = (array)$this->getInstalledMigrations();

        $installed = 0;
        $updated = 0;
        $repository = $this->option('repository');
        $clear = $this->option('clear');
        $retry = min($this->option('retry') ?? 1, 5);
        foreach (range(1, $retry) as $r) {
            if ($clear) {
                context()->get(Repository::class)->getCache()->rebuild();
            }
            if ($r > 1) {
                $this->output('Retry #' . $r);
            }
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
                    $this->output('Migration: ' . $migrationClass, 'info');
                    $migration = is_object($requestedMigration) ? $requestedMigration : (new $requestedMigration());
                    if ($migration->shouldSkip($repository)) {
                        continue;
                    }

                    if ($this->option('fields')) {
                        $migration->onlyFields();
                    }
                    if ($this->option('indexes')) {
                        $migration->onlyIndexes();
                    }
                    foreach ($migration->dependencies() as $dependency) {
                        if (is_string($dependency)) {
                            $dependency = Reflect::create($dependency);
                        }
                        if ($dependency->shouldSkip($repository)) {
                            continue;
                        }
                        if ($this->option('fields')) {
                            $dependency->onlyFields();
                        }
                        if ($this->option('indexes')) {
                            $dependency->onlyIndexes();
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
                        if ($partial->shouldSkip($repository)) {
                            continue;
                        }
                        if ($this->option('fields')) {
                            $partial->onlyFields();
                        }
                        if ($this->option('indexes')) {
                            $partial->onlyIndexes();
                        }
                        $this->output('Partial: ' . $partial->getRepository() . ' : ' . get_class($partial), 'info');
                        $partial->up();
                    }
                    $this->output($migration->getRepository() . ' : ' . $migrationClass, 'info');
                    $this->output();
                } catch (Throwable $e) {
                    ddd(exception($e));
                }

                if (in_array($migrationClass, $installedMigrations)) {
                    $updated++;
                } else {
                    $installedMigrations[] = $migrationClass;
                    $installed++;
                }
            }
        }
        if ($clear) {
            context()->get(Repository::class)->getCache()->rebuild();
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

    private function getEnvironmentPath()
    {
        return path('root') . 'storage' . path('ds') . 'environment' . path('ds') . 'migrator' . path('ds') .
            $this->app . '.json';
    }
}
