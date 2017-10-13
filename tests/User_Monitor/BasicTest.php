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
class User_Monitor_BasicsTest extends TestCase
{

    /**
     * @beforeClass
     */
    public static function createDataBase()
    {
        Pluf::start(array(
            'general_domain' => 'localhost',
            'general_admin_email' => array(
                'root@localhost'
            ),
            'general_from_email' => 'test@localhost',
            'middleware_classes' => array(
                'Pluf_Middleware_Session'
            ),
            'debug' => true,
            'test_unit' => true,
            'languages' => array(
                'fa',
                'en'
            ),
            'tmp_folder' => dirname(__FILE__) . '/../tmp',
            'template_folders' => array(
                dirname(__FILE__) . '/../templates'
            ),
            'upload_path' => dirname(__FILE__) . '/../tmp',
            'template_tags' => array(),
            'time_zone' => 'Asia/Tehran',
            'encoding' => 'UTF-8',
            
            'secret_key' => '5a8d7e0f2aad8bdab8f6eef725412850',
            'user_signup_active' => true,
            'user_avatra_max_size' => 2097152,
            'auth_backends' => array(
                'Pluf_Auth_ModelBackend'
            ),
            'pluf_use_rowpermission' => true,
            'db_engine' => 'MySQL',
            'db_version' => '5.5.33',
            'db_login' => 'root',
            'db_password' => '',
            'db_server' => 'localhost',
            'db_database' => 'test',
            'db_table_prefix' => '_tm_',
            
            'mail_backend' => 'mail',
            'user_avatar_default' => dirname(__FILE__) . '/../conf/avatar.svg'
        ));
        $db = Pluf::db();
        $schema = Pluf::factory('Pluf_DB_Schema', $db);
        $models = array(
            'Pluf_Group',
            'Pluf_User',
            'Pluf_Permission',
            'Pluf_RowPermission',
            'Pluf_Session',
            'Pluf_Message',
            'Collection_Collection',
            'Collection_Document',
            'Collection_Attribute',
            'User_CProfile',
            'User_Avatar'
        );
        foreach ($models as $model) {
            $schema->model = Pluf::factory($model);
            $schema->dropTables();
            if (true !== ($res = $schema->createTables())) {
                throw new Exception($res);
            }
        }
        
        $user = new Pluf_User();
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
    }

    /**
     * @afterClass
     */
    public static function removeDatabses()
    {
        $db = Pluf::db();
        $schema = Pluf::factory('Pluf_DB_Schema', $db);
        $models = array(
            'Pluf_Group',
            'Pluf_User',
            'Pluf_Permission',
            'Pluf_RowPermission',
            'Pluf_Session',
            'Pluf_Message',
            'Collection_Collection',
            'Collection_Document',
            'Collection_Attribute',
            'User_CProfile',
            'User_Avatar'
        );
        foreach ($models as $model) {
            $schema->model = Pluf::factory($model);
            $schema->dropTables();
        }
    }

    /**
     * @test
     */
    public function currentUserRest()
    {
        
        $client = new Test_Client(array(
            array(
                'app' => 'User',
                'regex' => '#^/api/user#',
                'base' => '',
                'sub' => include 'User/urls.php'
            ),
            array(
                'regex' => '#^/monitor/(?P<property>.+)$#',
                'model' => 'User_Monitor',
                'method' => 'permisson',
                'http-method' => 'GET'
            )
        ));
        
        // Change detail
        $user = new Pluf_User();
        $user = $user->getUser('test');
        
        // Login
        $response = $client->post('/api/user/login', array(
            'login' => 'test',
            'password' => 'test'
        ));
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        
        // Login
        $response = $client->get('/monitor/owner');
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
    }

}