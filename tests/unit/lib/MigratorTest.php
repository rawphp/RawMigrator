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
use RawPHP\RawMigrator\Test\DBTestCase;

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
class MigratorTest extends DBTestCase
{
    /**
     * Setup before the test suite run.
     * 
     * @global IDatabase $db     database instance
     * @global array     $config migrator configuration array
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        
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
        
        self::$migrator = new Migrator( self::$db );
        
        self::$migrator->init( $config[ 'migration' ] );
        
        self::$migrator->migrationPath = TEST_MIGRATIONS_DIR;
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
        
        self::$migrator = NULL;
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
        $migrationName = "TestMigration1";
        
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
        $this->assertEquals( Migrator::MIGRATE_ALL, self::$migrator->levels );
        $this->assertFalse( self::$db->tableExists( self::$migrator->migrationTable ) );
        $this->assertFalse( self::$db->tableExists( 'migrate_1' ) );
        $this->assertFalse( self::$db->tableExists( 'migrate_2' ) );
        $this->assertFalse( self::$db->tableExists( 'migrate_3' ) );
        
        self::$migrator->migrateUp( 1 );
        
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
        $this->assertEquals( Migrator::MIGRATE_ALL, self::$migrator->levels );
        $this->assertFalse( self::$db->tableExists( self::$migrator->migrationTable ) );
        $this->assertFalse( self::$db->tableExists( 'migrate_1' ) );
        $this->assertFalse( self::$db->tableExists( 'migrate_2' ) );
        $this->assertFalse( self::$db->tableExists( 'migrate_3' ) );
        
        self::$migrator->migrateUp( Migrator::MIGRATE_ALL );
        
        echo 'After Migrating Up ALL Levels: ' . PHP_EOL;
        Database::arrayDump( $this->_getMigrationTableContent( ) );
        
        $this->assertTrue( self::$db->tableExists( 'migrate_1' ) );
        $this->assertTrue( self::$db->tableExists( 'migrate_2' ) );
        $this->assertTrue( self::$db->tableExists( 'migrate_3' ) );
        
        echo 'After Migrating Down 1 Level: ' . PHP_EOL;
        self::$migrator->migrateDown( 1 );
        
        Database::arrayDump( $this->_getMigrationTableContent( ) );
        
        $this->assertTrue( self::$db->tableExists( 'migrate_1' ) );
        $this->assertTrue( self::$db->tableExists( 'migrate_2' ) );
        $this->assertFalse( self::$db->tableExists( 'migrate_3' ) );
    }
    
    /**
     * Test creating a new migration with CamelCase style.
     */
    public function testCreateMigrationWithCamelCaseStyle()
    {
        $migrationName = "TestMigration1";
        
        self::$migrator->migrationClass = $migrationName;
        self::$migrator->migrationClassStyle = Migrator::STYLE_CAMEL_CASE;
        
        $this->assertTrue( self::$migrator->createMigration( ) );
        
        $files = scandir( TEST_MIGRATIONS_DIR );
        
        foreach( $files as $file )
        {
            if ( FALSE !== strstr( $file, $migrationName ) )
            {
                $this->assertFalse( strpos( $file, '_' ) );
                break;
            }
        }
    }
    
    /**
     * Test creating a new migration with CamelCase style.
     */
    public function testCreateMigrationWithUnderscoreStyle()
    {
        // M_12345_test_migration1
        $migrationName = "TestMigration1";
        
        self::$migrator->migrationClass = $migrationName;
        self::$migrator->migrationClassStyle = Migrator::STYLE_UNDERSCORE;
        
        $this->assertTrue( self::$migrator->createMigration( ) );
        
        $files = scandir( TEST_MIGRATIONS_DIR );
        
        foreach( $files as $file )
        {
            if ( FALSE !== strstr( $file, $migrationName ) )
            {
                $this->assertTrue( 0 < strpos( $file, '_' ) );
                
                //$parts = preg_split('/(?=[A-Z])/', $file, -1, PREG_SPLIT_NO_EMPTY);
                $parts = explode( '_', $file );
                $this->assertEquals( 4, count( $parts ) );
                
                break;
            }
        }
    }
    
    /**
     * Helper method to cleanup migratons.
     */
    private function _cleanupMigrations( )
    {
        $files = scandir( TEST_MIGRATIONS_DIR );
        
        $existing = array(
            'empty',
            'M09634060TestMigration.php',
            'M09634217TestMigration.php',
            'M09634265TestMigration.php',
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
        $query = "SELECT * FROM " . self::$migrator->migrationTable;
        
        return self::$db->query( $query );
    }
}