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
 * @package   RawPHP/RawMigrator
 * @author    Tom Kaczohca <tom@rawphp.org>
 * @copyright 2014 Tom Kaczocha
 * @license   http://rawphp.org/license.txt MIT
 * @link      http://rawphp.org/
 */

namespace RawPHP\RawMigrator;

use RawPHP\RawBase\Component;
use RawPHP\RawMigrator\IMigrator;
use RawPHP\RawDatabase\IDatabase;
use RawPHP\RawBase\Exceptions\RawException;

/**
 * This class handles database migration.
 * 
 * @category  PHP
 * @package   RawPHP/RawMigrator
 * @author    Tom Kaczocha <tom@rawphp.org>
 * @copyright 2014 Tom Kaczocha
 * @license   http://rawphp.org/license.txt MIT
 * @link      http://rawphp.org/
 */
class Migrator extends Component implements IMigrator
{
    const MIGRATE_ALL           = 'all';
    const MIGRATE_DEFAULT_TABLE = 'migrations';
    
    public $overwrite           = FALSE;
    public $migrationPath       = '';
    public $classPrefix         = 'M_';
    public $migrationTable      = 'migrations';
    public $migrationNamespace  = '';
    
    public $db                  = NULL;
    
    public $levels              = self::MIGRATE_ALL;
    public $migrationClass      = NULL;
    public $verbose             = FALSE;
    
    /**
     * Constructs a new migrator instance.
     * 
     * @param IDatabase $database the database instance
     * @param array     $config   configuration array
     */
    public function __construct( IDatabase $database, $config = array() )
    {
        parent::__construct( $config );
        
        $this->db = $database;
        
        $this->init( $config );
    }
    
    /**
     * Initialises the migrator.
     * 
     * @param array $config configuration array
     * 
     * @throws \InvalidArgumentException if a configuration is missing
     */
    public function init( $config )
    {
        if ( !isset( $config[ 'migration_path' ] ) )
        {
            throw new \InvalidArgumentException( 'Missing migration_path parameter' );
        }
        
        foreach ( $config as $key => $value )
        {
            switch( $key )
            {
                case 'migration_path':
                    $this->migrationPath = $value;
                    break;
                
                case 'namespace':
                    $this->migrationNamespace = $value;
                    break;
                
                case 'class_prefix':
                    $this->classPrefix = $value;
                    break;
                
                case 'migration_table':
                    $this->migrationTable = $value;
                    break;
                
                case 'overwrite':
                    $this->overwrite = $value;
                    break;
                
                case 'levels':
                    $this->levels = $value;
                    break;
                
                case 'name':
                    $this->migrationClass = $value;
                    break;
                
                case 'verbose':
                    $this->verbose = $value === 'true' ? TRUE : FALSE;
                    break;
                
                default:
                    // Do nothing
                    break;
            }
        }
    }
    
    /**
     * Creates a new migration class file.
     * 
     * @return bool TRUE on success, FALSE on failure
     * 
     * @throws RawException if configuration is missing
     */
    public function createMigration( )
    {
        $date = new \DateTime();
        
        $className  = $this->classPrefix;
        $className .= substr( $date->format( 'U' ), 2 );
        $className .= '_' . $this->migrationClass;
        
        $template = $this->getMigrationTemplate( $className );
        
        $path = $this->migrationPath . $className . '.php';
        
        if ( file_exists( $path ) && $this->overwrite )
        {
            return file_put_contents( $path, $template );
        }
        elseif ( file_exists( $path ) )
        {
            throw new RawException( 'Migration file already exists' );
        }
        
        $result = file_put_contents( $path, $template );
        
        return FALSE !== $result;
    }
    
    /**
     * Creates the migration template file code.
     * 
     * @param string $name new migration class name
     * 
     * @return string class template
     */
    public function getMigrationTemplate( $name )
    {
        $template = '<?php' . PHP_EOL . PHP_EOL;
        
        if ( '' !== $this->migrationNamespace )
        {
            $template .= 'namespace ' . $this->migrationNamespace . ';' . PHP_EOL . PHP_EOL;
        }
        
        $template .= 'use RawPHP\RawMigrator;' . PHP_EOL . PHP_EOL;
        
        $template .= 'class ' . $name . ' extends Migration' . PHP_EOL;
        $template .= '{' . PHP_EOL;

        $template .= '    /**' . PHP_EOL;
        $template .= '     * Implement database changes here.' . PHP_EOL;
        $template .= '     */' . PHP_EOL;
        $template .= '    public function migrateUp()' . PHP_EOL;
        $template .= '    {' . PHP_EOL;
        $template .= '        ' . PHP_EOL;
        $template .= '    }' . PHP_EOL . PHP_EOL;

        $template .= '    /**' . PHP_EOL;
        $template .= '     * Implement reverting changes here.' . PHP_EOL;
        $template .= '     */' . PHP_EOL;
        $template .= '    public function migrateDown()' . PHP_EOL;
        $template .= '    {' . PHP_EOL;
        $template .= '        ' . PHP_EOL;
        $template .= '    }' . PHP_EOL . PHP_EOL;

        $template .= '    /**' . PHP_EOL;
        $template .= '     * Alternatively to <code>migrateUp()</code>, this method will' . PHP_EOL;
        $template .= '     * use transactions (if available) to process the database changes.' .
                          PHP_EOL;
        $template .= '     */' . PHP_EOL;
        $template .= '    public function safeMigrateUp()' . PHP_EOL;
        $template .= '    {' . PHP_EOL;
        $template .= '        ' . PHP_EOL;
        $template .= '    }' . PHP_EOL . PHP_EOL;

        $template .= '    /**' . PHP_EOL;
        $template .= '     * Alternatively to <code>migrateDown()</code>, this method will' . 
                          PHP_EOL;
        $template .= '     * use transactions (if available) to revert changes.' . PHP_EOL;
        $template .= '     */' . PHP_EOL;
        $template .= '    public function safeMigrateDown()' . PHP_EOL;
        $template .= '    {' . PHP_EOL;
        $template .= '        ' . PHP_EOL;
        $template .= '    }' . PHP_EOL;
        $template .= '}';

        return $template;
    }
    
    /**
     * Creates the migration database table if it doesn't exist.
     * 
     * @return bool TRUE on success, FALSE on failure
     * 
     * @throws RawException if migration table is not set
     */
    public function createMigrationTable( )
    {
        if ( empty( $this->migrationTable ) )
        {
            throw new RawException( 'Migration table name must be set' );
        }
        
        $table = array(
            'migration_id'           => 'INTEGER(11) PRIMARY KEY AUTO_INCREMENT NOT NULL',
            'migration_name'         => 'VARCHAR(128) NOT NULL',
            'migration_date_applied' => 'BIGINT(20) NOT NULL',
        );
        
        if ( TRUE === $this->db->createTable( $this->migrationTable, $table ) )
        {
            return $this->db->addIndex( $this->migrationTable, array( 'migration_name' ) );
        }
        
        return FALSE;
    }
    
    /**
     * Returns a list of migration files.
     * 
     * @return array list of available migrations
     */
    public function getMigrations( )
    {
        $migrations = array();
        
        $dir = opendir( $this->migrationPath );
        
        while ( ( $file = readdir( $dir ) ) !== FALSE )
        {
            if ( '.' !== $file && '..' !== $file )
            {
                $migrations[] = str_replace( '.php', '', $file );
            }
        }
        
        closedir( $dir );
        
        return $migrations;
    }
    
    /**
     * Returns a list of new migrations.
     * 
     * @return array list of migrations
     */
    public function getNewMigrations( )
    {
        // check if migration table exists
        $exists = $this->db->tableExists( $this->migrationTable );
        
        if ( !$exists )
        {
            $this->createMigrationTable( );
        }
        
        $query = "SELECT * FROM $this->migrationTable";
        
        $applied = $this->db->query( $query );
        
        $migrations = $this->getMigrations();
        
        $list = array();
        
        foreach( $migrations as $migration )
        {
            $done = FALSE;
            $migToFind = $this->migrationNamespace . '\\' . $migration;
            
            foreach( $applied as $mig )
            {
                if ( $mig[ 'migration_name' ] === $migToFind )
                {
                    $done = TRUE;
                    break;
                }
            }
            
            if ( !$done )
            {
                $list[] = $migration;
            }
        }
        
        return $list;
    }
    
    /**
     * Returns a list of applied migrations.
     * 
     * @return array list of applied migrations
     */
    public function getAppliedMigrations( )
    {
        $query = "SELECT * FROM $this->migrationTable";
        
        $applied = $this->db->query( $query );
        
        $list = array();
        
        foreach( $applied as $mig )
        {
            $list[] = $mig[ 'migration_name' ];
        }
        
        $list = array_reverse( $list );
        
        return $list;
    }
    
    /**
     * Runs the UP migration.
     * 
     * @throws RawException on failed transaction
     */
    public function migrateUp( )
    {
        $newMigrations = $this->getNewMigrations( );
        
        $i = 0;
        
        if ( self::MIGRATE_ALL === $this->levels || $this->levels > count( $newMigrations ) )
        {
            $this->levels = count( $newMigrations );
        }
        
        while ( $i < $this->levels )
        {
            $class = $this->migrationNamespace . '\\' . $newMigrations[ $i ];
            
            if ( !class_exists( $class ) )
            {
                $cls = 'RawPHP\\RawMigrator\\Migrations\\' . $newMigrations[ $i ];
                
                if ( class_exists( $cls ) )
                {
                    $class = $cls;
                }
                else
                {
                    throw new RawException( 'Migration class: ' . $class . ' - not found' );
                }
            }
            
            $migration = new $class( );
            
            $method = new \ReflectionMethod( $class, 'migrateUp' );
            
            // run migrateUp
            if ( $class === $method->getdeclaringClass()->name )
            {
                $migration->migrateUp( $this->db );
                
                $this->_addMigrationRecord( $class );
            }
            else
            {
                $method = new \ReflectionMethod( $class, 'safeMigrateUp' );
                
                if ( $class === $method->getDeclaringClass()->name )
                {
                    try
                    {
                        $this->db->startTransaction( );
                        
                        // turn off auto commit
                        $this->db->setTransactionAutocommit( FALSE );
                        
                        // run safeMigrateUp
                        $migration->safeMigrateUp( $this->db );
                        
                        // update migrations table with the new applied migration
                        $this->_addMigrationRecord( $class );
                        
                        // commit transaction
                        $this->db->commitTransaction( );
                    }
                    catch ( Exception $e )
                    {
                        // roleback transaction
                        $this->db->rollbackTransaction( );
                        
                        throw $e;
                    }
                }
            }
            
            $i++;
            
            if ( $this->levels === $i )
            {
                return;
            }
        }
    }
    
    /**
     * Runs the DOWN migration.
     * 
     * @throws RawException on failed transaction
     */
    public function migrateDown( )
    {
        $migrations = $this->getAppliedMigrations( );
        
        $i = 0;
        
        if ( self::MIGRATE_ALL === $this->levels || $this->levels > count( $migrations ) )
        {
            $this->levels = count( $migrations );
        }
        
        while ( $i < $this->levels )
        {
            $class = $migrations[ $i ];
            
            $migration = new $class( );
            
            $method = new \ReflectionMethod( $class, 'migrateDown' );
            
            // run migrateDown
            if ( $class === $method->getDeclaringClass( )->name )
            {
                $migration->migrateDown( $this->db );
                
                // delete migration from table
                if ( FALSE === $this->_deleteMigrationRecord( $class ) )
                {
                    throw new RawException( 'Failed to delete migration record: ' . $class );
                }
            }
            else // run safeMigrateDown
            {
                $method = new \ReflectionMethod( $class, 'safeMigrateDown' );
            
                if ( $class === $method->getDeclaringClass( )->name )
                {
                    try
                    {
                        // start transaction
                        $this->db->startTransaction( );

                        // turn off auto commit
                        $this->db->setTransactionAutocommit( FALSE );

                        // run safeMigrateDown
                        $migration->safeMigrateDown( $this->db );

                        // delete migration record
                        if ( FALSE === $this->_deleteMigrationRecord( $class ) )
                        {
                            throw new RawException( 'Failed to delete migration record: ' . $class );
                            
                            $this->db->rollbackTransaction();
                        }

                        // commit transaction
                        $this->db->commitTransaction( );
                    }
                    catch ( Exception $e )
                    {
                        // rollback transaction
                        $this->db->rollbackTransaction( );

                        throw $e;
                    }
                }
            }
            
            $i++;
            
            if ( $this->levels === $i )
            {
                return;
            }
        }
    }
    
    /**
     * Inserts an applied migration into the database.
     * 
     * @param string $class migration name
     * 
     * @return bool TRUE on success, FALSE on failure
     */
    private function _addMigrationRecord( $class )
    {
        $name = $this->db->prepareString( $class );
        $tm   = new \DateTime();
        $tm   = $tm->getTimestamp();
        
        // update migrations table with the new applied migration
        $query = "INSERT INTO $this->migrationTable 
                 ( migration_name, migration_date_applied ) VALUES ( ";
        
        $query .= "'$name', ";
        $query .= "$tm";
        
        $query .= " )";
        
        $this->db->lockTables( $this->migrationTable );
        
        $id = $this->db->insert( $query );
        
        if ( $this->verbose )
        {
            echo 'Migrating: ' . $class . PHP_EOL;
        }
        
        $this->db->unlockTables();
        
        return FALSE !== $id;
    }
    
    /**
     * Deletes a migration entry from the database.
     * 
     * @param string $class migration name
     * 
     * @return bool TRUE on success, FALSE on failure
     */
    private function _deleteMigrationRecord( $class )
    {
        $name = $this->db->prepareString( $class );
        
        $query = "DELETE FROM $this->migrationTable WHERE migration_name = '$name'";
        
        $this->db->lockTables( $this->migrationTable );
        
        $result = $this->db->execute( $query );
        
        if ( $this->verbose )
        {
            echo 'Deleting: ' . $class . PHP_EOL;
        }
        
        $this->db->unlockTables();
        
        return $result === 1;
    }
}
