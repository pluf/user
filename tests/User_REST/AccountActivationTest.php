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
class User_REST_AccountActivationTest extends TestCase
{

    private static $client = null;
    private static $config = null;
    
    /**
     * @beforeClass
     */
    public static function createDataBase()
    {
        static::$config = include(__DIR__ . '/../conf/config.php');
        Pluf::start(static::$config);
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
    public function anonymouseCreateAccount()
    {
        // auto activation -> false
        static::$config['user_account_auto_activate'] = false;
        Pluf::start(static::$config);
        
        $form = array(
            'login' => 'LoginName' . rand(),
            'password' => 'test',
            'is_active' => true
        );
        $response = self::$client->post('/api/v2/user/accounts', $form);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        $actual = json_decode($response->content, true);
        
        // auto activation -> true
        static::$config['user_account_auto_activate'] = true;
        Pluf::start(static::$config);
        
        $form = array(
            'login' => 'LoginName' . rand(),
            'password' => 'test'
        );
        $response = self::$client->post('/api/v2/user/accounts', $form);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        $actual = json_decode($response->content, true);
        $this->assertTrue($actual['is_active']);
    }

    /**
     * @test
     */
    public function ownerCreateAccount()
    {
        // login
        $response = self::$client->post('/api/v2/user/login', array(
            'login' => 'test',
            'password' => 'test'
        ));
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        
        
        // auto activation -> false, is_active is set
        static::$config['user_account_auto_activate'] = false;
        Pluf::start(static::$config);
        
        $form = array(
            'login' => 'LoginName' . rand(),
            'password' => 'test',
            'is_active' => true
        );
        $response = self::$client->post('/api/v2/user/accounts', $form);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        $actual = json_decode($response->content, true);
        $this->assertTrue($actual['is_active']);
        
        // auto activation is true, is_active is not set
        static::$config['user_account_auto_activate'] = true;
        Pluf::start(static::$config);
        
        $form = array(
            'login' => 'LoginName' . rand(),
            'password' => 'test'
        );
        $response = self::$client->post('/api/v2/user/accounts', $form);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        $actual = json_decode($response->content, true);
        $this->assertTrue($actual['is_active']);
        
        
        // auto activation is true, is_active is false
        static::$config['user_account_auto_activate'] = true;
        Pluf::start(static::$config);
        
        $form = array(
            'login' => 'LoginName' . rand(),
            'password' => 'test',
            'is_active' => false
        );
        $response = self::$client->post('/api/v2/user/accounts', $form);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        $actual = json_decode($response->content, true);
        
        // logout
        $response = self::$client->post('/api/v2/user/logout');
        $this->assertNotNull($response);
    }

}



