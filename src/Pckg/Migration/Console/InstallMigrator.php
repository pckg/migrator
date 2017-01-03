<?php namespace Pckg\Migration\Console;

use Exception;
use Pckg\Concept\Reflect;
use Pckg\Framework\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Throwable;

class InstallMigrator extends Command
{

    protected function configure()
    {
        $this->setName('migrator:install')
             ->setDescription('Install migrations from envirtonment');
    }

    /**
     * We
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
        foreach ($requestedMigrations as $requestedMigration) {
            /**
             * @T00D00
             * Implement beforeFirstUp(), beforeUp(), afterUp(), afterFirstUp(), isFirstUp()
             */
            try {
                $migration = new $requestedMigration;
                foreach ($migration->dependencies() as $dependency) {
                    if (is_string($dependency)) {
                        $dependency = Reflect::create($dependency);
                    }
                    $this->output(' - Dependency: ' . $dependency->getRepository() . ' : ' . get_class($dependency));
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
                    $this->output(' - Partial: ' . $partial->getRepository() . ' : ' . get_class($partial));
                    $partial->up();
                }
                $this->output($migration->getRepository() . ' : ' . $requestedMigration);
                $this->output();
            } catch (Throwable $e) {
                dd(exception($e));
            }

            if (in_array($requestedMigration, $installedMigrations)) {
                $updated++;

            } else {
                $installedMigrations[] = $requestedMigration;
                $installed++;

            }
        }

        $this->output('Updated: ' . $updated);
        $this->output('Installed: ' . $installed);
        $this->output('Total: ' . count($installedMigrations));

        $this->putInstalledMigrations($installedMigrations);
    }

    private function getRequestedMigrations()
    {
        return require $this->getConfigPath();
    }

    private function getInstalledMigrations()
    {
        return is_file($this->getEnvironmentPath())
            ? json_decode(file_get_contents($this->getEnvironmentPath()))
            : [];
    }

    private function putInstalledMigrations(array $installedMigrations = [])
    {
        return file_put_contents($this->getEnvironmentPath(), json_encode($installedMigrations));
    }

    private function getConfigPath()
    {
        return path('apps') . $this->app . path('ds') . 'config' . path('ds') . 'migrations.php';
    }

    private function getSrcPath()
    {
        return path('apps') . $this->app . path('ds') . 'src';
    }

    private function getEnvironmentPath()
    {
        return path('root') . 'storage' . path('ds') . 'environment' . path('ds') . 'migrator' . path(
            'ds'
        ) . $this->app . '.json';
    }

}