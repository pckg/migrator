<?php namespace Pckg\Migration\Console;

use Pckg\Framework\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class InstallMigrator extends Command
{

    protected function configure()
    {
        $this->setName('migrator:install')
             ->setDescription('Install migrations from envirtonment')
             ->addOption('platform', null, InputOption::VALUE_REQUIRED);
    }

    /**
     * We
     */
    public function handle()
    {
        $this->app = $this->getApp();

        $requestedMigrations = $this->getRequestedMigrations();
        $installedMigrations = (array)$this->getInstalledMigrations();

        $installed = 0;
        $updated = 0;
        foreach ($requestedMigrations as $requestedMigration) {
            $this->output('Creating ' . $requestedMigration);
            $migration = new $requestedMigration;
            $this->output('Installing @ ' . $migration->getRepository());
            $migration->up();
            $this->output('Installed.');
            $this->output();

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