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

/**
 *
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 */
class Group_REST_BasicsTest extends TestCase
{

    private static $client = null;

    /**
     *
     * @beforeClass
     */
    public static function createDataBase()
    {
        Pluf::start(__DIR__ . '/../conf/config.php');
        $m = new Pluf_Migration(Pluf::f('installed_apps'));
        $m->install();
        $m->init();
        
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
        
        $per = User_Role::getFromString('Pluf.owner');
        $user->setAssoc($per);

        self::$client = new Test_Client(array(
            array(
                'app' => 'Role',
                'regex' => '#^/api/v2/user/roles#',
                'base' => '',
                'sub' => include 'Role/urls-v2.php'
            ),
            array(
                'app' => 'Group',
                'regex' => '#^/api/v2/user/groups#',
                'base' => '',
                'sub' => include 'Group/urls-v2.php'
            ),
            array(
                'app' => 'User',
                'regex' => '#^/api/v2/user#',
                'base' => '',
                'sub' => include 'User/urls-v2.php'
            )
        ));

        // Login
        $response = self::$client->post('/api/v2/user/login', array(
            'login' => 'test',
            'password' => 'test'
        ));
        Test_Assert::assertResponseStatusCode($response, 200, 'Fail to login');
    }

    /**
     *
     * @afterClass
     */
    public static function removeDatabses()
    {
        $m = new Pluf_Migration(Pluf::f('installed_apps'));
        $m->unInstall();
    }

    /**
     * Getting list of properties with admin
     *
     * @test
     */
    public function adminCanGetListOfListOfGroups()
    {
        // Getting list
        $response = self::$client->get('/api/v2/user/groups');
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
        Test_Assert::assertResponsePaginateList($response, 'Find result is not JSON paginated list');
    }

    /**
     *
     * @test
     */
    public function adminCanCreateAGroup()
    {
        // Getting list
        $response = self::$client->post('/api/v2/user/groups', array(
            'name' => 'Group name',
            'descritpion' => 'Descritpion'
        ));
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
        Test_Assert::assertResponseNotAnonymousModel($response, 'Fail to create a group');

        $group = new User_Group();
        $list = $group->getList();
        Test_Assert::assertTrue(sizeof($list) > 0, 'Group is not created in db');

        // Delete all groups
        foreach ($list as $g) {
            $g->delete();
        }
    }

    /**
     *
     * @test
     */
    public function shouldGetGroup()
    {
        $group = new User_Group();
        $group->name = 'Group-' . rand();
        $group->description = 'Descritpion';
        $group->create();

        $role = User_Role::getFromString('Pluf.owner');
        $group->setAssoc($role);

        // Get list of roles
        $response = self::$client->get('/api/v2/user/groups/' . $group->id);
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');

        $group->delete();
    }

    /**
     *
     * @test
     */
    public function shouldDeleteAGroup()
    {
        $group = new User_Group();
        $group->name = 'Group-' . rand();
        $group->description = 'Descritpion';
        $group->create();

        $role = User_Role::getFromString('Pluf.owner');
        $group->setAssoc($role);

        // Get list of roles
        $response = self::$client->delete('/api/v2/user/groups/' . $group->id);
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
    }

    /**
     *
     * @test
     */
    public function shoudGetListOfRolesOfGroup()
    {
        $group = new User_Group();
        $group->name = 'Group-' . rand();
        $group->description = 'Descritpion';
        $group->create();

        $role = User_Role::getFromString('Pluf.owner');
        $group->setAssoc($role);

        // Get list of roles
        $response = self::$client->get('/api/v2/user/groups/' . $group->id . '/roles');
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
        Test_Assert::assertResponsePaginateList($response, 'Find result is not JSON paginated list');
        Test_Assert::assertResponseNonEmptyPaginateList($response, 'No role is in group');

        $group->delete();
    }

    /**
     *
     * @test
     */
    public function shoudAddARoleToAGroup()
    {
        $group = new User_Group();
        $group->name = 'Group-' . rand();
        $group->description = 'Descritpion';
        $group->create();

        $role = User_Role::getFromString('Pluf.owner');
        $group->setAssoc($role);

        // Add role
        $response = self::$client->post('/api/v2/user/groups/' . $group->id . '/roles', array(
            'role_id' => $role->id
        ));
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');

        $list = $group->get_roles_list();
        Test_Assert::assertTrue(sizeof($list) > 0, 'No role in list');
        $group->delAssoc($role);

        $response = self::$client->post('/api/v2/user/groups/' . $group->id . '/roles/' . $role->id, array());
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');

        $list = $group->get_roles_list();
        Test_Assert::assertTrue(sizeof($list) > 0, 'No role in list');
        $group->delete();
    }

    /**
     *
     * @test
     */
    public function shouldDeleteRoleOfAGroup()
    {
        $group = new User_Group();
        $group->name = 'Group-' . rand();
        $group->description = 'Descritpion';
        $group->create();

        $role = User_Role::getFromString('Pluf.owner');
        $group->setAssoc($role);

        // Add role
        $response = self::$client->post('/api/v2/user/groups/' . $group->id . '/roles', array(
            'role_id' => $role->id
        ));
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');

        $list = $group->get_roles_list();
        Test_Assert::assertTrue(sizeof($list) > 0, 'No role in list');

        $response = self::$client->delete('/api/v2/user/groups/' . $group->id . '/roles/' . $role->id);
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');

        $list = $group->get_roles_list();
        Test_Assert::assertTrue(sizeof($list) == 0, 'No role is in list');
        $group->delete();
    }

    /**
     *
     * @test
     */
    public function shoudGetListOfUsersOfGroup()
    {
        $group = new User_Group();
        $group->name = 'Group-' . rand();
        $group->description = 'Descritpion';
        $group->create();

        // Get list of roles
        $response = self::$client->get('/api/v2/user/groups/' . $group->id . '/accounts');
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
        Test_Assert::assertResponsePaginateList($response, 'Find result is not JSON paginated list');

        $group->delete();
    }

    /**
     *
     * @test
     */
    public function shoudAddAUserToAGroup()
    {
        $group = new User_Group();
        $group->name = 'Group-' . rand();
        $group->description = 'Descritpion';
        $group->create();

        $user = new User_Account();
        $user = $user->getUser('admin');

        // Add role
        $response = self::$client->post('/api/v2/user/groups/' . $group->id . '/accounts', array(
            'user_id' => $user->id
        ));
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');

        $list = $group->get_accounts_list();
        Test_Assert::assertTrue(sizeof($list) > 0, 'No user in list');
        $group->delAssoc($user);

        $response = self::$client->post('/api/v2/user/groups/' . $group->id . '/accounts/' . $user->id);
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');

        $list = $group->get_accounts_list();
        Test_Assert::assertTrue(sizeof($list) > 0, 'No role in list');
        $group->delete();
    }

    /**
     *
     * @test
     */
    public function shouldDeleteUsersOfAGroup()
    {
        $group = new User_Group();
        $group->name = 'Group-' . rand();
        $group->description = 'Descritpion';
        $group->create();

        $user = new User_Account();
        $user = $user->getUser('admin');

        // Add role
        $response = self::$client->post('/api/v2/user/groups/' . $group->id . '/accounts', array(
            'user_id' => $user->id
        ));
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');

        $list = $group->get_accounts_list();
        Test_Assert::assertTrue(sizeof($list) > 0, 'No user in list');

        $response = self::$client->delete('/api/v2/user/groups/' . $group->id . '/accounts/' . $user->id);
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');

        $list = $group->get_accounts_list();
        Test_Assert::assertTrue(sizeof($list) == 0, 'No user is in list');
        $group->delete();
    }
}



