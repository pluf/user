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
        Pluf::start(__DIR__ . '/../conf/config.php');
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
        $m = new Pluf_Migration(Pluf::f('installed_apps'));
        $m->unInstall();
    }

    /**
     * @test
     */
    public function anonymousCanGetListOfRoles()
    {
        $response = self::$client->get('/api/role/find');
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
        Test_Assert::assertResponsePaginateList($response, 'Find result is not JSON paginated list');
    }

    /**
     * Getting list of properties with admin
     *
     * @test
     */
    public function adminCanGetListOfRoles()
    {
        // Login
        $response = self::$client->post('/api/user/login', array(
            'login' => 'admin',
            'password' => 'admin'
        ));
        Test_Assert::assertResponseStatusCode($response, 200, 'Fail to login');
        
        // Getting list
        $response = self::$client->get('/api/role/find');
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
        Test_Assert::assertResponsePaginateList($response, 'Find result is not JSON paginated list');
    }

    /**
     * @test
     */
    public function adminCanCreateARole()
    {
        // Login
        $response = self::$client->post('/api/user/login', array(
            'login' => 'admin',
            'password' => 'admin'
        ));
        Test_Assert::assertResponseStatusCode($response, 200, 'Fail to login');
        
        // Getting list
        $data = array(
            'name' => 'Group name',
            'descritpion' => 'Descritpion',
            'application' => 'test',
            'code_name' => 'test-' . rand()
        );
        $response = self::$client->post('/api/role/new', $data);
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
        Test_Assert::assertResponseNotAnonymousModel($response, 'Fail to create a group');
        
        $role = Role::getFromString($data['application'] . '.' . $data['code_name']);
        Test_Assert::assertFalse($role->isAnonymous(), 'Role is not created in db');
        
        // Delete
        $role->delete();
    }

    /**
     * @test
     */
    public function shouldGetRole()
    {
        $role = new Role();
        $role->name = 'Group-' . rand();
        $role->description = 'Descritpion';
        $role->code_name = 'test' . rand();
        $role->application = 'test';
        $role->create();
        
        // Login
        $response = self::$client->post('/api/user/login', array(
            'login' => 'admin',
            'password' => 'admin'
        ));
        Test_Assert::assertResponseStatusCode($response, 200, 'Fail to login');
        
        // Get
        $response = self::$client->get('/api/role/' . $role->id);
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
        
        $role->delete();
    }

    /**
     * @test
     */
    public function shouldDeleteAGroup()
    {
        $role = new Role();
        $role->name = 'Group-' . rand();
        $role->description = 'Descritpion';
        $role->code_name = 'test' . rand();
        $role->application = 'test';
        $role->create();
        
        // Login
        $response = self::$client->post('/api/user/login', array(
            'login' => 'admin',
            'password' => 'admin'
        ));
        Test_Assert::assertResponseStatusCode($response, 200, 'Fail to login');
        
        // Get list of roles
        $response = self::$client->delete('/api/role/' . $role->id);
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
        
        $role2 = new Role($role->id);
        Test_Assert::assertTrue($role2->isAnonymous(), 'Role exist!?');
    }

    /**
     * @test
     */
    public function shoudGetListOfGroupOfTheRole()
    {
        $role = new Role();
        $role->name = 'role-' . rand();
        $role->description = 'Descritpion';
        $role->code_name = 'test' . rand();
        $role->application = 'test';
        $role->create();
        
        $group = new Group();
        $group->name = 'Group-' . rand();
        $group->description = 'Descritpion';
        $group->create();
        
        $role->setAssoc($group);
        
        // Login
        $response = self::$client->post('/api/user/login', array(
            'login' => 'admin',
            'password' => 'admin'
        ));
        Test_Assert::assertResponseStatusCode($response, 200, 'Fail to login');
        
        // Get list of roles
        $response = self::$client->get('/api/role/' . $role->id . '/group/find');
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
        Test_Assert::assertResponsePaginateList($response, 'Find result is not JSON paginated list');
        Test_Assert::assertResponseNonEmptyPaginateList($response, 'No role is in group');
        
        $role->delete();
        $group->delete();
    }

    /**
     * @test
     */
    public function shoudAddAGroupToTheRole()
    {
        $role = new Role();
        $role->name = 'role-' . rand();
        $role->description = 'Descritpion';
        $role->code_name = 'test' . rand();
        $role->application = 'test';
        $role->create();
        
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
        
        // Add role
        $response = self::$client->post('/api/role/' . $role->id . '/group/new', array(
            'group_id' => $group->id
        ));
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
        
        $list = $role->get_groups_list();
        Test_Assert::assertTrue(sizeof($list) > 0, 'No group in list');
        $role->delAssoc($group);
        
        $response = self::$client->post('/api/role/' . $role->id . '/group/' . $group->id);
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
        
        $list = $role->get_groups_list();
        Test_Assert::assertTrue(sizeof($list) > 0, 'No group in list');
        $group->delete();
        $role->delete();
    }

    /**
     * @test
     */
    public function shouldDeleteGropOfTheRole()
    {
        $role = new Role();
        $role->name = 'role-' . rand();
        $role->description = 'Descritpion';
        $role->code_name = 'test' . rand();
        $role->application = 'test';
        $role->create();
        
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
        
        // Add role
        $response = self::$client->post('/api/role/' . $role->id . '/group/new', array(
            'group_id' => $group->id
        ));
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
        
        $list = $role->get_groups_list();
        Test_Assert::assertTrue(sizeof($list) > 0, 'No group in list');
        
        $response = self::$client->delete('/api/role/' . $role->id . '/group/' . $group->id);
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
        
        $list = $role->get_groups_list();
        Test_Assert::assertTrue(sizeof($list) == 0, 'Group is still in list');
        $group->delete();
        $role->delete();
    }

    /**
     * @test
     */
    public function shoudGetListOfUsersOfTheRole()
    {
        $role = new Role();
        $role->name = 'role-' . rand();
        $role->description = 'Descritpion';
        $role->code_name = 'test' . rand();
        $role->application = 'test';
        $role->create();
        
        // Login
        $response = self::$client->post('/api/user/login', array(
            'login' => 'admin',
            'password' => 'admin'
        ));
        Test_Assert::assertResponseStatusCode($response, 200, 'Fail to login');
        
        // Get list of roles
        $response = self::$client->get('/api/role/' . $role->id . '/user/find');
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
        Test_Assert::assertResponsePaginateList($response, 'Find result is not JSON paginated list');
        
        $role->delete();
    }

    /**
     * @test
     */
    public function shoudAddAUserToAThe()
    {
        $role = new Role();
        $role->name = 'role-' . rand();
        $role->description = 'Descritpion';
        $role->code_name = 'test' . rand();
        $role->application = 'test';
        $role->create();
        
        $user = new User();
        $user = $user->getUser('admin');
        
        // Login
        $response = self::$client->post('/api/user/login', array(
            'login' => 'admin',
            'password' => 'admin'
        ));
        Test_Assert::assertResponseStatusCode($response, 200, 'Fail to login');
        
        // Add
        $response = self::$client->post('/api/role/' . $role->id . '/user/new', array(
            'user_id' => $user->id
        ));
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
        
        $list = $role->get_users_list();
        Test_Assert::assertTrue(sizeof($list) > 0, 'No user in list');
        $role->delAssoc($user);
        
        $response = self::$client->post('/api/role/' . $role->id . '/user/' . $user->id);
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
        
        $list = $role->get_users_list();
        Test_Assert::assertTrue(sizeof($list) > 0, 'No role in list');
        $role->delete();
    }

    /**
     * @test
     */
    public function shouldDeleteUsersOfAGroup()
    {
        $role = new Role();
        $role->name = 'role-' . rand();
        $role->description = 'Descritpion';
        $role->code_name = 'test' . rand();
        $role->application = 'test';
        $role->create();
        
        $user = new User();
        $user = $user->getUser('admin');
        
        // Login
        $response = self::$client->post('/api/user/login', array(
            'login' => 'admin',
            'password' => 'admin'
        ));
        Test_Assert::assertResponseStatusCode($response, 200, 'Fail to login');
        
        // Add role
        $response = self::$client->post('/api/role/' . $role->id . '/user/new', array(
            'user_id' => $user->id
        ));
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
        
        $list = $role->get_users_list();
        Test_Assert::assertTrue(sizeof($list) > 0, 'No user in list');
        
        $response = self::$client->delete('/api/role/' . $role->id . '/user/' . $user->id);
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
        
        $list = $role->get_users_list();
        Test_Assert::assertTrue(sizeof($list) == 0, 'No user is in list');
        $role->delete();
    }
}



