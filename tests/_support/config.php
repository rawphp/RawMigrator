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

use RawPHP\RawMigrator\Migrator;

$config = array();

/*******************************************************************************
 * Test Database
 * -----------------------------------------------------------------------------
 * These are the database settings for testing.
 * 
 ******************************************************************************/
$config[ 'test_db' ][ 'db_name' ] = 'raw_migrator_test';
$config[ 'test_db' ][ 'db_user' ] = 'root';
$config[ 'test_db' ][ 'db_pass' ] = '';
$config[ 'test_db' ][ 'db_host' ] = 'localhost';


/*******************************************************************************
 * Database Migration
 * -----------------------------------------------------------------------------
 * These are the migration settings for the application.
 * 
 ******************************************************************************/
$config[ 'migration' ][ 'migration_path' ]   = dirname( dirname( __FILE__ ) ) . DS . '_output' . DS;
$config[ 'migration' ][ 'namespace' ]        = 'RawPHP\\RawMigrator\\Migrations';
$config[ 'migration' ][ 'migration_table' ]  = 'migrations';

// Migrator::STYLE_CAMEL_CASE or Migrator::STYLE_UNDERSCORE
$config[ 'migration' ][ 'class_name_style' ] = Migrator::STYLE_CAMEL_CASE;
$config[ 'migration' ][ 'class_prefix' ]     = 'M';
$config[ 'migration' ][ 'overwrite' ]        = FALSE;


return $config;