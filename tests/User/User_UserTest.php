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
class User_UserTest extends TestCase
{

    /**
     * @beforeClass
     */
    public static function createDataBase()
    {
        Pluf::start(__DIR__ . '/../conf/config.php');
        $db = Pluf::db();
        $schema = Pluf::factory('Pluf_DB_Schema', $db);
        $models = array(
            'User_Group',
            'User_Account',
            'User_Role',
            'User_Credential',
            'User_Message'
        );
        foreach ($models as $model) {
            $schema->model = Pluf::factory($model);
            $schema->dropTables();
            if (true !== ($res = $schema->createTables())) {
                throw new Exception($res);
            }
        }
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
     * @afterClass
     */
    public static function removeDatabses()
    {
        $db = Pluf::db();
        $schema = Pluf::factory('Pluf_DB_Schema', $db);
        $models = array(
            'User_Group',
            'User_Account',
            'User_Role',
            'User_Credential',
            'User_Message'
        );
        foreach ($models as $model) {
            $schema->model = Pluf::factory($model);
            $schema->dropTables();
        }
    }

    /**
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
     * @test
     */
    public function testHasAppPermissions()
    {
        $user = new User_Account();
        $user = $user->getUser('test');
        $this->assertEquals(true, $user->hasAppPerms('DummyModel'));
        $this->assertEquals(false, $user->hasPerm('DummyModel2'));
    }
}


