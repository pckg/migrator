# Migrator

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/799bca98727a49a2807b5a613440a19c)](https://www.codacy.com/app/schtr4jh/migrator?utm_source=github.com&utm_medium=referral&utm_content=pckg/migrator&utm_campaign=badger)

![Build status](https://github.com/pckg/migrator/workflows/Pckg%20Migrator%20CI/badge.svg)

# Usage

Register `Pckg\Migration\Provider\Migration` provider in your application. This is only needed if you don't
use `Pckg\Framework\Provider\Framework`.

```
...
use Pckg\Migration\Provider\Migration;
...
public function providers()
{
    return [
        Migration::class,
    ];
}
```

## Consoles

All commands are app-scoped and need to be prefixed with `php console $appName`.

`migrator:install` - Install migrations from environment

- `--only=SomeMigration` - Install only listed migrations
- `--fields` - Install only fields (no indexes/key)
- `--indexes` - Install only indexes/key (no fields)
- `--yes` - Say yes to all questions
- `--clear` - Clear cache before and after
- `--retry=1` - Retry iterations
- `--repository=default` - Install only repository

## Create a migration

```
<?php

namespace Foo\Migration;

use Foo\Migration\SomeMigration;

class SomeMigration extends \Pckg\Framework\Provider
{

    public function up()
    {
        $clients = $this->table('clients');
        $clients->deletable();
        $clients->varchar('name');
        $clients->json('props');
        
        $projects = $this->table('projects');
        $projects->timeable();
        $projects->deletable();
        $projects->integer('client_id')->references('clients');
        $projects->varchar('name');
        
        $this->save();
    }
}
```

## Apply migration

List your migrations in `./config/migrations.php` or `./app/$app/config/migrations.php`.

```
<?php

use Foo\Migration\SomeMigration;

return [
    SomeMigration::class,
];
```

Or add your migration to the `Provider`.

```
<?php

namespace Foo\Provider;

use Pckg\Framework\Provider;
use Foo\Migration\SomeMigration;

class Feature extends Provider
{

    public function migrations()
    {
        return [
            SomeMigration::class,
        ];
    }
}
```

And then execute migrations:

`# php console $appName migrator:install`
