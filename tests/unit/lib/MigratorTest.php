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
    protected $migrator = NULL;
    
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
        global $db;
        
        parent::setUpBeforeClass();
        
        self::$db = $db;
        
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
        global $config;
        
        $this->migrator = new Migrator( self::$db );
        
        $this->migrator->init( $config[ 'migration' ] );
        
        $this->migrator->migrationPath = TEST_MIGRATIONS_DIR;
    }
    
    /**
     * Cleanup after each test.
     */
    protected function tearDown()
    {
        self::$db->dropTable( 'migrate_1' );
        self::$db->dropTable( 'migrate_2' );
        self::$db->dropTable( 'migrate_3' );
        self::$db->dropTable( $this->migrator->migrationTable );
        
        $this->_cleanupMigrations( );
        
        $this->migrator = NULL;
    }
    
    /**
     * Test migrator instatiation.
     */
    public function testMigratorInstanceNotNull( )
    {
        $this->assertNotNull( $this->migrator );
    }
    
    /**
     * Test creating a new migration.
     */
    public function testCreateMigration()
    {
        $migrationName = "test_migration_1";
        
        $this->migrator->migrationClass = $migrationName;
        
        $this->assertTrue( $this->migrator->createMigration( ) );
        
        $files = scandir( TEST_MIGRATIONS_DIR );
        
        $this->assertEquals( 6, count( $files ) );
    }
    
    /**
     * Test creating a migration table.
     */
    public function testCreateMigrationTable()
    {
        $this->assertTrue( $this->migrator->createMigrationTable( ) );
        
        $this->assertTrue( self::$db->tableExists( $this->migrator->migrationTable ) );
    }
    
    /**
     * Test getting migrations.
     */
    public function testGetMigrations()
    {
        $migrations = $this->migrator->getMigrations();
        
        $this->assertEquals( 3, count( $migrations ) );
    }
    
    /**
     * Test getting new migrations.
     */
    public function testGetNewMigrations()
    {
        $this->migrator->migrateUp( );
        
        $migrations = $this->migrator->getNewMigrations( );
        
        $this->assertEquals( 0, count( $migrations ) );
    }
    
    /**
     * Test migrating up.
     */
    public function testMigrateUp()
    {
        $this->migrator->migrateUp( );
        
        $this->assertTrue( self::$db->tableExists( 'migrate_1' ) );
        $this->assertTrue( self::$db->tableExists( 'migrate_2' ) );
        $this->assertTrue( self::$db->tableExists( 'migrate_3' ) );
    }
    
    /**
     * Test migrating down.
     */
    public function testMigrateDown()
    {
        $this->migrator->migrateUp( );
        
        $this->assertTrue( self::$db->tableExists( 'migrate_1' ) );
        $this->assertTrue( self::$db->tableExists( 'migrate_2' ) );
        $this->assertTrue( self::$db->tableExists( 'migrate_3' ) );
        
        $this->migrator->migrateDown( );
        
        $this->assertFalse( self::$db->tableExists( 'migrate_1' ) );
        $this->assertFalse( self::$db->tableExists( 'migrate_2' ) );
        $this->assertFalse( self::$db->tableExists( 'migrate_3' ) );
    }
    
    /**
     * Test migration table is empty after down migration.
     */
    public function testMigrateDownClearsMigrationTable( )
    {
        $this->migrator->migrateUp( );
        
        $this->migrator->migrateDown( );
        
        $table = $this->migrator->migrationTable;
        
        $query = "SELECT * FROM $table";
        
        $this->assertEmpty( self::$db->query( $query ) );
    }
    
    /**
     * Test migrating up 1 level.
     */
    public function testMigrateUp1Level( )
    {
        $this->assertEquals( Migrator::MIGRATE_ALL, $this->migrator->levels );
        $this->assertFalse( self::$db->tableExists( $this->migrator->migrationTable ) );
        $this->assertFalse( self::$db->tableExists( 'migrate_1' ) );
        $this->assertFalse( self::$db->tableExists( 'migrate_2' ) );
        $this->assertFalse( self::$db->tableExists( 'migrate_3' ) );
        
        $this->migrator->migrateUp( 1 );
        
        echo 'After Migrating Up 1 Level: ' . PHP_EOL;
        Database::arrayDump( $this->_getMigrationTableContent( ) );
        
        $this->assertTrue( self::$db->tableExists( 'migrate_1' ) );
        $this->assertFalse( self::$db->tableExists( 'migrate_2' ) );
        $this->assertFalse( self::$db->tableExists( 'migrate_3' ) );
    }
    
    /**
     * Test migrating down 1 level.
     */
    public function testMigrateDown1Level( )
    {
        $this->assertEquals( Migrator::MIGRATE_ALL, $this->migrator->levels );
        $this->assertFalse( self::$db->tableExists( $this->migrator->migrationTable ) );
        $this->assertFalse( self::$db->tableExists( 'migrate_1' ) );
        $this->assertFalse( self::$db->tableExists( 'migrate_2' ) );
        $this->assertFalse( self::$db->tableExists( 'migrate_3' ) );
        
        $this->migrator->migrateUp( Migrator::MIGRATE_ALL );
        
        echo 'After Migrating Up ALL Levels: ' . PHP_EOL;
        Database::arrayDump( $this->_getMigrationTableContent( ) );
        
        $this->assertTrue( self::$db->tableExists( 'migrate_1' ) );
        $this->assertTrue( self::$db->tableExists( 'migrate_2' ) );
        $this->assertTrue( self::$db->tableExists( 'migrate_3' ) );
        
        echo 'After Migrating Down 1 Level: ' . PHP_EOL;
        $this->migrator->migrateDown( 1 );
        
        Database::arrayDump( $this->_getMigrationTableContent( ) );
        
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
    
    /**
     * Helper method to get migration table content.
     * 
     * @return array migration table results
     */
    private function _getMigrationTableContent( )
    {
        $query = "SELECT * FROM " . $this->migrator->migrationTable;
        
        return self::$db->query( $query );
    }
}
