<?php
return array(
    /*
     * ********************************************
     * Engines
     * ********************************************
     */
    array(
        'regex' => '#^$#',
        'model' => 'User_Views_OAuth2Engine',
        'method' => 'find',
        'http-method' => 'GET'
    ),
    array(
        'regex' => '#^/(?P<type>[^/]+)$#',
        'model' => 'User_Views_OAuth2Engine',
        'method' => 'get',
        'http-method' => 'GET'
    )
);