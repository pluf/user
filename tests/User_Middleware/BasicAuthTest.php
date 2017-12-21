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
class User_Middleware_BasicAuthTest extends TestCase
{

    /**
     * @beforeClass
     */
    public static function createDataBase()
    {
        Pluf::start(dirname(__FILE__) . '/../conf/mysql.user.config.php');
        $m = new Pluf_Migration(array(
            'Pluf',
            'User',
            'Group',
            'Role'
        ));
        $m->install();
        
        $user = new User();
        $user->login = 'test';
        $user->first_name = 'test';
        $user->last_name = 'test';
        $user->email = 'toto@example.com';
        $user->setPassword('test');
        $user->active = true;
        $user->administrator = true;
        if (true !== $user->create()) {
            throw new Exception();
        }
        
        $role = Role::getFromString('Pluf.owner');
        $user->setAssoc($role);
    }

    /**
     * @afterClass
     */
    public static function removeDatabses()
    {
        $m = new Pluf_Migration(array(
            'Pluf',
            'User',
            'Group',
            'Role'
        ));
        $m->unInstall();
    }

    /**
     * @test
     */
    public function shouldBeAclass()
    {
        $ba = new User_Middleware_BasicAuth();
        Test_Assert::assertNotNull($ba);
    }

    /**
     * @test
     */
    public function shouldHandleLoginInHeader()
    {
        $query = '/example/resource';
        $_SERVER = array();
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = 'http://localhost/example/resource';
        $_SERVER['REMOTE_ADDR'] = 'not set';
        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['PHP_AUTH_USER'] = 'test';
        $_SERVER['PHP_AUTH_PW'] = 'test';
        $GLOBALS['_PX_uniqid'] = 'example';
        
        $ba = new User_Middleware_BasicAuth();
        $request = new Pluf_HTTP_Request($query);
        
        // empty view
        $request->view = array(
            'ctrl' => array()
        );
        
        $res = $ba->process_request($request);
        Test_Assert::assertFalse($res, 'Middleware must not intropt the process');
        Test_Assert::assertFalse($request->user->isAnonymous(), 'Authentication not work');
    }

    /**
     * @test
     */
    public function shouldNotLoginIfNoItemExist()
    {
        $query = '/example/resource';
        $_SERVER = array();
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = 'http://localhost/example/resource';
        $_SERVER['REMOTE_ADDR'] = 'not set';
        $_SERVER['HTTP_HOST'] = 'localhost';
        // $_SERVER['PHP_AUTH_USER'] = 'test';
        // $_SERVER['PHP_AUTH_PW'] = 'test';
        $GLOBALS['_PX_uniqid'] = 'example';
        
        $ba = new User_Middleware_BasicAuth();
        $request = new Pluf_HTTP_Request($query);
        
        // empty view
        $request->view = array(
            'ctrl' => array()
        );
        
        $request->user = new User();
        $res = $ba->process_request($request);
        Test_Assert::assertFalse($res, 'Middleware must not intropt the process');
        Test_Assert::assertTrue($request->user->isAnonymous(), 'Authentication not work');
    }

    /**
     * @test
     */
    public function shouldIgnoreUnauthorizedUser()
    {
        $query = '/example/resource';
        $_SERVER = array();
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = 'http://localhost/example/resource';
        $_SERVER['REMOTE_ADDR'] = 'not set';
        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['PHP_AUTH_USER'] = 'test';
        $_SERVER['PHP_AUTH_PW'] = 'test123';
        $GLOBALS['_PX_uniqid'] = 'example';
        
        $ba = new User_Middleware_BasicAuth();
        $request = new Pluf_HTTP_Request($query);
        
        // empty view
        $request->view = array(
            'ctrl' => array()
        );
        
        $request->user = new User();
        $res = $ba->process_request($request);
        Test_Assert::assertFalse($res, 'Middleware must not intropt the process');
        Test_Assert::assertTrue($request->user->isAnonymous(), 'Authentication not work');
    }
}

