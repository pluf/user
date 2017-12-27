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
        
        $rol = Role::getFromString('Pluf.owner');
        $user->setAssoc($rol);
        
        $t = new User(1);
        Test_Assert::assertTrue($t->hasPerm('Pluf.owner'));
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
        $client = new Test_Client(array(
            array(
                'app' => 'User',
                'regex' => '#^/api/user#',
                'base' => '',
                'sub' => include 'User/urls.php'
            )
        ));
        $response = $client->get('/api/user');
        $this->assertNotNull($response);
    }

    /**
     * @test
     */
    public function loginRestTest()
    {
        $client = new Test_Client(array(
            array(
                'app' => 'User',
                'regex' => '#^/api/user#',
                'base' => '',
                'sub' => include 'User/urls.php'
            )
        ));
        $response = $client->post('/api/user/login', array(
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
        $client = new Test_Client(array(
            array(
                'app' => 'User',
                'regex' => '#^/api/user#',
                'base' => '',
                'sub' => include 'User/urls.php'
            )
        ));
        $response = $client->post('/api/user/logout');
        $this->assertNotNull($response);
        
        $response = $client->get('/api/user/logout');
        $this->assertNotNull($response);
    }

    /**
     * @test
     */
    public function listUsersRestTest()
    {
        $client = new Test_Client(array(
            array(
                'app' => 'User',
                'regex' => '#^/api/user#',
                'base' => '',
                'sub' => include 'User/urls.php'
            )
        ));
        $response = $client->get('/api/user/find');
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
    }

    /**
     * @test
     */
    public function updateCurrentUserRestTest()
    {
        $client = new Test_Client(array(
            array(
                'app' => 'User',
                'regex' => '#^/api/user#',
                'base' => '',
                'sub' => include 'User/urls.php'
            )
        ));
        
        // login
        $response = $client->post('/api/user/login', array(
            'login' => 'test',
            'password' => 'test'
        ));
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        
        // Change detail
        $response = $client->post('/api/user', array(
            'first_name' => 'my_name'
        ));
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
    }

    /**
     * @test
     */
    public function createNewUserRestTest()
    {
        $client = new Test_Client(array(
            array(
                'app' => 'User',
                'regex' => '#^/api/user#',
                'base' => '',
                'sub' => include 'User/urls.php'
            )
        ));
        
        // Change detail
        $form = array(
            'login' => 'LoginName' . rand(),
            'email' => 'info' . rand() . '@localhost',
            'password' => 'test',
            'first_name' => 'my_name'
        );
        $response = $client->post('/api/user/new', $form);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
    }

    /**
     * @test
     */
    public function getUserByIdRestTest()
    {
        $client = new Test_Client(array(
            array(
                'app' => 'User',
                'regex' => '#^/api/user#',
                'base' => '',
                'sub' => include 'User/urls.php'
            )
        ));
        
        $user = new User();
        $user = $user->getUser('test');
        
        // Change detail
        $response = $client->get('/api/user/' . $user->id);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
    }

    /**
     * @test
     */
    public function updateUserByIdRestTest()
    {
        $client = new Test_Client(array(
            array(
                'app' => 'User',
                'regex' => '#^/api/user#',
                'base' => '',
                'sub' => include 'User/urls.php'
            )
        ));
        
        $user = new User();
        $user = $user->getUser('test');
        
        // Login
        $response = $client->post('/api/user/login', array(
            'login' => 'test',
            'password' => 'test'
        ));
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        
        // Change detail
        $form = array(
            'first_name' => 'my_name'
        );
        $response = $client->post('/api/user/' . $user->id, $form);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
    }

    /**
     * @test
     */
    public function deleteUserByIdRestTest()
    {
        $client = new Test_Client(array(
            array(
                'app' => 'User',
                'regex' => '#^/api/user#',
                'base' => '',
                'sub' => include 'User/urls.php'
            )
        ));
        
        // Change detail
        $form = array(
            'login' => 'LoginName' . rand(),
            'email' => 'info' . rand() . '@localhost',
            'password' => 'test',
            'first_name' => 'my_name'
        );
        $response = $client->post('/api/user/new', $form);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        
        $user = new User();
        $user = $user->getUser($form['login']);
        
        // Login
        $response = $client->post('/api/user/login', array(
            'login' => 'test',
            'password' => 'test'
        ));
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        
        // delete
        $response = $client->delete('/api/user/' . $user->id, $form);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
    }

    /**
     * @test
     */
    public function getUserProfileRestTest()
    {
        $client = new Test_Client(array(
            array(
                'app' => 'User',
                'regex' => '#^/api/user#',
                'base' => '',
                'sub' => include 'User/urls.php'
            )
        ));
        
        // Change detail
        $form = array(
            'login' => 'LoginName' . rand(),
            'email' => 'info' . rand() . '@localhost',
            'password' => 'test',
            'first_name' => 'my_name'
        );
        $response = $client->post('/api/user/new', $form);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        
        $user = new User();
        $user = $user->getUser($form['login']);
        
        // Login
        $response = $client->post('/api/user/login', array(
            'login' => 'test',
            'password' => 'test'
        ));
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        
        // delete
        $response = $client->get('/api/user/' . $user->id . '/profile');
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
    }

    /**
     * @test
     */
    public function updateUserProfileRestTest()
    {
        $client = new Test_Client(array(
            array(
                'app' => 'User',
                'regex' => '#^/api/user#',
                'base' => '',
                'sub' => include 'User/urls.php'
            )
        ));
        
        // Change detail
        $form = array(
            'login' => 'LoginName' . rand(),
            'email' => 'info' . rand() . '@localhost',
            'password' => 'test',
            'first_name' => 'my_name'
        );
        $response = $client->post('/api/user/new', $form);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        
        $user = new User();
        $user = $user->getUser($form['login']);
        
        // Login
        $response = $client->post('/api/user/login', array(
            'login' => 'test',
            'password' => 'test'
        ));
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        
        // delete
        $response = $client->post('/api/user/' . $user->id . '/profile', array(
            'email' => 'public@mail'
        ));
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
    }

    /**
     * @test
     */
    public function getUserAvatarRestTest()
    {
        $client = new Test_Client(array(
            array(
                'app' => 'User',
                'regex' => '#^/api/user#',
                'base' => '',
                'sub' => include 'User/urls.php'
            )
        ));
        
        // Change detail
        $user = new User();
        $user = $user->getUser('test');
        
        // Login
        $response = $client->post('/api/user/login', array(
            'login' => 'test',
            'password' => 'test'
        ));
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        
        // get
        $response = $client->get('/api/user/' . $user->id . '/avatar');
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        
        // get
        $response = $client->get('/api/user/avatar');
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
    }

    // TODO: maso, 2017: adding test to update avatar
    
    /**
     * @test
     */
    public function assUserGroupRestTest()
    {
        $client = new Test_Client(array(
            array(
                'app' => 'User',
                'regex' => '#^/api/user#',
                'base' => '',
                'sub' => include 'User/urls.php'
            )
        ));
        
        // Change detail
        $user = new User();
        $user = $user->getUser('test');
        
        $group = new Group();
        $group->name = 'test:' . rand();
        $group->create();
        
        // Login
        $response = $client->post('/api/user/login', array(
            'login' => 'test',
            'password' => 'test'
        ));
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        
        // find
        $response = $client->get('/api/user/' . $user->id . '/group/find');
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        
        // create
        $response = $client->post('/api/user/' . $user->id . '/group/new', array(
            'groupId' => $group->id,
            'group' => $group->id
        ));
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        
        // get
        $response = $client->get('/api/user/' . $user->id . '/group/' . $group->id);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        
        // delete
        $response = $client->delete('/api/user/' . $user->id . '/group/' . $group->id);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        
        // create
        $response = $client->post('/api/user/' . $user->id . '/group/new', array(
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
        $client = new Test_Client(array(
            array(
                'app' => 'User',
                'regex' => '#^/api/user#',
                'base' => '',
                'sub' => include 'User/urls.php'
            )
        ));
        
        // Change detail
        $user = new User();
        $user = $user->getUser('test');
        
        $perm = new Role();
        $perm->app = 'test';
        $perm->code_name = 'testRest';
        $perm->create();
        
        // Login
        $response = $client->post('/api/user/login', array(
            'login' => 'test',
            'password' => 'test'
        ));
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        
        // find
        $response = $client->get('/api/user/' . $user->id . '/role/find');
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        
        // get
        $response = $client->post('/api/user/' . $user->id . '/role/new', array(
            'roleId' => $perm->id,
            'role' => $perm->id
        ));
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        
        // get
        $response = $client->get('/api/user/' . $user->id . '/role/' . $perm->id);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        
        // delete
        $response = $client->delete('/api/user/' . $user->id . '/role/' . $perm->id);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
    }
}



