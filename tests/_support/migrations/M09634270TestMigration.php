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
 * @package   RawPHP/RawMigrator/Migrations/Tests
 * @author    Tom Kaczohca <tom@rawphp.org>
 * @copyright 2014 Tom Kaczocha
 * @license   http://rawphp.org/license.txt MIT
 * @link      http://rawphp.org/
 */

namespace RawPHP\RawMigrator\TestMigrations;

use RawPHP\RawMigrator\Migration;
use RawPHP\RawDatabase\IDatabase;

/**
 * Migration test.
 * 
 * @category  PHP
 * @package   RawPHP/RawMigrator/Migrations/Tests
 * @author    Tom Kaczohca <tom@rawphp.org>
 * @copyright 2014 Tom Kaczocha
 * @license   http://rawphp.org/license.txt MIT
 * @link      http://rawphp.org/
 */
class M09634270TestMigration extends Migration
{
    protected $table = 'migrate_4';
    protected $column = 'table_text';
    
    /**
     * Create the test table.
     * 
     * @param IDatabase $db the database instance
     */
    public function safeMigrateUp( IDatabase $db )
    {
        $columns = array(
            'table_id' => 'INTEGER(11) PRIMARY KEY AUTO_INCREMENT NOT NULL',
            'table_num' => 'INTEGER(11) NOT NULL',
        );
        
        $db->createTable( $this->table, $columns );
        
        $db->addColumn( $this->table, $this->column, 'VARCHAR(32) NOT NULL' );
    }
    
    /**
     * Drop the table.
     * 
     * @param IDatabase $db the database instance
     */
    public function safeMigrateDown( IDatabase $db )
    {
        $db->dropColumn( $this->table, $this->column );
        
        $db->dropTable( $this->table );
    }
}