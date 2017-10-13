<?php

namespace Pckg\Migration\Provider;

use Pckg\Framework\Provider;
use Pckg\Migration\Console\InstallMigrator;
use Pckg\Migration\Console\UpdateMigrator;

/**
 * Class Migration
 *
 * @package Pckg\Migration\Provider
 */
class Migration extends Provider
{

    /**
     * @return array
     */
    public function consoles()
    {
        return [
            UpdateMigrator::class,
            InstallMigrator::class,
        ];
    }
}