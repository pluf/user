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
    /*
     * Current User
     */
    array( // Get information of current user
        'regex' => '#^$#',
        'model' => 'User_Views',
        'method' => 'getAccount',
        'http-method' => 'GET'
    ),
    array( // Update information of current user
        'regex' => '#^$#',
        'model' => 'User_Views',
        'method' => 'updateAccount',
        'precond' => array(
            'Pluf_Precondition::loginRequired'
        ),
        'http-method' => 'POST'
    ),
    array( // Delete current user
        'regex' => '#^$#',
        'model' => 'User_Views',
        'method' => 'deleteAccount',
        'precond' => array(
            'Pluf_Precondition::loginRequired'
        ),
        'http-method' => 'DELETE'
    ),
    
    array( // Create new user
        'regex' => '#^/new$#',
        'model' => 'User_Views_User',
        'method' => 'create',
        'http-method' => 'POST'
    ),
    array( // Find users
        'regex' => '#^/find$#',
        'model' => 'User_Views_User',
        'method' => 'find',
        'http-method' => 'GET'
    ),
    array( // Login
        'regex' => '#^/login$#',
        'model' => 'User_Views_Authentication',
        'method' => 'login',
        'http-method' => 'POST'
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
    array( // Password
        'regex' => '#^/password$#',
        'model' => 'User_Views_Password',
        'method' => 'password',
        'http-method' => array(
            'POST'
        )
    ),
    
    //
    // User
    //
    array( // Get information of user (by id)
        'regex' => '#^/(?P<userId>\d+)$#',
        'model' => 'User_Views_User',
        'method' => 'get',
        'http-method' => 'GET'
    ),
    array( // Update information of user (by id)
        'regex' => '#^/(?P<userId>\d+)$#',
        'model' => 'User_Views_User',
        'method' => 'update',
        'precond' => array(
            'Pluf_Precondition::loginRequired'
        ),
        'http-method' => 'POST'
    ),
    array( // Delete user (by id)
        'regex' => '#^/(?P<userId>\d+)$#',
        'model' => 'User_Views_User',
        'method' => 'delete',
        'precond' => array(
            'Pluf_Precondition::loginRequired'
        ),
        'http-method' => 'DELETE'
    ),
    //
    // Profile
    //
    array( // Get profile of user (by id)
        'regex' => '#^/(?P<userId>\d+)/profile$#',
        'model' => 'User_Views_CProfile',
        'method' => 'get',
        'http-method' => 'GET'
    ),
    array( // Update profile of user (by id)
        'regex' => '#^/(?P<userId>\d+)/profile$#',
        'model' => 'User_Views_CProfile',
        'method' => 'update',
        'precond' => array(
            'Pluf_Precondition::loginRequired'
        ),
        'http-method' => 'POST'
    ),
//     // Profile (by Collection)
//     array( // Get profile of user (by id)
//         'regex' => '#^/(?P<userId>\d+)/cprofile$#',
//         'model' => 'User_Views_CProfile',
//         'method' => 'get',
//         'http-method' => 'GET'
//     ),
//     array( // Update profile of user (by id)
//         'regex' => '#^/(?P<userId>\d+)/cprofile$#',
//         'model' => 'User_Views_CProfile',
//         'method' => 'update',
//         'precond' => array(
//             'Pluf_Precondition::loginRequired'
//         ),
//         'http-method' => 'POST'
//     ),
    //
    // Avatar (Current user)
    //
    array(
        'regex' => '#^/avatar$#',
        'model' => 'User_Views',
        'method' => 'getAvatar',
        'http-method' => 'GET',
        'precond' => array(),
        // Cache apram
        'cacheable' => true,
        'revalidate' => true,
        'intermediate_cache' => true
    ),
    array(
        'regex' => '#^/avatar$#',
        'model' => 'User_Views',
        'method' => 'updateAvatar',
        'http-method' => 'POST',
        'precond' => array(
            'Pluf_Precondition::loginRequired'
        )
    ),
    array(
        'regex' => '#^/avatar$#',
        'model' => 'User_Views',
        'method' => 'deleteAvatar',
        'http-method' => 'DELETE',
        'precond' => array(
            'Pluf_Precondition::loginRequired'
        )
    ),
    /*
     * Avatar (Specified User)
     */
    array(
        'regex' => '#^/(?P<userId>\d+)/avatar$#',
        'model' => 'User_Views_Avatar',
        'method' => 'get',
        'http-method' => 'GET',
        'precond' => array(),
        // Cache apram
        'cacheable' => true,
        'revalidate' => true,
        'intermediate_cache' => true
    ),
    array(
        'regex' => '#^/(?P<userId>\d+)/avatar$#',
        'model' => 'User_Views_Avatar',
        'method' => 'update',
        'http-method' => 'POST',
        'precond' => array(
            'Pluf_Precondition::loginRequired'
        )
    ),
    array(
        'regex' => '#^/(?P<userId>\d+)/avatar$#',
        'model' => 'User_Views_Avatar',
        'method' => 'delete',
        'http-method' => 'DELETE',
        'precond' => array(
            'Pluf_Precondition::loginRequired'
        )
    ),
    // XXX: Hadi, 1395-07-08: I believe that following RESTs are redundant.
    /*
     * Groups
     */
    array(
        'regex' => '#^/(?P<userId>\d+)/group/new$#',
        'model' => 'User_Views_Group',
        'method' => 'create',
        'http-method' => 'POST',
        'precond' => array(
            'Pluf_Precondition::adminRequired'
        )
    ),
    array(
        'regex' => '#^/(?P<userId>\d+)/group/find$#',
        'model' => 'User_Views_Group',
        'method' => 'find',
        'http-method' => 'GET'
    ),
    array(
        'regex' => '#^/(?P<userId>\d+)/group/(?P<groupId>\d+)$#',
        'model' => 'User_Views_Group',
        'method' => 'get',
        'http-method' => 'GET'
    ),
    array(
        'regex' => '#^/(?P<userId>\d+)/group/(?P<groupId>\d+)$#',
        'model' => 'User_Views_Group',
        'method' => 'update',
        'http-method' => 'POST'
    ),
    array(
        'regex' => '#^/(?P<userId>\d+)/group/(?P<groupId>\d+)$#',
        'model' => 'User_Views_Group',
        'method' => 'delete',
        'http-method' => 'DELETE'
    ),
    /*
     * Role
     */
    array(
        'regex' => '#^/(?P<userId>\d+)/role/new$#',
        'model' => 'User_Views_Permission',
        'method' => 'create',
        'http-method' => 'POST',
        'precond' => array(
            'Pluf_Precondition::loginRequired'
        )
    ),
    array(
        'regex' => '#^/(?P<userId>\d+)/role/find$#',
        'model' => 'User_Views_Permission',
        'method' => 'find',
        'http-method' => 'GET'
    ),
    array(
        'regex' => '#^/(?P<userId>\d+)/role/(?P<roleId>\d+)$#',
        'model' => 'User_Views_Permission',
        'method' => 'get',
        'http-method' => 'GET'
    ),
    // array(
    // 'regex' => '#^/(?P<userId>\d+)/role/(?P<roleId>\d+)$#',
    // 'model' => 'User_Views_Permission',
    // 'method' => 'POST',
    // 'http-method' => 'update',
    // 'precond' => array(
    // 'Pluf_Precondition::ownerRequired'
    // )
    // ),
    array(
        'regex' => '#^/(?P<userId>\d+)/role/(?P<roleId>\d+)$#',
        'model' => 'User_Views_Permission',
        'method' => 'delete',
        'http-method' => 'DELETE',
        'precond' => array(
            'Pluf_Precondition::loginRequired'
        )
    ),
    //
    // Passowrd
    //
    array( // Update information of user (by id)
        'regex' => '#^/(?P<userId>\d+)/password$#',
        'model' => 'User_Views_Password',
        'method' => 'update',
        'precond' => array(
            'Pluf_Precondition::loginRequired'
        ),
        'http-method' => 'POST'
    )
);
