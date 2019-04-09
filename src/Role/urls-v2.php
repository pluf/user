<?php
return array(
    /*
     * Roles
     */
    array(
        'regex' => '#^/schema$#',
        'model' => 'Pluf_Views',
        'method' => 'getSchema',
        'http-method' => 'GET',
        'params' => array(
            'model' => 'User_Role'
        )
    ),

    array( // Create
        'regex' => '#^$#',
        'model' => 'Role_Views',
        'method' => 'create',
        'http-method' => array(
            'PUT',
            'POST'
        ),
        'precond' => array(
            'User_Precondition::ownerRequired'
        )
    ),
    array( // Read (list)
        'regex' => '#^$#',
        'model' => 'Pluf_Views',
        'method' => 'findObject',
        'http-method' => 'GET',
        'params' => array(
            'model' => 'User_Role'
        )
    ),
    array( // Read
        'regex' => '#^/(?P<id>\d+)$#',
        'model' => 'Role_Views',
        'method' => 'get',
        'http-method' => 'GET'
    ),
    array( // Update
        'regex' => '#^/(?P<id>\d+)$#',
        'model' => 'Role_Views',
        'method' => 'update',
        'http-method' => 'POST',
        'precond' => array(
            'User_Precondition::ownerRequired'
        )
    ),
    array( // Delete
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
    array( // Create
        'regex' => '#^/(?P<role_id>\d+)/accounts$#',
        'model' => 'Role_Views_User',
        'method' => 'add',
        'http-method' => array(
            'PUT',
            'POST'
        ),
        'precond' => array(
            'User_Precondition::ownerRequired'
        )
    ),
    array( // Create
        'regex' => '#^/(?P<role_id>\d+)/accounts/(?P<user_id>\d+)$#',
        'model' => 'Role_Views_User',
        'method' => 'add',
        'http-method' => array(
            'PUT',
            'POST'
        ),
        'precond' => array(
            'User_Precondition::ownerRequired'
        )
    ),
    array( // Read (list)
        'regex' => '#^/(?P<role_id>\d+)/accounts$#',
        'model' => 'Role_Views_User',
        'method' => 'find',
        'http-method' => 'GET',
        'precond' => array()
    ),
    array( // Read
        'regex' => '#^/(?P<role_id>\d+)/accounts/(?P<user_id>\d+)$#',
        'model' => 'Role_Views_User',
        'method' => 'get',
        'http-method' => 'GET',
        'precond' => array()
    ),
    array( // Delete
        'regex' => '#^/(?P<role_id>\d+)/accounts/(?P<user_id>\d+)$#',
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
    array( // Create
        'regex' => '#^/(?P<role_id>\d+)/groups$#',
        'model' => 'Role_Views_Group',
        'method' => 'add',
        'http-method' => array(
            'PUT',
            'POST'
        ),
        'precond' => array(
            'User_Precondition::ownerRequired'
        )
    ),
    array( // Create
        'regex' => '#^/(?P<role_id>\d+)/groups/(?P<group_id>\d+)$#',
        'model' => 'Role_Views_Group',
        'method' => 'add',
        'http-method' => array(
            'PUT',
            'POST'
        ),
        'precond' => array(
            'User_Precondition::ownerRequired'
        )
    ),
    array( // Read (list)
        'regex' => '#^/(?P<role_id>\d+)/groups$#',
        'model' => 'Role_Views_Group',
        'method' => 'find',
        'http-method' => 'GET',
        'precond' => array()
    ),
    array( // Read
        'regex' => '#^/(?P<role_id>\d+)/groups/(?P<group_id>\d+)$#',
        'model' => 'Role_Views_Group',
        'method' => 'get',
        'http-method' => 'GET',
        'precond' => array()
    ),
    array( // Delete
        'regex' => '#^/(?P<role_id>\d+)/groups/(?P<group_id>\d+)$#',
        'model' => 'Role_Views_Group',
        'method' => 'delete',
        'http-method' => 'DELETE',
        'precond' => array(
            'User_Precondition::ownerRequired'
        )
    )
);
