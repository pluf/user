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
namespace Pluf\Test\Account;

use Pluf\Test\TestCase;
use Pluf\Exception;
use Pluf;
use Pluf_Migration;
use User_Role;
use User_Group;
use User_Account;
use User_Credential;

class AccountModelTest extends TestCase
{

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

        $perms = array();
        for ($i = 1; $i <= 10; $i ++) {
            $perm = new User_Role();
            $perm->application = 'DummyModel';
            $perm->code_name = 'code-' . $i;
            $perm->name = 'code-' . $i;
            $perm->description = 'code-' . $i;
            $perm->create();
            $perms[] = clone ($perm);
        }
        $groups = array();
        for ($i = 1; $i <= 10; $i ++) {
            $group = new User_Group();
            $group->name = 'Group ' . $i;
            $group->description = 'Group ' . $i;
            $group->create();
            $groups[] = clone ($group);
        }
        $groups[0]->setAssoc($perms[0]);
        $groups[0]->setAssoc($perms[1]);
        $groups[0]->setAssoc($perms[2]);
        $groups[0]->setAssoc($perms[3]);
        $groups[1]->setAssoc($perms[0]); // again perm "1"
        $groups[0]->setAssoc($perms[4]);
        $groups[0]->setAssoc($perms[5]);

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

        $user->setAssoc($groups[0]);
        $user->setAssoc($groups[1]);
        $user->setAssoc($perms[7]);
        $user->setAssoc($perms[8]);
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
    public function testGetPermissions()
    {
        $user = new User_Account();
        $user = $user->getUser('test');
        $a = $user->getAllRoles();
        $this->assertEquals(8, count($a));
    }

    /**
     *
     * @test
     */
    public function testHasPermission()
    {
        $user = new User_Account();
        $user = $user->getUser('test');
        $this->assertEquals(true, $user->hasPerm('DummyModel.code-5'));
        $this->assertEquals(false, $user->hasPerm('DummyModel.code-7'));

        $user->is_active = false;
        $this->assertEquals(false, $user->hasPerm('DummyModel.code-5'));
    }

    /**
     *
     * @test
     */
    public function testHasAppPermissions()
    {
        $user = new User_Account();
        $user = $user->getUser('test');
        $this->assertEquals(true, $user->hasAppPerms('DummyModel'));
        $this->assertEquals(false, $user->hasPerm('DummyModel2'));
    }

    /**
     *
     * @test
     */
    public function shouldPossibleCreateNew()
    {
        $user = new User_Account();
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
        $user = new User_Account();
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
        $user = new User_Account();
        $user = $user->getUser('test');
        $roles = $user->get_roles_list();
        $this->assertTrue($roles->count() > 0);
    }

    /**
     *
     * @test
     */
    public function shouldPossibleToGetGroups()
    {
        $user = new User_Account();
        $user = $user->getUser('test');
        $groups = $user->get_groups_list();
        $this->assertTrue($groups->count() > 0);
    }

    /**
     *
     * @expectedException \Pluf\Exception
     * @test
     */
    public function testUniqueLogin()
    {
        // Test user
        $user = new User_Account();
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
        $user = new User_Account();
        $user->login = 'test';
        $user->is_active = true;

        Pluf::loadFunction('Pluf_Shortcuts_GetFormForModel');
        $form = Pluf_Shortcuts_GetFormForModel($user, $user->getData(), array());
        $form->save();
    }
}


