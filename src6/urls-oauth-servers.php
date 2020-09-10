<?php
return array(
    // ************************************************************* Schema
    array(
        'regex' => '#^/schema$#',
        'model' => 'Pluf_Views',
        'method' => 'getSchema',
        'http-method' => 'GET',
        'params' => array(
            'model' => 'User_OAuth2Server'
        )
    ),
    // ************************************************************* OAuth2Server
    array(
        'regex' => '#^$#',
        'model' => 'User_Views_OAuth2Server',
        'method' => 'find',
        'http-method' => 'GET'
    ),
    array(
        'regex' => '#^$#',
        'model' => 'User_Views_OAuth2Server',
        'method' => 'create',
        'http-method' => array(
            'POST'
        ),
        'precond' => array(
            'User_Precondition::ownerRequired'
        )
    ),
    array(
        'regex' => '#^/(?P<id>\d+)$#',
        'model' => 'User_Views_OAuth2Server',
        'method' => 'get',
        'http-method' => 'GET'
    ),
    array(
        'regex' => '#^/(?P<id>\d+)$#',
        'model' => 'User_Views_OAuth2Server',
        'method' => 'update',
        'http-method' => array(
            'POST'
        ),
        'precond' => array(
            'User_Precondition::ownerRequired'
        )
    ),
    array(
        'regex' => '#^/(?P<modelId>\d+)$#',
        'model' => 'Pluf_Views',
        'method' => 'deleteObject',
        'http-method' => 'DELETE',
        'params' => array(
            'model' => 'User_OAuth2Server',
            'permanently' => false
        ),
        'precond' => array(
            'User_Precondition::ownerRequired'
        )
    ),
);