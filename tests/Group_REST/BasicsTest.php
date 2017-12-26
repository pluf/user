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
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 */
class Group_REST_BasicsTest extends TestCase
{

    private static $client = null;

    /**
     * @beforeClass
     */
    public static function createDataBase()
    {
        Pluf::start(__DIR__ . '/../conf/sqlite.config.php');
        $m = new Pluf_Migration(Pluf::f('installed_apps'));
        $m->install();
        $m->init();
        
        $user = new User(1);
        $rol = Role::getFromString('Pluf.owner');
        $user->setAssoc($rol);
        
        self::$client = new Test_Client(array(
            array(
                'app' => 'Role',
                'regex' => '#^/api/role#',
                'base' => '',
                'sub' => include 'Role/urls.php'
            ),
            array(
                'app' => 'Group',
                'regex' => '#^/api/group#',
                'base' => '',
                'sub' => include 'Group/urls.php'
            ),
            array(
                'app' => 'User',
                'regex' => '#^/api/user#',
                'base' => '',
                'sub' => include 'User/urls.php'
            )
        ));
    }

    /**
     * @afterClass
     */
    public static function removeDatabses()
    {
        Pluf::start(__DIR__ . '/../conf/sqlite.config.php');
        $m = new Pluf_Migration(Pluf::f('installed_apps'));
        $m->unInstall();
    }

    /**
     * @test
     */
    public function anonymousCanGetListOfGroups()
    {
        $response = self::$client->get('/api/group/find');
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
        Test_Assert::assertResponsePaginateList($response, 'Find result is not JSON paginated list');
    }

    /**
     * Getting list of properties with admin
     *
     * @test
     */
    public function adminCanGetListOfListOfGroups()
    {
        // Login
        $response = self::$client->post('/api/user/login', array(
            'login' => 'admin',
            'password' => 'admin'
        ));
        Test_Assert::assertResponseStatusCode($response, 200, 'Fail to login');
        
        // Getting list
        $response = self::$client->get('/api/group/find');
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
        Test_Assert::assertResponsePaginateList($response, 'Find result is not JSON paginated list');
    }

    /**
     * @test
     */
    public function adminCanCreateAGroup()
    {
        // Login
        $response = self::$client->post('/api/user/login', array(
            'login' => 'admin',
            'password' => 'admin'
        ));
        Test_Assert::assertResponseStatusCode($response, 200, 'Fail to login');
        
        // Getting list
        $response = self::$client->post('/api/group/new', array(
            'name' => 'Group name',
            'descritpion' => 'Descritpion'
        ));
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
        Test_Assert::assertResponseNotAnonymousModel($response, 'Fail to create a group');
        
        $group = new Group();
        $list = $group->getList();
        Test_Assert::assertTrue(sizeof($list) > 0, 'Group is not created in db');
        
        // Delete all groups
        foreach ($list as $g) {
            $g->delete();
        }
    }

    /**
     * @test
     */
    public function shouldGetGroup()
    {
        $group = new Group();
        $group->name = 'Group-' . rand();
        $group->description = 'Descritpion';
        $group->create();
        
        $role = Role::getFromString('Pluf.owner');
        $group->setAssoc($role);
        
        // Login
        $response = self::$client->post('/api/user/login', array(
            'login' => 'admin',
            'password' => 'admin'
        ));
        Test_Assert::assertResponseStatusCode($response, 200, 'Fail to login');
        
        // Get list of roles
        $response = self::$client->get('/api/group/' . $group->id);
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
        
        $group->delete();
    }

    /**
     * @test
     */
    public function shouldDeleteAGroup()
    {
        $group = new Group();
        $group->name = 'Group-' . rand();
        $group->description = 'Descritpion';
        $group->create();
        
        $role = Role::getFromString('Pluf.owner');
        $group->setAssoc($role);
        
        // Login
        $response = self::$client->post('/api/user/login', array(
            'login' => 'admin',
            'password' => 'admin'
        ));
        Test_Assert::assertResponseStatusCode($response, 200, 'Fail to login');
        
        // Get list of roles
        $response = self::$client->delete('/api/group/' . $group->id);
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
    }

    /**
     * @test
     */
    public function shoudGetListOfRolesOfGroup()
    {
        $group = new Group();
        $group->name = 'Group-' . rand();
        $group->description = 'Descritpion';
        $group->create();
        
        $role = Role::getFromString('Pluf.owner');
        $group->setAssoc($role);
        
        // Login
        $response = self::$client->post('/api/user/login', array(
            'login' => 'admin',
            'password' => 'admin'
        ));
        Test_Assert::assertResponseStatusCode($response, 200, 'Fail to login');
        
        // Get list of roles
        $response = self::$client->get('/api/group/' . $group->id . '/role/find');
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
        Test_Assert::assertResponsePaginateList($response, 'Find result is not JSON paginated list');
        Test_Assert::assertResponseNonEmptyPaginateList($response, 'No role is in group');
        
        $group->delete();
    }

    /**
     * @test
     */
    public function shoudAddARoleToAGroup()
    {
        $group = new Group();
        $group->name = 'Group-' . rand();
        $group->description = 'Descritpion';
        $group->create();
        
        $role = Role::getFromString('Pluf.owner');
        $group->setAssoc($role);
        
        // Login
        $response = self::$client->post('/api/user/login', array(
            'login' => 'admin',
            'password' => 'admin'
        ));
        Test_Assert::assertResponseStatusCode($response, 200, 'Fail to login');
        
        // Add role
        $response = self::$client->post('/api/group/' . $group->id . '/role/new', array(
            'role_id' => $role->id
        ));
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
        
        $list = $group->get_roles_list();
        Test_Assert::assertTrue(sizeof($list) > 0, 'No role in list');
        $group->delAssoc($role);
        
        $response = self::$client->post('/api/group/' . $group->id . '/role/' . $role->id, array());
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
        
        $list = $group->get_roles_list();
        Test_Assert::assertTrue(sizeof($list) > 0, 'No role in list');
        $group->delete();
    }

    /**
     * @test
     */
    public function shouldDeleteRoleOfAGroup()
    {
        $group = new Group();
        $group->name = 'Group-' . rand();
        $group->description = 'Descritpion';
        $group->create();
        
        $role = Role::getFromString('Pluf.owner');
        $group->setAssoc($role);
        
        // Login
        $response = self::$client->post('/api/user/login', array(
            'login' => 'admin',
            'password' => 'admin'
        ));
        Test_Assert::assertResponseStatusCode($response, 200, 'Fail to login');
        
        // Add role
        $response = self::$client->post('/api/group/' . $group->id . '/role/new', array(
            'role_id' => $role->id
        ));
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
        
        $list = $group->get_roles_list();
        Test_Assert::assertTrue(sizeof($list) > 0, 'No role in list');
        
        $response = self::$client->delete('/api/group/' . $group->id . '/role/' . $role->id);
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
        
        $list = $group->get_roles_list();
        Test_Assert::assertTrue(sizeof($list) == 0, 'No role is in list');
        $group->delete();
    }

    /**
     * @test
     */
    public function shoudGetListOfUsersOfGroup()
    {
        $group = new Group();
        $group->name = 'Group-' . rand();
        $group->description = 'Descritpion';
        $group->create();
        
        // Login
        $response = self::$client->post('/api/user/login', array(
            'login' => 'admin',
            'password' => 'admin'
        ));
        Test_Assert::assertResponseStatusCode($response, 200, 'Fail to login');
        
        // Get list of roles
        $response = self::$client->get('/api/group/' . $group->id . '/user/find');
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
        Test_Assert::assertResponsePaginateList($response, 'Find result is not JSON paginated list');
        
        $group->delete();
    }

    /**
     * @test
     */
    public function shoudAddAUserToAGroup()
    {
        $group = new Group();
        $group->name = 'Group-' . rand();
        $group->description = 'Descritpion';
        $group->create();
        
        $user = new User();
        $user = $user->getUser('admin');
        
        // Login
        $response = self::$client->post('/api/user/login', array(
            'login' => 'admin',
            'password' => 'admin'
        ));
        Test_Assert::assertResponseStatusCode($response, 200, 'Fail to login');
        
        // Add role
        $response = self::$client->post('/api/group/' . $group->id . '/user/new', array(
            'user_id' => $user->id
        ));
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
        
        $list = $group->get_users_list();
        Test_Assert::assertTrue(sizeof($list) > 0, 'No user in list');
        $group->delAssoc($user);
        
        $response = self::$client->post('/api/group/' . $group->id . '/user/' . $user->id);
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
        
        $list = $group->get_users_list();
        Test_Assert::assertTrue(sizeof($list) > 0, 'No role in list');
        $group->delete();
    }

    /**
     * @test
     */
    public function shouldDeleteUsersOfAGroup()
    {
        $group = new Group();
        $group->name = 'Group-' . rand();
        $group->description = 'Descritpion';
        $group->create();
        
        $user = new User();
        $user = $user->getUser('admin');
        
        // Login
        $response = self::$client->post('/api/user/login', array(
            'login' => 'admin',
            'password' => 'admin'
        ));
        Test_Assert::assertResponseStatusCode($response, 200, 'Fail to login');
        
        // Add role
        $response = self::$client->post('/api/group/' . $group->id . '/user/new', array(
            'user_id' => $user->id
        ));
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
        
        $list = $group->get_users_list();
        Test_Assert::assertTrue(sizeof($list) > 0, 'No user in list');
        
        $response = self::$client->delete('/api/group/' . $group->id . '/user/' . $user->id);
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
        
        $list = $group->get_users_list();
        Test_Assert::assertTrue(sizeof($list) == 0, 'No user is in list');
        $group->delete();
    }
}



