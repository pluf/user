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

class ModelTest extends TestCase
{

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

        $per = User_Role::getFromString('tenant.owner');
        $user->setAssoc($per);
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
     * @before
     */
    public function init()
    {
        $this->account = User_Account::getUser('test');
    }

    /**
     *
     * @test
     */
    public function createNewAddress()
    {
        $ad = $this->get_random_address();
        $this->assertTrue($ad->create(), 'Impossible to create address');
    }

    /**
     *
     * @test
     */
    public function getAccountOfAddress()
    {
        $address = $this->get_random_address();
        $this->assertTrue($address->create(), 'Impossible to create address');

        $account = $address->get_account();
        $this->assertNotNull($account);
    }
}


