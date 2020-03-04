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
namespace Pluf\User\AccountTest;

use PHPUnit\Framework\TestCase;
use Pluf\Bootstrap;
use Pluf\Migration;
use Pluf\User\Account;
use Pluf\Exception;
use Pluf\User\Credential;
use Pluf\User\Shortcuts;

class ModelTest extends TestCase
{

    /**
     *
     * @beforeClass
     */
    public static function createDataBase()
    {
        Bootstrap::start(__DIR__ . '/../conf/config.php');
        $m = new Migration();
        $m->install();
        $m->init();

        // Test user
        $user = new Account();
        $user->login = 'test';
        $user->is_active = true;
        if (true !== $user->create()) {
            throw new Exception();
        }
        // Credential of user
        $credit = new Credential();
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
        $m = new Migration();
        $m->unInstall();
    }

    /**
     *
     * @test
     */
    public function shouldPossibleCreateNew()
    {
        $user = new Account();
        $user->login = 'Random' . rand();
        $user->email = 'Hi@test.com';
        $this->assertTrue($user->create(), 'Impossible to create user');
    }

    /**
     *
     * @test
     */
    public function shouldPossibleToGetMessages()
    {
        $user = new Account();
        $user = $user->getUser('test');
        $mess = $user->get_messages_list();
        $this->assertEquals(0, $mess->count());
    }

    /**
     *
     * @test
     */
    public function shouldPossibleToRoles()
    {
        $user = new Account();
        $user = $user->getUser('test');
        $roles = $user->get_roles_list();
        $this->assertEquals(0, $roles->count());
    }

    /**
     *
     * @test
     */
    public function shouldPossibleToGetGroups()
    {
        $user = new Account();
        $user = $user->getUser('test');
        $groups = $user->get_groups_list();
        $this->assertEquals(0, $groups->count());
    }

    /**
     *
     * @expectedException \Pluf\Exception
     * @test
     */
    public function testUniqueLogin()
    {
        // Test user
        $user = new Account();
        $user->login = 'test';
        $user->is_active = true;
        // Test user already exists
        $user->create();
    }

    /**
     * TODO: maso, 2017: must throw Pluf_Exception_Form
     *
     * @expectedException \Pluf\Exception
     * @test
     */
    public function testValidationUnique()
    {
        // Test user already exists
        $user = new Account();
        $user->login = 'test';
        $user->is_active = true;

        $form = Shortcuts::getFormForModel($user, $user->getData(), array());
        $form->save();
    }
}


