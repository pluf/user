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
require_once 'Pluf.php';

/**
 *
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 */
class User_Phone_RestTest extends TestCase
{

    var $client;
    private $account;

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

//         $per = User_Role::getFromString('tenant.owner');
//         $user->setAssoc($per);
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
     *
     * @before
     */
    public function init()
    {
        $this->client = new Test_Client(array(
            array(
                'app' => 'User',
                'regex' => '#^/user#',
                'base' => '',
                'sub' => include 'User/urls-v2.php'
            )
        ));
        // login
        $this->client->post('/user/login', array(
            'login' => 'test',
            'password' => 'test'
        ));
        // account
        $this->account = User_Account::getUser('test');
    }

    /**
     *
     * @test
     */
    public function createRestTest()
    {
        $form = array(
            'phone' => '0' . rand(1111111111,9999999999),
            'type' => 'office'
        );
        $response = $this->client->post('/user/accounts/' . $this->account->id . '/phones', $form);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        $actual = json_decode($response->content, true);
        $this->assertTrue($actual['id'] > 0);
        $this->assertEquals($actual['phone'], $form['phone']);
        $this->assertEquals($actual['type'], $form['type']);
    }

    private function get_random_phone(){
        $item = new User_Phone();
        $item->phone = '0' . rand(1111111111,9999999999);
        $item->type = 'home';
        $item->account_id = $this->account;
        return $item;
    }
    
    /**
     *
     * @test
     */
    public function getRestTest()
    {
        $item = $this->get_random_phone();
        $item->create();
        $this->assertFalse($item->isAnonymous(), 'Could not create User_Phone');
        // Get item
        $response = $this->client->get('/user/accounts/' . $this->account->id . '/phones/' . $item->id);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
    }

    /**
     *
     * @test
     */
    public function updateRestTest()
    {
        $item = $this->get_random_phone();
        $item->create();
        $this->assertFalse($item->isAnonymous(), 'Could not create User_Phone');
        // Update phone
        $form = array(
            'type' => 'office'
        );
        $response = $this->client->post('/user/accounts/' . $this->account->id . '/phones/' . $item->id, $form);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        $actual = json_decode($response->content, true);
        $this->assertEquals($actual['id'], $item->id);
        $this->assertEquals($actual['phone'], $item->phone);
        $this->assertEquals($actual['type'], $form['type']);
    }

    /**
     *
     * @test
     */
    public function deleteRestTest()
    {
        $item = $this->get_random_phone();
        $item->create();
        $this->assertFalse($item->isAnonymous(), 'Could not create User_Phone');
        // delete
        $response = $this->client->delete('/user/accounts/' . $this->account->id . '/phones/' . $item->id);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
    }

    /**
     *
     * @test
     */
    public function findRestTest()
    {
        $response = $this->client->get('/user/accounts/' . $this->account->id . '/phones');
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
    }

    /**
     *
     * @test
     */
    public function verificationRestTest()
    {
        // Phone
        $item = $this->get_random_phone();
        $item->create();
        $this->assertFalse($item->isAnonymous(), 'Could not create User_Phone');
        
        // Verificaion
        $verification = Verifier_Service::createVerification($item->get_account(), $item);
        
        // Verify phone
        $url = '/user/accounts/' . $this->account->id . '/phones/' . $item->id . '/verifications/' . $verification->code;
        $response = $this->client->post($url);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        $actual = json_decode($response->content, true);
        $this->assertEquals($actual['id'], $item->id);
        $this->assertEquals($actual['phone'], $item->phone);
        $this->assertEquals($actual['type'], $item->type);
        $this->assertTrue($actual['is_verified']);
    }
    
}



