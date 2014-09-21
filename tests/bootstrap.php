<?php

/**
 * This file is part of RawPHP - a PHP Framework.
 * 
 * Copyright (c) 2014 RawPHP.org
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 * 
 * PHP version 5.3
 * 
 * @category  PHP
 * @package   RawPHP/RawMigrator
 * @author    Tom Kaczohca <tom@rawphp.org>
 * @copyright 2014 Tom Kaczocha
 * @license   http://rawphp.org/license.txt MIT
 * @link      http://rawphp.org/
 */

use RawPHP\RawDatabase\Mysql;
use RawPHP\RawYaml\Yaml;

defined( 'DS' )                  || define( 'DS', DIRECTORY_SEPARATOR );
defined( 'SUPPORT_DIR' )         || define( 'SUPPORT_DIR', dirname( __FILE__ ) 
                                            . DS . '_support' . DS );
defined( 'OUTPUT_DIR' )          || define( 'OUTPUT_DIR', dirname( __FILE__ ) 
                                            . DS . '_output' . DS );
defined( 'TEST_MIGRATIONS_DIR' ) || define( 'TEST_MIGRATIONS_DIR', SUPPORT_DIR 
                                            . 'migrations' . DS );

require_once dirname( dirname( __FILE__ ) ) . DS . 'vendor' . DS . 'autoload.php';

// get config
$yaml = new Yaml( );
$config = $yaml->load( SUPPORT_DIR . 'config.yml' );
$config[ 'migration' ][ 'migration_path' ] = fixPath( $config[ 'migration' ][ 'migration_path' ] );

$db = new Mysql( );
$db->init( $config[ 'test_db' ] );

// load migration files
loadMigrationFiles( );

echo PHP_EOL . PHP_EOL . '************* BOOTSTRAP ********************' . PHP_EOL . PHP_EOL;


/**
 * Helper function to load migration files.
 */
function loadMigrationFiles( )
{
    foreach ( scandir( TEST_MIGRATIONS_DIR ) as $file )
    {
        if ( '.' !== $file && '..' !== $file )
        {
            include_once TEST_MIGRATIONS_DIR . $file;
        }
    }
}

/**
 * Helper function to cleanup migration path in configuration.
 * 
 * @param string $path migration path
 * 
 * @return string migration path
 */
function fixPath( $path )
{
    // fix path
    if ( FALSE !== strstr( $path, '%OUTPUT_DIR%' ) )
    {
        $path = str_replace( '%OUTPUT_DIR%', OUTPUT_DIR, $path );
    }

    $len = strlen( $path );

    if ( DS !== $path[ $len - 1 ] )
    {
        $path .= DS;
    }
    
    return $path;
}