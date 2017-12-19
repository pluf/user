<?php 

return array(
    'general_domain' => 'localhost',
    'general_admin_email' => array(
        'root@localhost'
    ),
    'general_from_email' => 'test@localhost',
    'middleware_classes' => array(
        'Pluf_Middleware_Session',
        'User_Middleware_Session'
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
    
    'db_engine' => 'MySQL',
    'db_version' => '5.5.33',
    'db_login' => 'root',
    'db_password' => '',
    'db_server' => 'localhost',
    'db_database' => 'test',
    'db_table_prefix' => '_tm_',
    
    'mail_backend' => 'mail',
    'user_avatar_default' => dirname(__FILE__) . '/../conf/avatar.svg'
);