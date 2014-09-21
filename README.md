# RawMigrator - A Simple Database Migration Service for PHP Applications

[![Build Status](https://travis-ci.org/rawphp/RawMigrator.svg?branch=master)](https://travis-ci.org/rawphp/RawMigrator) [![Coverage Status](https://coveralls.io/repos/rawphp/RawMigrator/badge.png?branch=master)](https://coveralls.io/r/rawphp/RawMigrator?branch=master)
[![Latest Stable Version](https://poser.pugx.org/rawphp/raw-migrator/v/stable.svg)](https://packagist.org/packages/rawphp/raw-migrator) [![Total Downloads](https://poser.pugx.org/rawphp/raw-migrator/downloads.svg)](https://packagist.org/packages/rawphp/raw-migrator) [![Latest Unstable Version](https://poser.pugx.org/rawphp/raw-migrator/v/unstable.svg)](https://packagist.org/packages/rawphp/raw-migrator) [![License](https://poser.pugx.org/rawphp/raw-migrator/license.svg)](https://packagist.org/packages/rawphp/raw-migrator)

## Package Features
- Simple to use migrator
- Create new migration classes
- Migrate database up and down
- Supports migration level control

## Installation

### Composer
RawMigrator is available via [Composer/Packagist](https://packagist.org/packages/rawphp/raw-router).

Add `"rawphp/raw-migrator": "0.*@dev"` to the require block in your composer.json and then run `composer install`.

```json
{
        "require": {
            "rawphp/raw-migrator": "0.*@dev"
        }
}
```

You can also simply run the following from the command line:

```sh
composer require rawphp/raw-migrator "0.*@dev"
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
    'migration_path'   => '/path/to/migrations/dir/',            // path to migrations directory
    'namespace'        => 'RawPHP\\RawMigrator\\Migrations\\',   // migrations namespace, leave empty if namespaces not used
    'migration_table'  => 'migrations',                          // migrations table name
    'class_name_style' => Migrator::STYLE_CAMEL_CASE;            // Migrator::STYLE_UNDERSCORE
    'class_prefix'     => 'M',                                   // class prefix for creating new migrations
    'overwrite'        => FALSE,                                 // Whether to overwrite existing migrations of the same name
);

// get new migrator instance
// the migrator expects an instance of RawPHP\RawDatabase\IDatabase class as its first parameter.
// You can use your own database implementation as long as you implement the IDatabase interface.

$database = new Database( );
$database->init( $dbConfig );

// create new instance
$migrator = new Migrator( $database );

// initialise migrator
$migrator->init( $config );

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


// NOTE: If using the RawConsole package to run these migrations, you need to
// add 'RawPHP\\RawMigrator\\Commands\\' namespace to the console configuration file.

```

Further usage documentation will be forthcoming.

## License
This package is licensed under the [MIT](https://github.com/rawphp/RawMigrator/blob/master/LICENSE). Read LICENSE for information on the software availability and distribution.

## Contributing

Please submit bug reports, suggestions and pull requests to the [GitHub issue tracker](https://github.com/rawphp/RawMigrator/issues).

## Changelog

#### 22-09-2014
- Updated for PHP 5.3.

#### 20-09-2014
- Changed 'namespace' to 'migration_namespace' migrator configuration.

#### 20-09-2014
- Added the migration command.
- Replaced php array configuration with yaml
- Fixed MigrationException namespace.

#### 18-09-2014
- Updated to work with the latest rawphp/rawbase package.
- Replaced php array configuration with yaml

#### 17-09-2014
- Added DBTestCase class to be used with the migrator when testing database.
- Added Class Naming Style to Migrator options. Choose between CamelCase and Underscore.

#### 16-09-2014
- Added optional $levels parameter to `migrateUp( )` and `migrateDown( )` in Migrator.

#### 13-09-2014
- Implemented the hook system
- Moved component configuration from constructor to `init()`

#### 11-09-2014
- Initial Code Commit
