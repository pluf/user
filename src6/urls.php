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
    array( // schema
        'regex' => '#^/accounts/schema$#',
        'model' => 'User_Views',
        'method' => 'getSchema',
        'http-method' => 'GET',
        'params' => array(
            'model' => 'User_Account'
        )
    ),
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
    
    // ************************************************** Account Verification
    array( // create verification
        'regex' => '#^/accounts/(?P<userId>\d+)/verifications$#',
        'model' => 'User_Views_Account',
        'method' => 'verify',
        'http-method' => 'POST'
    ),
    array( // verify
        'regex' => '#^/accounts/(?P<userId>\d+)/verifications/(?P<code>[^/]+)$#',
        'model' => 'User_Views_Account',
        'method' => 'activate',
        'http-method' => 'POST'
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

    // ************************************************** Profiles (current user)

    array( // schema
        'regex' => '#^/profiles/schema$#',
        'model' => 'User_Views',
        'method' => 'getSchema',
        'http-method' => 'GET',
        'params' => array(
            'model' => 'User_Profile'
        )
    ),
    array( // Create / Update
        'regex' => '#^/profiles$#',
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
        'regex' => '#^/profiles/(?P<profileId>\d+)$#',
        'model' => 'User_Views_Profile',
        'method' => 'update',
        'http-method' => 'POST',
        'precond' => array(
            'User_Precondition::loginRequired'
        )
    ),
    array( // Read (list)
        'regex' => '#^/profiles$#',
        'model' => 'User_Views_Profile',
        'method' => 'find',
        'http-method' => 'GET'
    ),
    array( // Read
        'regex' => '#^/profiles/(?P<modelId>\d+)$#',
        'model' => 'Pluf_Views',
        'method' => 'getManyToOne',
        'http-method' => 'GET',
        'params' => array(
            'parent' => 'User_Account',
            'parentKey' => 'account_id',
            'model' => 'User_Profile'
        )
    ),
    array( // Delete
        'regex' => '#^/profiles/(?P<profileId>\d+)$#',
        'model' => 'User_Views_Profile',
        'method' => 'delete',
        'http-method' => 'DELETE',
        'precond' => array(
            'User_Precondition::loginRequired'
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
    ),
    
    // ************************************************** Emails
    
    array( // schema
        'regex' => '#^/emails/schema$#',
        'model' => 'User_Views',
        'method' => 'getSchema',
        'http-method' => 'GET',
        'params' => array(
            'model' => 'User_Email'
        )
    ),
    array( // Create
        'regex' => '#^/accounts/(?P<accountId>\d+)/emails$#',
        'model' => 'User_Views_Email',
        'method' => 'create',
        'http-method' => 'POST',
        'precond' => array(
            'User_Precondition::loginRequired'
        )
    ),
    array( // Read (list)
        'regex' => '#^/accounts/(?P<accountId>\d+)/emails$#',
        'model' => 'User_Views_Email',
        'method' => 'find',
        'http-method' => 'GET',
        'precond' => array(
            'User_Precondition::loginRequired'
        )
    ),
    array( // Read
        'regex' => '#^/accounts/(?P<accountId>\d+)/emails/(?P<emailId>\d+)$#',
        'model' => 'User_Views_Email',
        'method' => 'get',
        'http-method' => 'GET',
        'precond' => array(
            'User_Precondition::loginRequired'
        )
    ),
    array( // Update
        'regex' => '#^/accounts/(?P<accountId>\d+)/emails/(?P<emailId>\d+)$#',
        'model' => 'User_Views_Email',
        'method' => 'update',
        'http-method' => 'POST',
        'precond' => array(
            'User_Precondition::loginRequired'
        )
    ),
    array( // Delete
        'regex' => '#^/accounts/(?P<accountId>\d+)/emails/(?P<emailId>\d+)$#',
        'model' => 'User_Views_Email',
        'method' => 'delete',
        'http-method' => 'DELETE',
        'precond' => array(
            'User_Precondition::loginRequired'
        )
    ),
    // ************************************************** Email Verification
    array( // create verification
        'regex' => '#^/accounts/(?P<accountId>\d+)/emails/(?P<emailId>\d+)/verifications$#',
        'model' => 'User_Views_Email',
        'method' => 'verify',
        'http-method' => 'POST'
    ),
    array( // verify
        'regex' => '#^/accounts/(?P<accountId>\d+)/emails/(?P<emailId>\d+)/verifications/(?P<code>[^/]+)$#',
        'model' => 'User_Views_Email',
        'method' => 'activate',
        'http-method' => 'POST'
    ),
    
    // ************************************************** Phones
    array( // schema
        'regex' => '#^/phones/schema$#',
        'model' => 'User_Views',
        'method' => 'getSchema',
        'http-method' => 'GET',
        'params' => array(
            'model' => 'User_Phone'
        )
    ),
    array( // Create
        'regex' => '#^/accounts/(?P<accountId>\d+)/phones$#',
        'model' => 'User_Views_Phone',
        'method' => 'create',
        'http-method' => 'POST',
        'precond' => array(
            'User_Precondition::loginRequired'
        )
    ),
    array( // Read (list)
        'regex' => '#^/accounts/(?P<accountId>\d+)/phones$#',
        'model' => 'User_Views_Phone',
        'method' => 'find',
        'http-method' => 'GET',
        'precond' => array(
            'User_Precondition::loginRequired'
        )
    ),
    array( // Read
        'regex' => '#^/accounts/(?P<accountId>\d+)/phones/(?P<phoneId>\d+)$#',
        'model' => 'User_Views_Phone',
        'method' => 'get',
        'http-method' => 'GET',
        'precond' => array(
            'User_Precondition::loginRequired'
        )
    ),
    array( // Update
        'regex' => '#^/accounts/(?P<accountId>\d+)/phones/(?P<phoneId>\d+)$#',
        'model' => 'User_Views_Phone',
        'method' => 'update',
        'http-method' => 'POST',
        'precond' => array(
            'User_Precondition::loginRequired'
        )
    ),
    array( // Delete
        'regex' => '#^/accounts/(?P<accountId>\d+)/phones/(?P<phoneId>\d+)$#',
        'model' => 'User_Views_Phone',
        'method' => 'delete',
        'http-method' => 'DELETE',
        'precond' => array(
            'User_Precondition::loginRequired'
        )
    ),
    // ************************************************** Phone Verification
    array( // create verification
        'regex' => '#^/accounts/(?P<accountId>\d+)/phones/(?P<phoneId>\d+)/verifications$#',
        'model' => 'User_Views_Phone',
        'method' => 'verify',
        'http-method' => 'POST'
    ),
    array( // verify
        'regex' => '#^/accounts/(?P<accountId>\d+)/phones/(?P<phoneId>\d+)/verifications/(?P<code>[^/]+)$#',
        'model' => 'User_Views_Phone',
        'method' => 'activate',
        'http-method' => 'POST'
    ),
    
    // ************************************************** Addresses
    array( // schema
        'regex' => '#^/addresses/schema$#',
        'model' => 'User_Views',
        'method' => 'getSchema',
        'http-method' => 'GET',
        'params' => array(
            'model' => 'User_Address'
        )
    ),
    array( // Create
        'regex' => '#^/accounts/(?P<accountId>\d+)/addresses$#',
        'model' => 'User_Views_Address',
        'method' => 'create',
        'http-method' => 'POST',
        'precond' => array(
            'User_Precondition::loginRequired'
        )
    ),
    array( // Read (list)
        'regex' => '#^/accounts/(?P<accountId>\d+)/addresses$#',
        'model' => 'User_Views_Address',
        'method' => 'find',
        'http-method' => 'GET',
        'precond' => array(
            'User_Precondition::loginRequired'
        )
    ),
    array( // Read
        'regex' => '#^/accounts/(?P<accountId>\d+)/addresses/(?P<addressId>\d+)$#',
        'model' => 'User_Views_Address',
        'method' => 'get',
        'http-method' => 'GET',
        'precond' => array(
            'User_Precondition::loginRequired'
        )
    ),
    array( // Update
        'regex' => '#^/accounts/(?P<accountId>\d+)/addresses/(?P<addressId>\d+)$#',
        'model' => 'User_Views_Address',
        'method' => 'update',
        'http-method' => 'POST',
        'precond' => array(
            'User_Precondition::loginRequired'
        )
    ),
    array( // Delete
        'regex' => '#^/accounts/(?P<accountId>\d+)/addresses/(?P<addressId>\d+)$#',
        'model' => 'User_Views_Address',
        'method' => 'delete',
        'http-method' => 'DELETE',
        'precond' => array(
            'User_Precondition::loginRequired'
        )
    ),
    // ************************************************** OAuth2Connections
    array( // schema
        'regex' => '#^/oauth2connections/schema$#',
        'model' => 'User_Views',
        'method' => 'getSchema',
        'http-method' => 'GET',
        'params' => array(
            'model' => 'User_OAuth2Connection'
        )
    ),
    array( // Create
        'regex' => '#^/accounts/(?P<accountId>\d+)/oauth2connections$#',
        'model' => 'User_Views_OAuth2Connection',
        'method' => 'create',
        'http-method' => 'POST',
        'precond' => array(
            'User_Precondition::loginRequired'
        )
    ),
    array( // Read (list)
        'regex' => '#^/accounts/(?P<accountId>\d+)/oauth2connections$#',
        'model' => 'User_Views_OAuth2Connection',
        'method' => 'find',
        'http-method' => 'GET',
        'precond' => array(
            'User_Precondition::loginRequired'
        )
    ),
    array( // Read
        'regex' => '#^/accounts/(?P<accountId>\d+)/oauth2connections/(?P<oauth2connId>\d+)$#',
        'model' => 'User_Views_OAuth2Connection',
        'method' => 'get',
        'http-method' => 'GET',
        'precond' => array(
            'User_Precondition::loginRequired'
        )
    ),
    array( // Delete
        'regex' => '#^/accounts/(?P<accountId>\d+)/oauth2connections/(?P<oauth2connId>\d+)$#',
        'model' => 'User_Views_OAuth2Connection',
        'method' => 'delete',
        'http-method' => 'DELETE',
        'precond' => array(
            'User_Precondition::loginRequired'
        )
    ),
    
    /******************************************************************************
     * Group
     ******************************************************************************/
    array(
        'regex' => '#^/groups#',
        'sub' => include 'urls-groups.php'
    ),
    /******************************************************************************
     * Roles
     ******************************************************************************/
    array(
        'regex' => '#^/roles#',
        'sub' => include 'urls-roles.php'
    ),
    
    /******************************************************************************
     * Messages
     ******************************************************************************/
    array(
        'regex' => '#^/messages#',
        'sub' => include 'urls-messages.php'
    ),
    /******************************************************************************
     * Authentication engines
     ******************************************************************************/
    array(
        'regex' => '#^/oauth2engines#',
        'sub' => include 'urls-oauth-engines.php'
    ),
    /******************************************************************************
     * Authentication backends
     ******************************************************************************/
    array(
        'regex' => '#^/oauth2servers#',
        'sub' => include 'urls-oauth-servers.php'
    ),
);

