<?php
return array(
    /*
     * Roles
     */
    array(
        'regex' => '#^/new$#',
        'model' => 'Role_Views',
        'method' => 'create',
        'http-method' => 'POST',
        'precond' => array(
            'User_Precondition::ownerRequired'
        )
    ),
    array(
        'regex' => '#^/find$#',
        'model' => 'Role_Views',
        'method' => 'find',
        'http-method' => 'GET'
    ),
    array(
        'regex' => '#^/(?P<id>\d+)$#',
        'model' => 'Role_Views',
        'method' => 'get',
        'http-method' => 'GET'
    ),
    array(
        'regex' => '#^/(?P<id>\d+)$#',
        'model' => 'Role_Views',
        'method' => 'update',
        'http-method' => 'POST',
        'precond' => array(
            'User_Precondition::ownerRequired'
        )
    ),
    array(
        'regex' => '#^/(?P<id>\d+)$#',
        'model' => 'Role_Views',
        'method' => 'delete',
        'http-method' => 'DELETE',
        'precond' => array(
            'User_Precondition::ownerRequired'
        )
    ),
    /*
     * Users of role
     */
    array(
        'regex' => '#^/(?P<role_id>\d+)/user/((?P<user_id>\d+)|new)$#',
        'model' => 'Role_Views_User',
        'method' => 'add',
        'http-method' => 'POST',
        'precond' => array(
            'User_Precondition::ownerRequired'
        )
    ),
    array(
        'regex' => '#^/(?P<role_id>\d+)/user/find$#',
        'model' => 'Role_Views_User',
        'method' => 'find',
        'http-method' => 'GET',
        'precond' => array()
    ),
    array(
        'regex' => '#^/(?P<role_id>\d+)/user/(?P<user_id>\d+)$#',
        'model' => 'Role_Views_User',
        'method' => 'get',
        'http-method' => 'GET',
        'precond' => array()
    ),
    array(
        'regex' => '#^/(?P<role_id>\d+)/user/(?P<user_id>\d+)$#',
        'model' => 'Role_Views_User',
        'method' => 'delete',
        'http-method' => 'DELETE',
        'precond' => array(
            'User_Precondition::ownerRequired'
        )
    ),
    /*
     * Groups of role
     */
    array(
        'regex' => '#^/(?P<role_id>\d+)/group/((?P<group_id>\d+)|new)$#',
        'model' => 'Role_Views_Group',
        'method' => 'add',
        'http-method' => 'POST',
        'precond' => array(
            'User_Precondition::ownerRequired'
        )
    ),
    array(
        'regex' => '#^/(?P<role_id>\d+)/group/find$#',
        'model' => 'Role_Views_Group',
        'method' => 'find',
        'http-method' => 'GET',
        'precond' => array()
    ),
    array(
        'regex' => '#^/(?P<role_id>\d+)/group/(?P<group_id>\d+)$#',
        'model' => 'Role_Views_Group',
        'method' => 'get',
        'http-method' => 'GET',
        'precond' => array()
    ),
    array(
        'regex' => '#^/(?P<role_id>\d+)/group/(?P<group_id>\d+)$#',
        'model' => 'Role_Views_Group',
        'method' => 'delete',
        'http-method' => 'DELETE',
        'precond' => array(
            'User_Precondition::ownerRequired'
        )
    )
);
