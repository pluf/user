<?php
// /*
//  * This file is part of Pluf Framework, a simple PHP Application Framework.
//  * Copyright (C) 2010-2020 Phoinex Scholars Co. (http://dpq.co.ir)
//  *
//  * This program is free software: you can redistribute it and/or modify
//  * it under the terms of the GNU General Public License as published by
//  * the Free Software Foundation, either version 3 of the License, or
//  * (at your option) any later version.
//  *
//  * This program is distributed in the hope that it will be useful,
//  * but WITHOUT ANY WARRANTY; without even the implied warranty of
//  * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
//  * GNU General Public License for more details.
//  *
//  * You should have received a copy of the GNU General Public License
//  * along with this program. If not, see <http://www.gnu.org/licenses/>.
//  */
// use PHPUnit\Framework\TestCase;
// use PHPUnit\Framework\IncompleteTestError;
// require_once 'Pluf.php';

// /**
//  * @backupGlobals disabled
//  * @backupStaticAttributes disabled
//  */
// class User_Password_Token_MainTest extends TestCase
// {

//     /**
//      * @beforeClass
//      */
//     public static function createDataBase()
//     {
//         Pluf::start(__DIR__ . '/../conf/config.php');
//         $m = new Pluf_Migration(Pluf::f('installed_apps'));
//         $m->install();
//         $m->init();
        
//         // Test user
//         $user = new User_Account();
//         $user->login = 'test';
//         $user->is_active = true;
//         if (true !== $user->create()) {
//             throw new Exception();
//         }
//         // Credential of user
//         $credit = new User_Credential();
//         $credit->setFromFormData(array(
//             'account_id' => $user->id
//         ));
//         $credit->setPassword('test');
//         if (true !== $credit->create()) {
//             throw new Exception();
//         }
        
//         $per = User_Role::getFromString('Pluf.owner');
//         $user->setAssoc($per);
//     }

//     /**
//      * @afterClass
//      */
//     public static function removeDatabses()
//     {
//         $m = new Pluf_Migration(Pluf::f('installed_apps'));
//         $m->unInstall();
//     }

//     /**
//      * @test
//      */
//     public function testCreateToken()
//     {
//         $user = new User_Account();
//         $user = $user->getUser('test');
        
//         // creates new
//         $token = new User_Token();
//         $token->account_id = $user;
//         $token->type = 'test';
//         $token->create();
//         $this->assertNotNull($token->id);
        
//         // get token user
//         $tokenStored = new User_Token($token->id);
//         $this->assertEquals($tokenStored->account_id, $user->id);
//         $this->assertNotNull($tokenStored->token);
//     }

//     public function testCreateTokenForMail()
//     {
//         // Create user
//         $user = new User_Account();
//         $user->login = 'test' . rand();
//         $user->is_active = true;
//         if (true !== $user->create()) {
//             throw new Exception();
//         }
//         // Credential of user
//         $credit = new User_Credential();
//         $credit->setFromFormData(array(
//             'account_id' => $user->id
//         ));
//         $credit->setPassword('test');
//         if (true !== $credit->create()) {
//             throw new Exception();
//         }
        
//         $view = new User_Views_Password();
        
//         // Create token by email
//         $match = array();
//         global $_REQUEST;
//         global $_SERVER;
//         $_REQUEST = array(
//             'email' => $user->email
//         );
//         $_SERVER['REQUEST_URI'] = 'http://localhost/test';
//         $_SERVER['REQUEST_METHOD'] = 'POST';
//         $_SERVER['REMOTE_ADDR'] = 'localhost';
//         $request = new Pluf_HTTP_Request('/');
//         $request->user = new User_Account();
        
//         $res = $view->password($request, $match);
//         $this->assertNotNull($res);
        
//         $token = new User_Token();
//         $sql = new Pluf_SQL('account_id=%s', array(
//             $user->id
//         ));
//         $token = $token->getOne($sql->gen());
//         $this->assertNotNull($token);
//     }

//     public function testCreateTokenForLogin()
//     {
//         // Create user
//         $user = new User_Account();
//         $user->login = 'test' . rand();
//         $user->is_active = true;
//         if (true !== $user->create()) {
//             throw new Exception();
//         }
//         // Credential of user
//         $credit = new User_Credential();
//         $credit->setFromFormData(array(
//             'account_id' => $user->id
//         ));
//         $credit->setPassword('test');
//         if (true !== $credit->create()) {
//             throw new Exception();
//         }
        
//         $view = new User_Views_Password();
        
//         // Create token by email
//         $match = array();
//         global $_REQUEST;
//         global $_SERVER;
//         $_REQUEST = array(
//             'login' => $user->login
//         );
//         $_SERVER['REQUEST_URI'] = 'http://localhost/test';
//         $_SERVER['REQUEST_METHOD'] = 'POST';
//         $_SERVER['REMOTE_ADDR'] = 'localhost';
//         $request = new Pluf_HTTP_Request('/');
//         $request->user = new User_Account();
        
//         $res = $view->password($request, $match);
//         $this->assertNotNull($res);
        
//         $token = new User_Token();
//         $sql = new Pluf_SQL('account_id=%s', array(
//             $user->id
//         ));
//         $token = $token->getOne($sql->gen());
//         $this->assertNotNull($token);
//     }

//     public function testCreateDoubleTokenForLogin()
//     {
//         // Create user
//         $user = new User_Account();
//         $user->login = 'test' . rand();
//         $user->is_active = true;
//         if (true !== $user->create()) {
//             throw new Exception();
//         }
//         // Credential of user
//         $credit = new User_Credential();
//         $credit->setFromFormData(array(
//             'account_id' => $user->id
//         ));
//         $credit->setPassword('test');
//         if (true !== $credit->create()) {
//             throw new Exception();
//         }
        
//         $view = new User_Views_Password();
        
//         // Create token by email
//         $match = array();
//         global $_REQUEST;
//         global $_SERVER;
//         $_REQUEST = array(
//             'login' => $user->login
//         );
//         $_SERVER['REQUEST_URI'] = 'http://localhost/test';
//         $_SERVER['REQUEST_METHOD'] = 'POST';
//         $_SERVER['REMOTE_ADDR'] = 'localhost';
//         $request = new Pluf_HTTP_Request('/');
//         $request->user = new User_Account();
        
//         for ($i = 1; $i < 4; $i ++) {
//             $res = $view->password($request, $match);
//             $this->assertNotNull($res);
            
//             $token = new User_Token();
//             $sql = new Pluf_SQL('account_id=%s', array(
//                 $user->id
//             ));
//             $token = $token->getOne($sql->gen());
//             $this->assertNotNull($token);
//         }
//     }
// }


