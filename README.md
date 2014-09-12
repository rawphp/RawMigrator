# RawMigrator - A Simple Database Migration Service for PHP Applications

## Package Features
- Simple to use migrator
- Create new migration classes
- Migrate database up and down
- Supports migration level control

## Installation

### Composer
RawMigrator is available via [Composer/Packagist](https://packagist.org/packages/rawphp/raw-router).

Add `"rawphp/raw-migrator": "dev-master"` to the require block in your composer.json and then run `composer install`.

```json
{
        "require": {
            "rawphp/raw-migrator": "dev-master"
        }
}
```

You can also simply run the following from the command line:

```sh
composer require rawphp/raw-migrator "dev-master"
```

### Tarball
Alternatively, just copy the contents of the RawMigrator folder into somewhere that's in your PHP `include_path` setting. If you don't speak git or just want a tarball, click the 'zip' button at the top of the page in GitHub.

## Basic Usage

```php
<?php

use RawPHP\RawMigrator\Migrator;
use RawPHP\RawDatabase\Database;

// configuration
$config = array(
    'migration_path'  => '/path/to/migrations/dir/',            // path to migrations directory
    'namespace'       => 'RawPHP\\RawMigrator\\Migrations\\',   // migrations namespace, leave empty if namespaces not used
    'migration_table' => 'migrations',                          // migrations table name
    'class_prefix'    => 'M_',                                  // class prefix for creating new migrations
    'overwrite'       => FALSE,                                 // Whether to overwrite existing migrations of the same name
);

// get new migrator instance
// the migrator expects an instance of RawPHP\RawDatabase\IDatabase class as its first parameter.
// You can use your own database implementation as long as you implement the IDatabase interface.

$database = new Database( $dbConfig );

$migrator = new Migrator( $database, $config );

// create a new migration
$migrator->migrationClass = 'CreateUsersTable';

$migrator->createMigration( );

// update database
$migrator->migrateUp( );

// or to migrate up 1 level
$migrator->levels = 1;
$migrator->migrateUp( );

// migrate database down 1 level
$migrator->levels = 1;
$migrator->migrateDown( );
```

Further usage documentation will be forthcoming.

## License
This package is licensed under the [MIT](https://github.com/rawphp/RawMigrator/blob/master/LICENSE). Read LICENSE for information on the software availability and distribution.

## Contributing

Please submit bug reports, suggestions and pull requests to the [GitHub issue tracker](https://github.com/rawphp/RawMigrator/issues).

## Changelog

#### 11-09-2014
- Initial Code Commit