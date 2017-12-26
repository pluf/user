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
class User_Password_Token_MainTest extends TestCase
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

    /**
     * @test
     */
    public function testCreateToken()
    {
        $user = new User();
        $user = $user->getUser('test');
        
        // creates new
        $token = new User_PasswordToken();
        $token->user = $user;
        $token->create();
        $this->assertNotNull($token->id);
        
        // get tokne user
        $tokenStored = new User_PasswordToken($token->id);
        $this->assertEquals($tokenStored->user, $user->id);
        $this->assertNotNull($tokenStored->token);
    }

    public function testCreateTokenForMail()
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
        
        // Create token by email
        $match = array();
        global $_REQUEST;
        global $_SERVER;
        $_REQUEST = array(
            'email' => $user->email
        );
        $_SERVER['REQUEST_URI'] = 'http://localhost/test';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REMOTE_ADDR'] = 'localhost';
        $request = new Pluf_HTTP_Request('/');
        $request->user = new User();
        
        $res = $view->password($request, $match);
        $this->assertNotNull($res);
        
        $token = new User_PasswordToken();
        $sql = new Pluf_SQL('user=%s', array(
            $user->id
        ));
        $token = $token->getOne($sql->gen());
        $this->assertNotNull($token);
    }

    public function testCreateTokenForLogin()
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
        
        // Create token by email
        $match = array();
        global $_REQUEST;
        global $_SERVER;
        $_REQUEST = array(
            'login' => $user->login
        );
        $_SERVER['REQUEST_URI'] = 'http://localhost/test';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REMOTE_ADDR'] = 'localhost';
        $request = new Pluf_HTTP_Request('/');
        $request->user = new User();
        
        $res = $view->password($request, $match);
        $this->assertNotNull($res);
        
        $token = new User_PasswordToken();
        $sql = new Pluf_SQL('user=%s', array(
            $user->id
        ));
        $token = $token->getOne($sql->gen());
        $this->assertNotNull($token);
    }

    public function testCreateDoubleTokenForLogin()
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
        
        // Create token by email
        $match = array();
        global $_REQUEST;
        global $_SERVER;
        $_REQUEST = array(
            'login' => $user->login
        );
        $_SERVER['REQUEST_URI'] = 'http://localhost/test';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REMOTE_ADDR'] = 'localhost';
        $request = new Pluf_HTTP_Request('/');
        $request->user = new User();
        
        for ($i = 1; $i < 4; $i ++) {
            $res = $view->password($request, $match);
            $this->assertNotNull($res);
            
            $token = new User_PasswordToken();
            $sql = new Pluf_SQL('user=%s', array(
                $user->id
            ));
            $token = $token->getOne($sql->gen());
            $this->assertNotNull($token);
        }
    }
}


