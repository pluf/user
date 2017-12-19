<?php
/*
 * This file is part of Pluf Framework, a simple PHP Application Framework.
 * Copyright (C) 2010-2020 Phoinex Scholars Co. (http://dpq.co.ir)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\IncompleteTestError;

require_once 'Pluf.php';

Pluf::loadFunction('Pluf_Shortcuts_GetFormForModel');

/**
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 */
class User_ModelTest extends TestCase
{

    /**
     * @beforeClass
     */
    public static function createDataBase ()
    {
        Pluf::start(dirname(__FILE__) . '/../conf/pluf.config.php');
        $db = Pluf::db();
        $schema = Pluf::factory('Pluf_DB_Schema', $db);
        $models = array(
                'Group',
                'User',
                'Role',
                'User_Message'
        );
        
        foreach ($models as $model) {
            $schema->model = Pluf::factory($model);
            $schema->dropTables();
            if (true !== ($res = $schema->createTables())) {
                throw new Exception($res);
            }
        }
        
        $user = new User();
        $user->login = 'test';
        $user->first_name = 'test';
        $user->last_name = 'test';
        $user->email = 'toto@example.com';
        $user->setPassword('test');
        $user->active = true;
        if (true !== $user->create()) {
            throw new Exception();
        }
    }

    /**
     * @afterClass
     */
    public static function removeDatabses ()
    {
        $db = Pluf::db();
        $schema = Pluf::factory('Pluf_DB_Schema', $db);
        $models = array(
                'Group',
                'User',
                'Role',
                'User_Message'
        );
        foreach ($models as $model) {
            $schema->model = Pluf::factory($model);
            $schema->dropTables();
        }
    }

    /**
     * @test
     */
    public function shouldPossibleCreateNew ()
    {
        $user = new User();
        $user->login = 'Random'.rand();
        $user->email = 'Hi@test.com';
        Test_Assert::assertTrue($user->create(), 'Impossible to create user');
    }

    /**
     * @test
     */
    public function shouldPossibleToGetMessages ()
    {
        $user = new User(1);
        $mess = $user->get_user_message_list();
        Test_Assert::assertEquals(0, $mess->count());
    }
    
    /**
     * @test
     */
    public function shouldPossibleToRoles ()
    {
        $user = new User(1);
        $roles = $user->get_roles_list();
        Test_Assert::assertEquals(0, $roles->count());
    }
    
    /**
     * @test
     */
    public function shouldPossibleToGetGroups ()
    {
        $user = new User(1);
        $groups = $user->get_groups_list();
        Test_Assert::assertEquals(0, $groups->count());
    }

    /**
     * @expectedException Pluf_Exception
     * @test
     */
    public function testUniqueLogin ()
    {
        $user = new User();
        $user->login = 'test';
        $user->first_name = 'test';
        $user->last_name = 'test';
        $user->email = 'toto@example.com';
        $user->setPassword('test');
        $user->active = true;
        // Test user already exists
        $user->create();
    }

    /**
     * TODO: maso, 2017: must throw Pluf_Exception_Form 
     * 
     * @expectedException Pluf_Exception
     * @test
     */
    public function testValidationUnique ()
    {
        // Test user already exists
        $user = new User();
        $user->login = 'test';
        $user->first_name = 'test';
        $user->last_name = 'test';
        $user->email = 'toto@example.com';
        $user->setPassword('test');
        $user->active = true;
        
        $form = Pluf_Shortcuts_GetFormForModel($user, $user->getData(), array());
        $errors = $form->save();
    }
}


