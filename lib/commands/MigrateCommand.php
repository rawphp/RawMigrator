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
 * @package   RawPHP/RawMigrator/Commands
 * @author    Tom Kaczohca <tom@rawphp.org>
 * @copyright 2014 Tom Kaczocha
 * @license   http://rawphp.org/license.txt MIT
 * @link      http://rawphp.org/
 */

namespace RawPHP\RawMigrator\Commands;

use RawPHP\CR;
use RawPHP\RawConsole\Command;
use RawPHP\RawConsole\Option;
use RawPHP\RawConsole\Type;
use RawPHP\RawMigrator\Migrator;

/**
 * The migrate command for RawConsole.
 * 
 * @category  PHP
 * @package   RawPHP/RawMigrator/Commands
 * @author    Tom Kaczohca <tom@rawphp.org>
 * @copyright 2014 Tom Kaczocha
 * @license   http://rawphp.org/license.txt MIT
 * @link      http://rawphp.org/
 */
class MigrateCommand extends Command
{
    /**
     * @var Migrator
     */
    public $migrator;
    
    /**
     * Configures the command.
     * 
     * @action ON_CONFIGURE_MIGRATE_CMD_ACTION
     */
    public function configure( )
    {
        $this->name          = 'Migrate';
        $this->version       = '1.0.0';
        $this->copyright     = '(c) 2014 RawPHP.org';
        $this->supportSite   = 'https://github.com/rawphp/RawPHP-Framework/issues';
        $this->supportSource = 'https://github.com/rawphp/RawPHP-Framework';
        $this->description   = 'Allows the migration of the database in either 
                                the up or down direction';
        
        $option = new Option( );
        $option->shortCode   = 'd';
        $option->longCode    = 'direction';
        $option->isRequired  = TRUE;
        $option->type        = Type::STRING;
        $option->description = 'The direction you want to migrate in';
        
        $this->options[] = $option;
        
        $option = new Option( );
        $option->shortCode   = 'l';
        $option->longCode    = 'levels';
        $option->isOptional  = TRUE;
        $option->type        = Type::STRING;
        $option->description = 'How many levels you want to migrate';
        
        $this->options[] = $option;
        
        $this->doAction( self::ON_CONFIGURE_MIGRATE_CMD_ACTION );
    }
    
    /**
     * Executes the command action.
     * 
     * @action ON_BEFORE_MIGRATE_CMD_EXECUTE_ACTION
     * @action ON_AFTER_MIGRATE_CMD_EXECUTE_ACTION
     */
    public function execute( )
    {
        $this->doAction( self::ON_BEFORE_MIGRATE_CMD_EXECUTE_ACTION );
        
        $this->migrator = new Migrator( CR::app( )->db );
        
        $config = CR::app( )->config;
        
        $this->migrator->init( $config[ 'migration' ] );
        
        $action    = self::getOption( $this, 'action' )->value;
        $verbose   = self::getOption( $this, 'verbose' )->value;
        
        $direction = self::getOption( $this, 'direction' )->value;
        $levels    = self::getOption( $this, 'levels' )->value;
        
        $this->migrator->verbose = $verbose;
        
        switch( $direction )
        {
            case 'up':
                $this->migrator->migrateUp( $levels );
                break;
            
            case 'down':
                $this->migrator->migrateDown( $levels );
                break;
            
            default:
                // do nothing
                break;
        }
        
        $this->doAction( self::ON_AFTER_MIGRATE_CMD_EXECUTE_ACTION );
    }
    
    const ON_CONFIGURE_MIGRATE_CMD_ACTION       = 'on_configure_migrate_cmd_action';
    const ON_BEFORE_MIGRATE_CMD_EXECUTE_ACTION  = 'on_before_migrate_cmd_execute_action';
    const ON_AFTER_MIGRATE_CMD_EXECUTE_ACTION   = 'on_after_migrate_cmd_execute_action';
}