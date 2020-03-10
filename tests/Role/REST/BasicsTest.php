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
namespace Plfu\Test\Role\REST;

use Pluf\Exception;
use Pluf\Test\TestCase;
use Pluf;
use Pluf_Migration;
use User_Account;
use User_Role;
use User_Group;
use User_Credential;
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
        $m = new Pluf_Migration(Pluf::f('installed_apps'));
        $m->unInstall();
    }

    /**
     * Getting list of properties with admin
     *
     * @test
     */
    public function adminCanGetListOfRoles()
    {
        // Getting list
        $response = self::$client->get('/user/roles');
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');
        $this->assertResponsePaginateList($response, 'Find result is not JSON paginated list');
    }

    /**
     *
     * @test
     */
    public function adminCanCreateARole()
    {
        // Getting list
        $data = array(
            'name' => 'Role name',
            'descritpion' => 'Descritpion',
            'application' => 'test',
            'code_name' => 'test-' . rand()
        );
        $response = self::$client->post('/user/roles', $data);
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');
        $this->assertResponseNotAnonymousModel($response, 'Fail to create a group');

        $role = User_Role::getFromString($data['application'] . '.' . $data['code_name']);
        $this->assertFalse($role->isAnonymous(), 'Role is not created in db');

        // Delete
        $role->delete();
    }

    /**
     *
     * @test
     */
    public function shouldGetSchema()
    {
        // Get
        $response = self::$client->get('/user/roles/schema');
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');
    }

    /**
     *
     * @test
     */
    public function shouldGetRoles()
    {
        // Get regular
        $response = self::$client->get('/user/roles');
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');

        // Get with query
        $response = self::$client->get('/user/roles', array(
            '_px_q' => 'test',
            '_px_sk' => 'id',
            '_px_so' => 'a'
        ));
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');
    }

    /**
     *
     * @test
     */
    public function shouldGetRole()
    {
        $role = new User_Role();
        $role->name = 'Role-' . rand();
        $role->description = 'Descritpion';
        $role->code_name = 'test' . rand();
        $role->application = 'test';
        $role->create();

        // Get
        $response = self::$client->get('/user/roles/' . $role->id);
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');

        $role->delete();
    }

    /**
     *
     * @test
     */
    public function shouldDeleteAGroup()
    {
        $role = new User_Role();
        $role->name = 'Role-' . rand();
        $role->description = 'Descritpion';
        $role->code_name = 'test' . rand();
        $role->application = 'test';
        $role->create();

        // Get list of roles
        $response = self::$client->delete('/user/roles/' . $role->id);
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');

        $role2 = new User_Role($role->id);
        $this->assertTrue($role2->isAnonymous(), 'Role exist!?');
    }

    /**
     *
     * @test
     */
    public function shoudGetListOfGroupOfTheRole()
    {
        $role = new User_Role();
        $role->name = 'role-' . rand();
        $role->description = 'Descritpion';
        $role->code_name = 'test' . rand();
        $role->application = 'test';
        $role->create();

        $group = new User_Group();
        $group->name = 'Group-' . rand();
        $group->description = 'Descritpion';
        $group->create();

        $role->setAssoc($group);

        // Get list of roles
        $response = self::$client->get('/user/roles/' . $role->id . '/groups');
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');
        $this->assertResponsePaginateList($response, 'Find result is not JSON paginated list');
        $this->assertResponseNonEmptyPaginateList($response, 'No role is in group');

        $role->delete();
        $group->delete();
    }

    /**
     *
     * @test
     */
    public function shoudAddAGroupToTheRole()
    {
        $role = new User_Role();
        $role->name = 'role-' . rand();
        $role->description = 'Descritpion';
        $role->code_name = 'test' . rand();
        $role->application = 'test';
        $role->create();

        $group = new User_Group();
        $group->name = 'Group-' . rand();
        $group->description = 'Descritpion';
        $group->create();

        // Add role
        $response = self::$client->post('/user/roles/' . $role->id . '/groups', array(
            'group_id' => $group->id
        ));
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');

        $list = $role->get_groups_list();
        $this->assertTrue(sizeof($list) > 0, 'No group in list');
        $role->delAssoc($group);

        $response = self::$client->post('/user/roles/' . $role->id . '/groups/' . $group->id);
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');

        $list = $role->get_groups_list();
        $this->assertTrue(sizeof($list) > 0, 'No group in list');
        $group->delete();
        $role->delete();
    }

    /**
     *
     * @test
     */
    public function shouldDeleteGropOfTheRole()
    {
        $role = new User_Role();
        $role->name = 'role-' . rand();
        $role->description = 'Descritpion';
        $role->code_name = 'test' . rand();
        $role->application = 'test';
        $role->create();

        $group = new User_Group();
        $group->name = 'Group-' . rand();
        $group->description = 'Descritpion';
        $group->create();

        // Add role
        $response = self::$client->post('/user/roles/' . $role->id . '/groups', array(
            'group_id' => $group->id
        ));
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');

        $list = $role->get_groups_list();
        $this->assertTrue(sizeof($list) > 0, 'No group in list');

        $response = self::$client->delete('/user/roles/' . $role->id . '/groups/' . $group->id);
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');

        $list = $role->get_groups_list();
        $this->assertTrue(sizeof($list) == 0, 'Group is still in list');
        $group->delete();
        $role->delete();
    }

    /**
     *
     * @test
     */
    public function shoudGetListOfUsersOfTheRole()
    {
        $role = new User_Role();
        $role->name = 'role-' . rand();
        $role->description = 'Descritpion';
        $role->code_name = 'test' . rand();
        $role->application = 'test';
        $role->create();

        // Get list of roles
        $response = self::$client->get('/user/roles/' . $role->id . '/accounts');
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');
        $this->assertResponsePaginateList($response, 'Find result is not JSON paginated list');

        $role->delete();
    }

    /**
     *
     * @test
     */
    public function shoudAddAUserToAThe()
    {
        $role = new User_Role();
        $role->name = 'role-' . rand();
        $role->description = 'Descritpion';
        $role->code_name = 'test' . rand();
        $role->application = 'test';
        $role->create();

        $user = new User_Account();
        $user = $user->getUser('admin');

        // Add
        $response = self::$client->post('/user/roles/' . $role->id . '/accounts', array(
            'user_id' => $user->id
        ));
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');

        $list = $role->get_accounts_list();
        $this->assertTrue(sizeof($list) > 0, 'No user in list');
        $role->delAssoc($user);

        $response = self::$client->post('/user/roles/' . $role->id . '/accounts/' . $user->id);
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');

        $list = $role->get_accounts_list();
        $this->assertTrue(sizeof($list) > 0, 'No role in list');
        $role->delete();
    }

    /**
     *
     * @test
     */
    public function shouldDeleteUsersOfAGroup()
    {
        $role = new User_Role();
        $role->name = 'role-' . rand();
        $role->description = 'Descritpion';
        $role->code_name = 'test' . rand();
        $role->application = 'test';
        $role->create();

        $user = new User_Account();
        $user = $user->getUser('admin');

        // Add role
        $response = self::$client->post('/user/roles/' . $role->id . '/accounts', array(
            'user_id' => $user->id
        ));
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');

        $list = $role->get_accounts_list();
        $this->assertTrue(sizeof($list) > 0, 'No user in list');

        $response = self::$client->delete('/user/roles/' . $role->id . '/accounts/' . $user->id);
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');

        $list = $role->get_accounts_list();
        $this->assertTrue(sizeof($list) == 0, 'No user is in list');
        $role->delete();
    }
}



