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
namespace Pluf\Test\Address;

use Pluf\Test\TestCase;
use Pluf\Test\Client;
use Pluf\Exception;
use Pluf;
use Pluf_Migration;
use User_Account;
use User_Credential;
use User_Address;
use User_Role;

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
        $m = new Pluf_Migration();
        $m->uninstall();
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
            'country' => 'country-' . rand(),
            'province' => 'province-' . rand(),
            'city' => 'city-' . rand(),
            'address' => 'address-' . rand(),
            'postal_code' => rand(111111111, 999999999) . '0',
            // 'location' => 'POINT(100 100.1)',
            'type' => 'office'
        );
        $response = $this->client->post('/user/accounts/' . $this->account->id . '/addresses', $form);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        $actual = json_decode($response->content, true);
        $this->assertTrue($actual['id'] > 0);
        $this->assertEquals($actual['country'], $form['country']);
        $this->assertEquals($actual['province'], $form['province']);
        $this->assertEquals($actual['city'], $form['city']);
        $this->assertEquals($actual['address'], $form['address']);
        $this->assertEquals($actual['postal_code'], $form['postal_code']);
        // $this->assertEquals($actual['location'], $form['location']);
        $this->assertEquals($actual['type'], $form['type']);
    }

    private function get_random_address()
    {
        $item = new User_Address();
        $item->country = 'Country';
        $item->province = 'Province';
        $item->city = 'City';
        $item->address = 'Test Address';
        $item->postal_code = rand(111111111, 999999999) . '0';
        // $item->location = 'POINT(100 100.1)';
        $item->type = 'office';
        $item->account_id = $this->account;
        return $item;
    }

    /**
     *
     * @test
     */
    public function getRestTest()
    {
        $item = $this->get_random_address();
        $item->create();
        $this->assertFalse($item->isAnonymous(), 'Could not create User_Address');
        // Get item
        $response = $this->client->get('/user/accounts/' . $this->account->id . '/addresses/' . $item->id);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
    }

    /**
     *
     * @test
     */
    public function updateRestTest()
    {
        $item = $this->get_random_address();
        $item->create();
        $this->assertFalse($item->isAnonymous(), 'Could not create User_Address');
        // Update address
        $form = array(
            'country' => 'country-' . rand(),
            'province' => 'province-' . rand(),
            'city' => 'city-' . rand(),
            'address' => 'address-' . rand(),
            'postal_code' => rand(111111111, 999999999) . '0',
            // 'location' => 'POINT(100 100.1)',
            'type' => 'home'
        );
        $response = $this->client->post('/user/accounts/' . $this->account->id . '/addresses/' . $item->id, $form);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        $actual = json_decode($response->content, true);
        $this->assertTrue($actual['id'] > 0);
        $this->assertEquals($actual['country'], $form['country']);
        $this->assertEquals($actual['province'], $form['province']);
        $this->assertEquals($actual['city'], $form['city']);
        $this->assertEquals($actual['address'], $form['address']);
        $this->assertEquals($actual['postal_code'], $form['postal_code']);
        // $this->assertEquals($actual['location'], $form['location']);
        $this->assertEquals($actual['type'], $form['type']);
    }

    /**
     *
     * @test
     */
    public function deleteRestTest()
    {
        $item = $this->get_random_address();
        $item->create();
        $this->assertFalse($item->isAnonymous(), 'Could not create User_Address');
        // delete
        $response = $this->client->delete('/user/accounts/' . $this->account->id . '/addresses/' . $item->id);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
    }

    /**
     *
     * @test
     */
    public function findRestTest()
    {
        $response = $this->client->get('/user/accounts/' . $this->account->id . '/addresses');
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
    }
}



