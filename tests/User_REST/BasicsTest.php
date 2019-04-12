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
class User_REST_BasicsTest extends TestCase
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

        $user = new User_Account();
        $user = $user->getUser('test');
        Test_Assert::assertTrue($user->hasPerm('tenant.owner'));

        self::$client = new Test_Client(array(
            array(
                'app' => 'User',
                'regex' => '#^/api/v2/user#',
                'base' => '',
                'sub' => include 'User/urls-v2.php'
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
    public function currentUserRest()
    {
        $response = self::$client->get('/api/v2/user/accounts/current');
        $this->assertNotNull($response);
    }

    /**
     * @test
     */
    public function loginRestTest()
    {
        $response = self::$client->post('/api/v2/user/login', array(
            'login' => 'test',
            'password' => 'test'
        ));
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
    }

    /**
     * @test
     */
    public function logoutRestTest()
    {
        $response = self::$client->post('/api/v2/user/logout');
        $this->assertNotNull($response);

        $response = self::$client->get('/api/v2/user/logout');
        $this->assertNotNull($response);
    }

    /**
     * @test
     */
    public function listUsersRestTest()
    {
        $response = self::$client->get('/api/v2/user/accounts');
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
    }

    /**
     * @test
     */
    public function listUsersByAdminRestTest()
    {
        $response = self::$client->post('/api/v2/user/login', array(
            'login' => 'test',
            'password' => 'test'
        ));
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);

        // General get
        $response = self::$client->get('/api/v2/user/accounts');
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);

        // Get list by qury and sort
        $response = self::$client->get('/api/v2/user/accounts', array(
            '_px_q' => 'test',
            '_px_sk' => 'id',
            '_px_so' => 'a'
        ));
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
    }

    /**
     * @test
     */
    public function createNewUserRestTest()
    {
        // Change detail
        $form = array(
            'login' => 'LoginName' . rand(),
            'email' => 'info' . rand() . '@localhost',
            'password' => 'test',
            'first_name' => 'my_name'
        );
        $response = self::$client->post('/api/v2/user/accounts', $form);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
    }

    /**
     * @test
     */
    public function getUserByIdRestTest()
    {
        $user = new User_Account();
        $user = $user->getUser('test');

        $response = self::$client->get('/api/v2/user/accounts/' . $user->id);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
    }

    /**
     * @test
     */
    public function deleteUserByIdRestTest()
    {
        $form = array(
            'login' => 'LoginName' . rand(),
            'email' => 'info' . rand() . '@localhost',
            'password' => 'test',
            'first_name' => 'my_name'
        );
        $response = self::$client->post('/api/v2/user/accounts', $form);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);

        $user = new User_Account();
        $user = $user->getUser($form['login']);

        // Login
        $response = self::$client->post('/api/v2/user/login', array(
            'login' => 'test',
            'password' => 'test'
        ));
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);

        // delete
        $response = self::$client->delete('/api/v2/user/accounts/' . $user->id, $form);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
    }

    /**
     * @test
     */
    public function getUserProfileRestTest()
    {
        $form = array(
            'login' => 'LoginName' . rand(),
            'email' => 'info' . rand() . '@localhost',
            'password' => 'test',
            'first_name' => 'my_name'
        );
        $response = self::$client->post('/api/v2/user/accounts', $form);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);

        $user = new User_Account();
        $user = $user->getUser($form['login']);

        // Login
        $response = self::$client->post('/api/v2/user/login', array(
            'login' => 'test',
            'password' => 'test'
        ));
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);

        $response = self::$client->get('/api/v2/user/accounts/' . $user->id . '/profiles');
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
    }

    /**
     * @test
     */
    public function updateUserProfileRestTest()
    {
        $form = array(
            'login' => 'LoginName' . rand(),
            'email' => 'info' . rand() . '@localhost',
            'password' => 'test',
            'first_name' => 'my_name'
        );
        $response = self::$client->post('/api/v2/user/accounts', $form);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);

        $user = new User_Account();
        $user = $user->getUser($form['login']);

        // Login
        $response = self::$client->post('/api/v2/user/login', array(
            'login' => 'test',
            'password' => 'test'
        ));
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);

        $response = self::$client->post('/api/v2/user/accounts/' . $user->id . '/profiles', array(
            'first_name' => 'test first name',
            'last_name' => 'test last name',
            'public_email' => 'public@mail'
        ));
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
    }

    /**
     * @test
     */
    public function getUserAvatarRestTest()
    {
        $user = new User_Account();
        $user = $user->getUser('test');

        // Login
        $response = self::$client->post('/api/v2/user/login', array(
            'login' => 'test',
            'password' => 'test'
        ));
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);

        // get avatar
        $response = self::$client->get('/api/v2/user/accounts/' . $user->id . '/avatar');
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);

        // current user avatar
        $response = self::$client->get('/api/v2/user/accounts/current/avatar');
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
    }

    // TODO: maso, 2017: adding test to update avatar

    /**
     * @test
     */
    public function addUserGroupRestTest()
    {
        $user = new User_Account();
        $user = $user->getUser('test');

        $group = new User_Group();
        $group->name = 'test:' . rand();
        $group->create();

        // Login
        $response = self::$client->post('/api/v2/user/login', array(
            'login' => 'test',
            'password' => 'test'
        ));
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);

        // find
        $response = self::$client->get('/api/v2/user/accounts/' . $user->id . '/groups');
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);

        // create
        $response = self::$client->post('/api/v2/user/accounts/' . $user->id . '/groups', array(
            'groupId' => $group->id,
            'group' => $group->id
        ));
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);

        // get
        $response = self::$client->get('/api/v2/user/accounts/' . $user->id . '/groups/' . $group->id);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);

        // delete
        $response = self::$client->delete('/api/v2/user/accounts/' . $user->id . '/groups/' . $group->id);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);

        // create
        $response = self::$client->post('/api/v2/user/accounts/' . $user->id . '/groups', array(
            'group_name' => $group->name
        ));
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
    }

    /**
     * @test
     */
    public function assUserRoleRestTest()
    {
        $user = new User_Account();
        $user = $user->getUser('test');

        $perm = new User_Role();
        $perm->app = 'test';
        $perm->code_name = 'testRest';
        $perm->create();

        // Login
        $response = self::$client->post('/api/v2/user/login', array(
            'login' => 'test',
            'password' => 'test'
        ));
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);

        // find
        $response = self::$client->get('/api/v2/user/accounts/' . $user->id . '/roles');
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);

        // get
        $response = self::$client->post('/api/v2/user/accounts/' . $user->id . '/roles', array(
            'roleId' => $perm->id,
            'role' => $perm->id
        ));
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);

        // get
        $response = self::$client->get('/api/v2/user/accounts/' . $user->id . '/roles/' . $perm->id);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);

        // delete
        $response = self::$client->delete('/api/v2/user/accounts/' . $user->id . '/roles/' . $perm->id);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
    }
}



