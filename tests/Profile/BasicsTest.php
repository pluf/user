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
// use Pluf\Test\TestCase;
// use PHPUnit\Framework\IncompleteTestError;
// require_once 'Pluf.php';

// /**
//  * Basic test of system API
//  *
//  * @backupGlobals disabled
//  * @backupStaticAttributes disabled
//  */
// class User_Profile_API_BasicsTest extends TestCase
// {

//     /**
//      *
//      * @beforeClass
//      */
//     public static function createDataBase()
//     {
//         Pluf::start(__DIR__ . '/../conf/config.php');
//         $db = Pluf::db();
//         $schema = Pluf::factory('Pluf_DB_Schema', $db);
//         $models = array(
//             'Collection_Collection',
//             'Collection_Document',
//             'Collection_Attribute',
//             'User_Group',
//             'User_Account',
//             'User_Credential',
//             'User_Role',
//             'User_Profile'
//         );
//         foreach ($models as $model) {
//             $schema->model = Pluf::factory($model);
//             $schema->dropTables();
//             if (true !== ($res = $schema->createTables())) {
//                 throw new Exception($res);
//             }
//         }

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
//     }

//     /**
//      *
//      * @afterClass
//      */
//     public static function removeDatabses()
//     {
//         $db = Pluf::db();
//         $schema = Pluf::factory('Pluf_DB_Schema', $db);
//         $models = array(
//             'Collection_Collection',
//             'Collection_Document',
//             'Collection_Attribute',
//             'User_Group',
//             'User_Account',
//             'User_Credential',
//             'User_Role',
//             'User_Profile'
//         );
//         foreach ($models as $model) {
//             $schema->model = Pluf::factory($model);
//             $schema->dropTables();
//         }
//     }

//     /**
//      *
//      * @test
//      */
//     public function testCreateNewProfile()
//     {

//         // Get a suer
//         $user = new User_Account();
//         $user = $user->getUser('test');

//         $profile = User_Views_Profile::get_profile_document($user->id);

//         $this->assertNotNull($profile);
//     }
// }


