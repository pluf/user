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
class User_Password_Token_ChangePassTest extends TestCase
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
    }

    /**
     * @afterClass
     */
    public static function removeDatabses()
    {
        $m = new Pluf_Migration(Pluf::f('installed_apps'));
        $m->unInstall();
    }

    public function testChangePassByOld()
    {
        // Create user
        $user = new User();
        $user->login = 'test' . rand();
        $user->first_name = 'test';
        $user->last_name = 'test';
        $user->email = 'test' . rand() . '@example.com';
        $user->setPassword('test');
        $user->active = true;
        if (true !== $user->create()) {
            throw new Exception();
        }
        
        $view = new User_Views_Password();
        $newPassword = 'test' . rand();
        
        // Create token by email
        $match = array();
        global $_REQUEST;
        global $_SERVER;
        $_REQUEST = array(
            'old' => 'test',
            'new' => $newPassword
        );
        $_SERVER['REQUEST_URI'] = 'http://localhost/test';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REMOTE_ADDR'] = 'localhost';
        $request = new Pluf_HTTP_Request('/');
        $request->user = $user;
        
        $res = $view->password($request, $match);
        $this->assertNotNull($res);
        
        /**
         *
         * @var User $newUser
         */
        $newUser = Pluf::factory('User', $user->id);
        $this->assertFalse($newUser->checkPassword('test'));
        $this->assertTrue($newUser->checkPassword($newPassword));
    }

    public function testChangePassByToken()
    {
        // Create user
        $user = new User();
        $user->login = 'test' . rand();
        $user->first_name = 'test';
        $user->last_name = 'test';
        $user->email = 'test' . rand() . '@example.com';
        $user->setPassword('test');
        $user->active = true;
        if (true !== $user->create()) {
            throw new Exception();
        }
        
        $token = new User_PasswordToken();
        $token->user = $user;
        if (! $token->create()) {
            throw new Exception('Not able to create token');
        }
        
        $view = new User_Views_Password();
        $newPassword = 'test' . rand();
        
        // Create token by email
        $match = array();
        global $_REQUEST;
        global $_SERVER;
        $_REQUEST = array(
            'token' => $token->token,
            'new' => $newPassword
        );
        $_SERVER['REQUEST_URI'] = 'http://localhost/test';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REMOTE_ADDR'] = 'localhost';
        $request = new Pluf_HTTP_Request('/');
        $request->user = $user;
        
        $res = $view->password($request, $match);
        $this->assertNotNull($res);
        
        /**
         *
         * @var User $newUser
         */
        $newUser = Pluf::factory('User', $user->id);
        $this->assertFalse($newUser->checkPassword('test'));
        $this->assertTrue($newUser->checkPassword($newPassword));
    }
}


