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
namespace Pluf\Test\Account\REST;

use Pluf\Test\TestCase;
use Pluf\Test\Client;
use Pluf\Exception;
use Pluf;
use Pluf_Migration;
use User_Role;
use User_Group;
use User_Account;
use User_Credential;

class AccountActivationTest extends TestCase
{

    private static $client = null;

    private static $config = null;

    /**
     *
     * @beforeClass
     */
    public static function createDataBase()
    {
        Pluf::start(self::$config = include (__DIR__ . '/../../conf/config.php'));
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

        $user = new User_Account();
        $user = $user->getUser('test');
        self::assertTrue($user->hasPerm('tenant.owner'));

        self::$client = new Client();
    }

    /**
     *
     * @afterClass
     */
    public static function removeDatabses()
    {
        $m = new Pluf_Migration();
        $m->uninstall();
    }

    /**
     *
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
        $response = self::$client->post('/user/accounts', $form);
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
        $response = self::$client->post('/user/accounts', $form);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        $actual = json_decode($response->content, true);
        $this->assertTrue($actual['is_active']);
    }

    /**
     *
     * @test
     */
    public function ownerCreateAccount()
    {
        // login
        $response = self::$client->post('/user/login', array(
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
        $response = self::$client->post('/user/accounts', $form);
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
        $response = self::$client->post('/user/accounts', $form);
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
        $response = self::$client->post('/user/accounts', $form);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        $actual = json_decode($response->content, true);

        // logout
        $response = self::$client->post('/user/logout');
        $this->assertNotNull($response);
    }
}



