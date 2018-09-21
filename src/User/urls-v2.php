<?php
/*
 * This file is part of Pluf Framework, a simple PHP Application Framework.
 * Copyright (C) 2010-2020 Phoinex Scholars Co. http://dpq.co.ir
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
return array(
    array( // Login
        'regex' => '#^/login$#',
        'model' => 'User_Views_Authentication',
        'method' => 'login',
        'http-method' => 'POST'
    ),
    array( // Login
        'regex' => '#^/tokens$#',
        'model' => 'User_Views_Authentication',
        'method' => 'login',
        'http-method' => 'PUT'
    ),
    array( // Logout
        'regex' => '#^/logout$#',
        'model' => 'User_Views_Authentication',
        'method' => 'logout',
        'http-method' => array(
            'POST',
            'GET'
        )
    ),
    array( // Logout from current session
        'regex' => '#^/tokens/current$#',
        'model' => 'User_Views_Authentication',
        'method' => 'logout',
        'http-method' => 'DELETE'
    ),

    // ************************************************** Current Account (current user info)

    array( // Read
        'regex' => '#^/accounts/current$#',
        'model' => 'User_Views',
        'method' => 'getAccount',
        'http-method' => 'GET'
    ),
    // array( // Update
    // 'regex' => '#^/accounts/current$#',
    // 'model' => 'User_Views',
    // 'method' => 'updateAccount',
    // 'precond' => array(
    // 'User_Precondition::loginRequired'
    // ),
    // 'http-method' => 'POST'
    // ),
    array( // Delete
        'regex' => '#^/accounts/current$#',
        'model' => 'User_Views',
        'method' => 'deleteAccount',
        'precond' => array(
            'User_Precondition::loginRequired'
        ),
        'http-method' => 'DELETE'
    ),

    // ************************************************** Account (user info)

    array( // Create
        'regex' => '#^/accounts$#',
        'model' => 'User_Views_Account',
        'method' => 'create',
        'http-method' => array(
            'PUT',
            'POST'
        )
    ),
    array( // Read (list)
        'regex' => '#^/accounts$#',
        'model' => 'Pluf_Views',
        'method' => 'findObject',
        'http-method' => 'GET',
        'params' => array(
            'model' => 'User_Account',
            'sql' => 'is_deleted=0'
        )
    ),
    array( // Read
        'regex' => '#^/accounts/(?P<modelId>\d+)$#',
        'model' => 'Pluf_Views',
        'method' => 'getObject',
        'http-method' => 'GET',
        'params' => array(
            'model' => 'User_Account',
            'sql' => 'is_deleted=false'
        )
    ),
    array( // Update
        'regex' => '#^/accounts/(?P<userId>\d+)$#',
        'model' => 'User_Views_Account',
        'method' => 'update',
        'precond' => array(
            'User_Precondition::ownerRequired'
        ),
        'http-method' => 'POST'
    ),
    array( // Delete
        'regex' => '#^/accounts/(?P<userId>\d+)$#',
        'model' => 'User_Views_Account',
        'method' => 'delete',
        'precond' => array(
            'User_Precondition::ownerRequired'
        ),
        'http-method' => 'DELETE'
    ),

    // ************************************************** Groups

    array( // Create
        'regex' => '#^/accounts/(?P<userId>\d+)/groups$#',
        'model' => 'User_Views_Group',
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
        'regex' => '#^/accounts/(?P<userId>\d+)/groups$#',
        'model' => 'User_Views_Group',
        'method' => 'find',
        'http-method' => 'GET'
    ),
    array( // Read
        'regex' => '#^/accounts/(?P<userId>\d+)/groups/(?P<groupId>\d+)$#',
        'model' => 'User_Views_Group',
        'method' => 'get',
        'http-method' => 'GET'
    ),

    // array( // Update
    // 'regex' => '#^/accounts/(?P<userId>\d+)/groups/(?P<groupId>\d+)$#',
    // 'model' => 'User_Views_Group',
    // 'method' => 'update',
    // 'http-method' => 'POST',
    // 'precond' => array(
    // 'User_Precondition::ownerRequired'
    // )
    // ),
    array( // Delete
        'regex' => '#^/accounts/(?P<userId>\d+)/groups/(?P<groupId>\d+)$#',
        'model' => 'User_Views_Group',
        'method' => 'delete',
        'http-method' => 'DELETE',
        'precond' => array(
            'User_Precondition::ownerRequired'
        )
    ),

    // ************************************************** Roles

    array( // Create
        'regex' => '#^/accounts/(?P<userId>\d+)/roles$#',
        'model' => 'User_Views_Permission',
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
        'regex' => '#^/accounts/(?P<userId>\d+)/roles$#',
        'model' => 'User_Views_Permission',
        'method' => 'find',
        'http-method' => 'GET'
    ),
    array( // Read
        'regex' => '#^/accounts/(?P<userId>\d+)/roles/(?P<roleId>\d+)$#',
        'model' => 'User_Views_Permission',
        'method' => 'get',
        'http-method' => 'GET'
    ),
    array( // Delete
        'regex' => '#^/accounts/(?P<userId>\d+)/roles/(?P<roleId>\d+)$#',
        'model' => 'User_Views_Permission',
        'method' => 'delete',
        'http-method' => 'DELETE',
        'precond' => array(
            'User_Precondition::ownerRequired'
        )
    ),

    // ************************************************** Profiles

    array( // Create / Update
        'regex' => '#^/accounts/(?P<userId>\d+)/profiles$#',
        'model' => 'User_Views_Profile',
        'method' => 'update',
        'http-method' => array(
            'PUT',
            'POST'
        ),
        'precond' => array(
            'User_Precondition::loginRequired'
        )
    ),
    array( // Update
        'regex' => '#^/accounts/(?P<userId>\d+)/profiles/(?P<profileId>\d+)$#',
        'model' => 'User_Views_Profile',
        'method' => 'update',
        'http-method' => 'POST',
        'precond' => array(
            'User_Precondition::loginRequired'
        )
    ),
    array( // Read (list)
        'regex' => '#^/accounts/(?P<parentId>\d+)/profiles$#',
        'model' => 'Pluf_Views',
        'method' => 'findManyToOne',
        'http-method' => 'GET',
        'precond' => array(
            'User_Precondition::loginRequired'
        ),
        'params' => array(
            'parent' => 'User_Account',
            'parentKey' => 'account_id',
            'model' => 'User_Profile'
        )
    ),
    array( // Read
        'regex' => '#^/accounts/(?P<parentId>\d+)/profiles/(?P<modelId>\d+)$#',
        'model' => 'Pluf_Views',
        'method' => 'getManyToOne',
        'http-method' => 'GET',
        'precond' => array(
            'User_Precondition::loginRequired'
        ),
        'params' => array(
            'parent' => 'User_Account',
            'parentKey' => 'account_id',
            'model' => 'User_Profile'
        )
    ),
    array( // Delete
        'regex' => '#^/accounts/(?P<userId>\d+)/profiles/(?P<profileId>\d+)$#',
        'model' => 'User_Views_Profile',
        'method' => 'delete',
        'http-method' => 'DELETE',
        'precond' => array(
            'User_Precondition::loginRequired'
        )
    ),

    // ************************************************** Avatar (Current user)
    array(
        'regex' => '#^/accounts/current/avatar$#',
        'model' => 'User_Views_Avatar',
        'method' => 'get',
        'http-method' => 'GET',
        'precond' => array(),
        // Cache param
        'cacheable' => true,
        'revalidate' => true,
        'intermediate_cache' => true
    ),
    array(
        'regex' => '#^/accounts/current/avatar$#',
        'model' => 'User_Views_Avatar',
        'method' => 'update',
        'http-method' => 'POST',
        'precond' => array(
            'User_Precondition::loginRequired'
        )
    ),
    array(
        'regex' => '#^/accounts/current/avatar$#',
        'model' => 'User_Views_Avatar',
        'method' => 'delete',
        'http-method' => 'DELETE',
        'precond' => array(
            'User_Precondition::loginRequired'
        )
    ),
    // ************************************************** Avatar (Specified User)
    array( // Read
        'regex' => '#^/accounts/(?P<userId>\d+)/avatar$#',
        'model' => 'User_Views_Avatar',
        'method' => 'get',
        'http-method' => 'GET',
        'precond' => array(),
        // Cache param
        'cacheable' => true,
        'revalidate' => true,
        'intermediate_cache' => true
    ),
    array( // Create / Update
        'regex' => '#^/accounts/(?P<userId>\d+)/avatar$#',
        'model' => 'User_Views_Avatar',
        'method' => 'update',
        'http-method' => array(
            'POST',
            'PUT'
        ),
        'precond' => array(
            'User_Precondition::loginRequired'
        )
    ),
    array( // Delete
        'regex' => '#^/accounts/(?P<userId>\d+)/avatar$#',
        'model' => 'User_Views_Avatar',
        'method' => 'delete',
        'http-method' => 'DELETE',
        'precond' => array(
            'User_Precondition::loginRequired'
        )
    ),

    // ************************************************** Credential (password)

    array( // Create/Update
        'regex' => '#^/credentials$#',
        'model' => 'User_Views_Password',
        'method' => 'password',
        'http-method' => array(
            'POST',
            'PUT'
        )
    )
);
