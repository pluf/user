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
namespace Pluf\Test\Middleware;

use Pluf\Test\TestCase;
use Pluf\Exception;
use Pluf;
use Pluf_Migration;
use Pluf_HTTP_Request;
use User_Account;
use User_Role;
use User_Group;
use User_Credential;
use User_Middleware_BasicAuth;


class BasicAuthTest extends TestCase
{

    /**
     * @beforeClass
     */
    public static function createDataBase()
    {
        Pluf::start(__DIR__ . '/../conf/config.php');
        $m = new Pluf_Migration();
        $m->install();
        
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
        
        $role = User_Role::getFromString('tenant.owner');
        $user->setAssoc($role);
    }

    /**
     * @afterClass
     */
    public static function removeDatabses()
    {
        $m = new Pluf_Migration();
        $m->uninstall();
    }

    /**
     * @test
     */
    public function shouldBeAclass()
    {
        $ba = new User_Middleware_BasicAuth();
        $this->assertNotNull($ba);
    }

    /**
     * @test
     */
    public function shouldHandleLoginInHeader()
    {
        $query = '/example/resource';
//         $_SERVER = array();
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
        $this->assertFalse($res, 'Middleware must not intropt the process');
        $this->assertFalse($request->user->isAnonymous(), 'Authentication not work');
    }

    /**
     * @test
     */
    public function shouldNotLoginIfNoItemExist()
    {
        $query = '/example/resource';
//         $_SERVER = array();
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = 'http://localhost/example/resource';
        $_SERVER['REMOTE_ADDR'] = 'not set';
        $_SERVER['HTTP_HOST'] = 'localhost';
        unset($_SERVER['PHP_AUTH_USER']);
        unset($_SERVER['PHP_AUTH_PW']);
        $GLOBALS['_PX_uniqid'] = 'example';
        
        $ba = new User_Middleware_BasicAuth();
        $request = new Pluf_HTTP_Request($query);
        
        // empty view
        $request->view = array(
            'ctrl' => array()
        );
        
        $request->user = new User_Account();
        $res = $ba->process_request($request);
        $this->assertFalse($res, 'Middleware must not intropt the process');
        $this->assertTrue($request->user->isAnonymous(), 'Authentication not work');
    }

    /**
     * @test
     */
    public function shouldIgnoreUnauthorizedUser()
    {
        $query = '/example/resource';
//         $_SERVER = array();
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
        
        $request->user = new User_Account();
        $res = $ba->process_request($request);
        $this->assertFalse($res, 'Middleware must not intropt the process');
        $this->assertTrue($request->user->isAnonymous(), 'Authentication not work');
    }
}


