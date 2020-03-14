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
// namespace Pluf\Test\PasswordToken;

// use Pluf\Test\TestCase;
// use Pluf\Test\Client;
// use Pluf\Exception;
// use Pluf;
// use Pluf_Migration;
// use User_Account;
// use User_Credential;
// use User_Role;
// use User_group;
// use User_Phone;
// use Verifier_Service;

// class ChangePassTest extends TestCase
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
        
//         $per = User_Role::getFromString('tenant.owner');
//         $user->setAssoc($per);
//     }

//     /**
//      * @afterClass
//      */
//     public static function removeDatabses()
//     {
//         $m = new Pluf_Migration(Pluf::f('installed_apps'));
//         $m->uninstall();
//     }

//     public function testChangePassByOld()
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
//         $newPassword = 'test' . rand();
        
//         // Create token by email
//         $match = array();
//         global $_REQUEST;
//         global $_SERVER;
//         $_REQUEST = array(
//             'old' => 'test',
//             'new' => $newPassword
//         );
//         $_SERVER['REQUEST_URI'] = 'http://localhost/test';
//         $_SERVER['REQUEST_METHOD'] = 'POST';
//         $_SERVER['REMOTE_ADDR'] = 'localhost';
//         $request = new Pluf_HTTP_Request('/');
//         $request->user = $user;
        
//         $res = $view->password($request, $match);
//         $this->assertNotNull($res);
        
//         /**
//          *
//          * @var User $newUser
//          */
//         $newUser = Pluf::factory('User_Account', $user->id);
//         $this->assertFalse($newUser->checkPassword('test'));
//         $this->assertTrue($newUser->checkPassword($newPassword));
//     }

//     public function testChangePassByToken()
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
        
//         $token = new User_Token();
//         $token->account_id = $user;
//         if (! $token->create()) {
//             throw new Exception('Not able to create token');
//         }
        
//         $view = new User_Views_Password();
//         $newPassword = 'test' . rand();
        
//         // Create token by email
//         $match = array();
//         global $_REQUEST;
//         global $_SERVER;
//         $_REQUEST = array(
//             'token' => $token->token,
//             'new' => $newPassword
//         );
//         $_SERVER['REQUEST_URI'] = 'http://localhost/test';
//         $_SERVER['REQUEST_METHOD'] = 'POST';
//         $_SERVER['REMOTE_ADDR'] = 'localhost';
//         $request = new Pluf_HTTP_Request('/');
//         $request->user = $user;
        
//         $res = $view->password($request, $match);
//         $this->assertNotNull($res);
        
//         /**
//          *
//          * @var User $newUser
//          */
//         $newUser = Pluf::factory('User_Account', $user->id);
//         $this->assertFalse($newUser->checkPassword('test'));
//         $this->assertTrue($newUser->checkPassword($newPassword));
//     }
// }


