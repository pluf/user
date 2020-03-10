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
use Pluf\Test\TestCase;
use PHPUnit\Framework\IncompleteTestError;

/**
 *
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 */
class User_ModelTest extends TestCase
{

    /**
     *
     * @beforeClass
     */
    public static function createDataBase()
    {
        Pluf::start(__DIR__ . '/../conf/config.php');
        $db = Pluf::db();
        $schema = Pluf::factory('Pluf_DB_Schema', $db);
        $models = array(
            'User_Group',
            'User_Account',
            'User_Role',
            'User_Credential',
            'User_Message'
        );

        foreach ($models as $model) {
            $schema->model = Pluf::factory($model);
            $schema->dropTables();
            if (true !== ($res = $schema->createTables())) {
                throw new Exception($res);
            }
        }

        // Test user
        $user = new User_Account();
        $user->login = 'test';
        $user->is_active = true;
        if (true !== $user->create()) {
            throw new Exception();
        }
        // Credential of user
        $credit = new User_Credential();
        $credit->setFromFormData(array(
            'account_id' => $user->id
        ));
        $credit->setPassword('test');
        if (true !== $credit->create()) {
            throw new Exception();
        }
    }

    /**
     *
     * @afterClass
     */
    public static function removeDatabses()
    {
        $db = Pluf::db();
        $schema = Pluf::factory('Pluf_DB_Schema', $db);
        $models = array(
            'User_Group',
            'User_Account',
            'User_Role',
            'User_Credential',
            'User_Message'
        );
        foreach ($models as $model) {
            $schema->model = Pluf::factory($model);
            $schema->dropTables();
        }
    }

    /**
     *
     * @test
     */
    public function shouldPossibleCreateNew()
    {
        $user = new User_Account();
        $user->login = 'Random' . rand();
        $user->email = 'Hi@test.com';
        $this->assertTrue($user->create(), 'Impossible to create user');
    }

    /**
     *
     * @test
     */
    public function shouldPossibleToGetMessages()
    {
        $user = new User_Account();
        $user = $user->getUser('test');
        $mess = $user->get_messages_list();
        $this->assertEquals(0, $mess->count());
    }

    /**
     *
     * @test
     */
    public function shouldPossibleToRoles()
    {
        $user = new User_Account();
        $user = $user->getUser('test');
        $roles = $user->get_roles_list();
        $this->assertEquals(0, $roles->count());
    }

    /**
     *
     * @test
     */
    public function shouldPossibleToGetGroups()
    {
        $user = new User_Account();
        $user = $user->getUser('test');
        $groups = $user->get_groups_list();
        $this->assertEquals(0, $groups->count());
    }

    /**
     *
     * @expectedException Pluf_Exception
     * @test
     */
    public function testUniqueLogin()
    {
        // Test user
        $user = new User_Account();
        $user->login = 'test';
        $user->is_active = true;
        // Test user already exists
        $user->create();
    }

    /**
     * TODO: maso, 2017: must throw Pluf_Exception_Form
     *
     * @expectedException Pluf_Exception
     * @test
     */
    public function testValidationUnique()
    {
        // Test user already exists
        $user = new User_Account();
        $user->login = 'test';
        $user->is_active = true;
        
        Pluf::loadFunction('Pluf_Shortcuts_GetFormForModel');
        $form = Pluf_Shortcuts_GetFormForModel($user, $user->getData(), array());
        $form->save();
    }
}


