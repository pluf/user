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
namespace Pluf\Test\Email;

use Pluf\Test\TestCase;
use Pluf\Test\Client;
use Pluf;
use Pluf_Migration;
use User_Account;
use User_Credential;
use User_Role;
use User_Email;
use Verifier_Service;

class RestTest extends TestCase
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
        $this->client = new Client();
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
            'email' => 'test_' . rand(1, 1000) . '@test.com',
            'type' => 'office'
        );
        $response = $this->client->post('/user/accounts/' . $this->account->id . '/emails', $form);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        $actual = json_decode($response->content, true);
        $this->assertTrue($actual['id'] > 0);
        $this->assertEquals($actual['email'], $form['email']);
        $this->assertEquals($actual['type'], $form['type']);
    }

    private function get_random_email()
    {
        $item = new User_Email();
        $item->email = 'test_' . rand(1, 100000) . '@test.com';
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
        $item = $this->get_random_email();
        $item->create();
        $this->assertFalse($item->isAnonymous(), 'Could not create User_Email');
        // Get item
        $response = $this->client->get('/user/accounts/' . $this->account->id . '/emails/' . $item->id);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
    }

    /**
     *
     * @test
     */
    public function updateRestTest()
    {
        $item = $this->get_random_email();
        $item->create();
        $this->assertFalse($item->isAnonymous(), 'Could not create User_Email');
        // Update email
        $form = array(
            'type' => 'office'
        );
        $response = $this->client->post('/user/accounts/' . $this->account->id . '/emails/' . $item->id, $form);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        $actual = json_decode($response->content, true);
        $this->assertEquals($actual['id'], $item->id);
        $this->assertEquals($actual['email'], $item->email);
        $this->assertEquals($actual['type'], $form['type']);
    }

    /**
     *
     * @test
     */
    public function deleteRestTest()
    {
        $item = $this->get_random_email();
        $item->create();
        $this->assertFalse($item->isAnonymous(), 'Could not create User_Email');
        // delete
        $response = $this->client->delete('/user/accounts/' . $this->account->id . '/emails/' . $item->id);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
    }

    /**
     *
     * @test
     */
    public function findRestTest()
    {
        $response = $this->client->get('/user/accounts/' . $this->account->id . '/emails');
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
    }

    /**
     *
     * @test
     */
    public function verificationRestTest()
    {
        // Email
        $item = $this->get_random_email();
        $item->create();
        $this->assertFalse($item->isAnonymous(), 'Could not create User_Email');

        // Verificaion
        $verification = Verifier_Service::createVerification($item->get_account(), $item);

        // Verify email
        $url = '/user/accounts/' . $this->account->id . '/emails/' . $item->id . '/verifications/' . $verification->code;
        $response = $this->client->post($url);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        $actual = json_decode($response->content, true);
        $this->assertEquals($actual['id'], $item->id);
        $this->assertEquals($actual['email'], $item->email);
        $this->assertEquals($actual['type'], $item->type);
        $this->assertTrue($actual['is_verified']);
    }
}



