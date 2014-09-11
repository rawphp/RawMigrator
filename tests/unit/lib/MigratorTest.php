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
 * @package   RawPHP/RawMigrator/Tests
 * @author    Tom Kaczohca <tom@rawphp.org>
 * @copyright 2014 Tom Kaczocha
 * @license   http://rawphp.org/license.txt MIT
 * @link      http://rawphp.org/
 */

namespace RawPHP\RawMigrator\Tests;

use RawPHP\RawMigrator\Migrator;
use RawPHP\RawDatabase\Database;

/**
 * The Migrator tests.
 * 
 * @category  PHP
 * @package   RawPHP/RawMigrator/Tests
 * @author    Tom Kaczohca <tom@rawphp.org>
 * @copyright 2014 Tom Kaczocha
 * @license   http://rawphp.org/license.txt MIT
 * @link      http://rawphp.org/
 */
class MigratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The migrator instance.
     * 
     * @var Migrator
     */
    protected static $migrator = NULL;
    
    /**
     * The database to test with.
     * 
     * @var Database 
     */
    protected static $db = NULL;
    
    /**
     * Setup before the test suite run.
     * 
     * @global IDatabase $db     database instance
     * @global array     $config migrator configuration array
     */
    public static function setUpBeforeClass()
    {
        global $db, $config;
        
        parent::setUpBeforeClass();
        
        self::$db = $db;
        
        self::$migrator = new Migrator( self::$db, $config[ 'migration' ] );
        
        self::$migrator->migrationPath = TEST_MIGRATIONS_DIR;
        
        self::$db->dropTable( 'migrations' );
        self::$db->dropTable( 'migrate_1' );
        self::$db->dropTable( 'migrate_2' );
        self::$db->dropTable( 'migrate_3' );
    }
    
    /**
     * Setup before each test.
     */
    public function setUp()
    {
        parent::setUp();
    }
    
    /**
     * Cleanup after each test.
     */
    protected function tearDown()
    {
        self::$db->dropTable( 'migrate_1' );
        self::$db->dropTable( 'migrate_2' );
        self::$db->dropTable( 'migrate_3' );
        self::$db->dropTable( self::$migrator->migrationTable );
        
        $this->_cleanupMigrations( );
    }
    
    /**
     * Test migrator instatiation.
     */
    public function testMigratorInstanceNotNull( )
    {
        $this->assertNotNull( self::$migrator );
    }
    
    /**
     * Test creating a new migration.
     */
    public function testCreateMigration()
    {
        $migrationName = "test_migration_1";
        
        self::$migrator->migrationClass = $migrationName;
        
        $this->assertTrue( self::$migrator->createMigration( ) );
        
        $files = scandir( TEST_MIGRATIONS_DIR );
        
        $this->assertEquals( 6, count( $files ) );
    }
    
    /**
     * Test creating a migration table.
     */
    public function testCreateMigrationTable()
    {
        $this->assertTrue( self::$migrator->createMigrationTable( ) );
        
        $this->assertTrue( self::$db->tableExists( self::$migrator->migrationTable ) );
    }
    
    /**
     * Test getting migrations.
     */
    public function testGetMigrations()
    {
        $migrations = self::$migrator->getMigrations();
        
        $this->assertEquals( 3, count( $migrations ) );
    }
    
    /**
     * Test getting new migrations.
     */
    public function testGetNewMigrations()
    {
        self::$migrator->migrateUp( );
        
        $migrations = self::$migrator->getNewMigrations( );
        
        $this->assertEquals( 0, count( $migrations ) );
    }
    
    /**
     * Test migrating up.
     */
    public function testMigrateUp()
    {
        self::$migrator->migrateUp( );
        
        $this->assertTrue( self::$db->tableExists( 'migrate_1' ) );
        $this->assertTrue( self::$db->tableExists( 'migrate_2' ) );
        $this->assertTrue( self::$db->tableExists( 'migrate_3' ) );
    }
    
    /**
     * Test migrating down.
     */
    public function testMigrateDown()
    {
        self::$migrator->migrateUp( );
        
        $this->assertTrue( self::$db->tableExists( 'migrate_1' ) );
        $this->assertTrue( self::$db->tableExists( 'migrate_2' ) );
        $this->assertTrue( self::$db->tableExists( 'migrate_3' ) );
        
        self::$migrator->migrateDown( );
        
        $this->assertFalse( self::$db->tableExists( 'migrate_1' ) );
        $this->assertFalse( self::$db->tableExists( 'migrate_2' ) );
        $this->assertFalse( self::$db->tableExists( 'migrate_3' ) );
    }
    
    /**
     * Test migration table is empty after down migration.
     */
    public function testMigrateDownClearsMigrationTable( )
    {
        self::$migrator->migrateUp( );
        
        self::$migrator->migrateDown( );
        
        $table = self::$migrator->migrationTable;
        
        $query = "SELECT * FROM $table";
        
        $this->assertEmpty( self::$db->query( $query ) );
    }
    
    /**
     * Test migrating up 1 level.
     */
    public function testMigrateUp1Level( )
    {
        self::$migrator->levels = 1;
        self::$migrator->migrateUp( );
        
        $this->assertTrue( self::$db->tableExists( 'migrate_1' ) );
        $this->assertFalse( self::$db->tableExists( 'migrate_2' ) );
    }
    
    /**
     * Test migrating down 1 level.
     */
    public function testMigrateDown1Level( )
    {
        self::$migrator->levels = Migrator::MIGRATE_ALL;
        
        self::$migrator->migrateUp( );
        
        $this->assertTrue( self::$db->tableExists( 'migrate_1' ) );
        $this->assertTrue( self::$db->tableExists( 'migrate_2' ) );
        $this->assertTrue( self::$db->tableExists( 'migrate_3' ) );
        
        self::$migrator->levels = 1;
        self::$migrator->migrateDown( );
        
        $this->assertTrue( self::$db->tableExists( 'migrate_1' ) );
        $this->assertTrue( self::$db->tableExists( 'migrate_2' ) );
        $this->assertFalse( self::$db->tableExists( 'migrate_3' ) );
    }
    
    /**
     * Helper method to cleanup migratons.
     */
    private function _cleanupMigrations( )
    {
        $files = scandir( TEST_MIGRATIONS_DIR );
        
        $existing = array(
            'empty',
            'M_09634060_test_migration.php',
            'M_09634217_test_migration.php',
            'M_09634265_test_migration.php',
        );
        
        foreach( $files as $file )
        {
            if ( '.' !== $file && '..' !== $file && !in_array( $file, $existing ) )
            {
                unlink( TEST_MIGRATIONS_DIR . $file );
            }
        }
    }
}
