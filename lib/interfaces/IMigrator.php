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

/**
 * This migrator interface.
 * 
 * @category  PHP
 * @package   RawPHP/RawMigrator
 * @author    Tom Kaczocha <tom@rawphp.org>
 * @copyright 2014 Tom Kaczocha
 * @license   http://rawphp.org/license.txt MIT
 * @link      http://rawphp.org/
 */
interface IMigrator
{
    /**
     * Initialises the migrator.
     * 
     * @param array $config configuration array
     * 
     * @action ON_INIT_ACTION
     * 
     * @throws \InvalidArgumentException if a configuration is missing
     */
    public function init( $config );
    
    /**
     * Creates a new migration class file.
     * 
     * @filter ON_CREATE_MIGRATION_FILTER
     * 
     * @action ON_CREATE_MIGRATION_ACTION
     * 
     * @return bool TRUE on success, FALSE on failure
     * 
     * @throws MigrationException if configuration is missing
     */
    public function createMigration( );
    
    /**
     * Creates the migration template file code.
     * 
     * @param string $name new migration class name
     * 
     * @filter ON_GET_MIGRATION_TEMPLATE_FILTER
     * 
     * @return string class template
     */
    public function getMigrationTemplate( $name );
    
    /**
     * Creates the migration database table if it doesn't exist.
     * 
     * @action ON_CREATE_MIGRATION_TABLE_ACTION
     * 
     * @return bool TRUE on success, FALSE on failure
     * 
     * @throws RawException if migration table is not set
     */
    public function createMigrationTable( );
    
    /**
     * Returns a list of migration files.
     * 
     * @filter ON_GET_MIGRATIONS_FILTER
     * 
     * @return array list of available migrations
     */
    public function getMigrations( );
    
    /**
     * Returns a list of new migrations.
     * 
     * @filter ON_GET_NEW_MIGRATIONS_FILTER
     * 
     * @return array list of migrations
     */
    public function getNewMigrations( );
    
    /**
     * Returns a list of applied migrations.
     * 
     * @filter ON_GET_APPLIED_MIGRATIONS_FILTER
     * 
     * @return array list of applied migrations
     */
    public function getAppliedMigrations( );
    
    /**
     * Runs the UP migration.
     * 
     * @param mixed $levels optional migration levels size
     * 
     * @action ON_MIGRATE_UP_ACTION
     * 
     * @throws MigrationException on failed transaction
     */
    public function migrateUp( $levels = NULL );
    
    /**
     * Runs the DOWN migration.
     * 
     * @param mixed $levels optional migration levels size
     * 
     * @action ON_MIGRATE_DOWN_ACTION
     * 
     * @throws MigrationException on failed transaction
     */
    public function migrateDown( $levels = NULL );
}