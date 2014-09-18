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
 * PHP version 5.4
 * 
 * @category  PHP
 * @package   RawPHP/RawMigrator/Test
 * @author    Tom Kaczohca <tom@rawphp.org>
 * @copyright 2014 Tom Kaczocha
 * @license   http://rawphp.org/license.txt MIT
 * @link      http://rawphp.org/
 */

namespace RawPHP\RawMigrator\Test;

use RawPHP\RawDatabase\IDatabase;
use RawPHP\RawMigrator\Migrator;

/**
 * Test Case used with database migration testing.
 * 
 * @category  PHP
 * @package   RawPHP/RawMigrator/Test
 * @author    Tom Kaczohca <tom@rawphp.org>
 * @copyright 2014 Tom Kaczocha
 * @license   http://rawphp.org/license.txt MIT
 * @link      http://rawphp.org/
 */
class DBTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var IDatabase
     */
    public static $db           = NULL;
    /**
     * @var Migrator
     */
    public static $migrator     = NULL;
    
    /**
     * Setup done before test suite run.
     * 
     * @global array     $config configuration array
     * @global IDatabase $db     the database instance
     */
    public static function setUpBeforeClass()
    {
        global $config, $db;
        self::$db = $db;
        
        parent::setUpBeforeClass();
        
        self::$migrator = new Migrator( $db );
        self::$migrator->init( $config[ 'migration' ] );
        self::$migrator->verbose = TRUE;
    }
    
    /**
     * Setup for a test.
     */
    protected function setUp()
    {
        parent::setUp( );
        
        self::createDatabase( );
    }
    
    /**
     * Cleanup after a test
     */
    protected function tearDown()
    {
        parent::tearDown( );
        
        self::dropDatabase( );
    }
    
    /**
     * Creates the database by migrating UP.
     */
    protected static function createDatabase( )
    {
        self::$migrator->migrateUp( Migrator::MIGRATE_ALL );
    }
    
    /**
     * Deletes the database by migrating DOWN.
     */
    protected static function dropDatabase( )
    {
        self::$migrator->migrateDown( Migrator::MIGRATE_ALL );
    }
}