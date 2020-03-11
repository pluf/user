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
namespace Pluf\Test\Group\REST;

use Pluf\Test\TestCase;
use Pluf\Exception;
use Pluf;
use Pluf_Migration;
use User_Account;
use User_Credential;
use User_Role;
use User_Group;
use Pluf\Test\Client;

class BasicsTest extends TestCase
{

    private static $client = null;

    /**
     *
     * @beforeClass
     */
    public static function createDataBase()
    {
        Pluf::start(__DIR__ . '/../../conf/config.php');
        $m = new Pluf_Migration();
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

        $per = User_Role::getFromString('tenant.owner');
        $user->setAssoc($per);

        self::$client = new Client();

        // Login
        $response = self::$client->post('/user/login', array(
            'login' => 'test',
            'password' => 'test'
        ));
        self::assertResponseStatusCode($response, 200, 'Fail to login');
    }

    /**
     *
     * @afterClass
     */
    public static function removeDatabses()
    {
        $m = new Pluf_Migration();
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
        $response = self::$client->get('/user/groups');
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');
        $this->assertResponsePaginateList($response, 'Find result is not JSON paginated list');
    }

    /**
     *
     * @test
     */
    public function adminCanCreateAGroup()
    {
        // Getting list
        $response = self::$client->post('/user/groups', array(
            'name' => 'Group name',
            'descritpion' => 'Descritpion'
        ));
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');
        $this->assertResponseNotAnonymousModel($response, 'Fail to create a group');

        $group = new User_Group();
        $list = $group->getList();
        $this->assertTrue(sizeof($list) > 0, 'Group is not created in db');

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

        $role = User_Role::getFromString('tenant.owner');
        $group->setAssoc($role);

        // Get list of roles
        $response = self::$client->get('/user/groups/' . $group->id);
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');

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

        $role = User_Role::getFromString('tenant.owner');
        $group->setAssoc($role);

        // Get list of roles
        $response = self::$client->delete('/user/groups/' . $group->id);
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');
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

        $role = User_Role::getFromString('tenant.owner');
        $group->setAssoc($role);

        // Get list of roles
        $response = self::$client->get('/user/groups/' . $group->id . '/roles');
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');
        $this->assertResponsePaginateList($response, 'Find result is not JSON paginated list');
        $this->assertResponseNonEmptyPaginateList($response, 'No role is in group');

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

        $role = User_Role::getFromString('tenant.owner');

        // Add role
        $response = self::$client->post('/user/groups/' . $group->id . '/roles', array(
            'role_id' => $role->id
        ));
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');

        $list = $group->get_roles_list();
        $this->assertTrue(sizeof($list) > 0, 'No role in list');

        $group->delAssoc($role);

        // Add role by another API
        $response = self::$client->post('/user/groups/' . $group->id . '/roles/' . $role->id, array());
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');

        $list = $group->get_roles_list();
        $this->assertTrue(sizeof($list) > 0, 'No role in list');

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

        $role = User_Role::getFromString('tenant.owner');

        // Add role to the group
        $group->setAssoc($role);

        // Delete role from group
        $response = self::$client->delete('/user/groups/' . $group->id . '/roles/' . $role->id);
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');

        $list = $group->get_roles_list();
        $this->assertTrue(sizeof($list) == 0, 'No role is in list');
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
        $response = self::$client->get('/user/groups/' . $group->id . '/accounts');
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');
        $this->assertResponsePaginateList($response, 'Find result is not JSON paginated list');

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
        $response = self::$client->post('/user/groups/' . $group->id . '/accounts', array(
            'user_id' => $user->id
        ));
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');

        $list = $group->get_accounts_list();
        $this->assertTrue(sizeof($list) > 0, 'No user in list');
        $group->delAssoc($user);

        $response = self::$client->post('/user/groups/' . $group->id . '/accounts/' . $user->id);
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');

        $list = $group->get_accounts_list();
        $this->assertTrue(sizeof($list) > 0, 'No role in list');
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
        $response = self::$client->post('/user/groups/' . $group->id . '/accounts', array(
            'user_id' => $user->id
        ));
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');

        $list = $group->get_accounts_list();
        $this->assertTrue(sizeof($list) > 0, 'No user in list');

        $response = self::$client->delete('/user/groups/' . $group->id . '/accounts/' . $user->id);
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');

        $list = $group->get_accounts_list();
        $this->assertTrue(sizeof($list) == 0, 'No user is in list');
        $group->delete();
    }
}



