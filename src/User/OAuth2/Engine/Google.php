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
use League\OAuth2\Client\Provider\Google;

/**
 * The engine to work with google authentication system (based on OAuth2)
 *
 * @author maso<mostafa.barmshory@dpq.co.ir>
 * @author hadi<mohammad.hadi.mansouri@dpq.co.ir>
 */
class User_OAuth2_Engine_Google extends User_OAuth2_Engine
{

    // const AuthorizationUrl = 'https://accounts.google.com/o/oauth2/v2/auth';
    // const AccessTokenUrl = 'https://www.googleapis.com/oauth2/v4/token';
    // const ResourceOwnerDetailsUrl = 'https://openidconnect.googleapis.com/v1/userinfo';
    public function getTitle()
    {
        return 'Google';
    }

    public function getDescription()
    {
        return 'Google OAuth2 authentication system';
    }

    public function getSymbol()
    {
        return 'google';
    }

    public function getExtraParam()
    {
        return array();
    }

    /**
     * Returns an authentication provider
     * 
     * @param array $options
     * @throws Pluf_Exception_BadRequest
     * @return \League\OAuth2\Client\Provider\Google
     */
    public function getProvider($options)
    {
        $authServer = new User_OAuth2Server($options['server_id']);
        if (! $authServer) {
            throw new Pluf_Exception_BadRequest('Authentication server does not exist');
        }
        $redirectUri = isset($options['redirect_uri']) ? $options['redirect_uri'] : $authServer->getMeta(User_OAuth2_Engine::RedirectUri);
        $clientId = $authServer->client_id;
        $clientSecret = $authServer->client_secret;
        $provider = new Google([
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
            'redirectUri' => $redirectUri
        ]);
        return $provider;
    }

    /**
     * Check authorization of the given request
     *
     * @param Pluf_HTTP_Request $request
     */
    public function authenticate($request)
    {
        $params = $request->REQUEST;
        try {
            $ownerDetails = $this->getOwnerDetails($request);
            // Use these details to authorize the user
            $username = $ownerDetails->getEmail();
            $authServer = new User_OAuth2Server($params['server_id']);
            if(!$authServer){
                throw new \Pluf\Exception('Authentication server does not exist: ' . $params['server_id']);
            }
            $connection = $this->getConnection(array(
                'username' => $username,
                'server_id' => $authServer->id
            ));
            if (!$connection) {
                // Do registeration if it does not exist and settings is set to allow registeration
                if($this->allowSignup()){
                    // Use these details to create user account and profile
                    $data = array(
                        'login' => $ownerDetails->getEmail(),
                        'first_name' => $ownerDetails->getFirstName(),
                        'last_name' => $ownerDetails->getLastName(),
                        'language' => $ownerDetails->getLocale(),
                        'email' => $ownerDetails->getEmail(),
                        'is_active' => true
                    );
                    $user = $this->signup($data);
                    // Create server-connection
                    $this->createConnection($authServer, $user, $data['login']);
                    return $user;
                }
                return false;
            }
            return $connection->get_account();
        } catch (Exception $e) {
            // Failed to get user details
            throw new \Pluf\Exception('Something went wrong: ' . $e->getMessage());
        }
    }

    public function allowSignup(){
        $allow = class_exists('Tenant_Service') ? //
            Tenant_Service::setting('account.allow.signup', false) : //
            Pluf::f('account.signup.allow', false);
        return $allow;
    }
    
    private function signup($data)
    {
        $usr = User_Account::getUser($data['login']);
        if ($usr) {
            throw new \Pluf\Exception('Username is existed already.', 400);
        }
        $form = new User_Form_Account($data, array());
        $cuser = $form->save();
        // Create profile
        $profile = new User_Profile();
        $form = Pluf_ModelUtils::getCreateForm($profile, $data);
        $profile = $form->save(false);
        $profile->account_id = $cuser;
        $profile->create();
        $cuser->profile = $profile;
        // Create email
        if (array_key_exists('email', $data)) {
            $email = new User_Email();
            $email->_a['cols']['email']['editable'] = true;
            $form = Pluf_ModelUtils::getCreateForm($email, $data);
            $email = $form->save(false);
            $email->account_id = $cuser;
            $email->create();
            $cuser->email = $email;
        }
        return $cuser;
    }
    
    /**
     * Creates and returns a OAuth2Connection to connect an user-account to a oauth2 account.
     * 
     * @param User_OAuth2Server $authServer
     * @param User_Account $user
     * @param string $username the username (user id) on the authentication server
     * @return User_OAuth2Connection
     */
    private function createConnection($authServer, $user, $username){
        $connection = new User_OAuth2Connection();
        $connection->username = $username;
        $connection->account_id = $user;
        $connection->server_id = $authServer;
        $connection->create();
        return $connection;
    }
    
    /**
     * Find and returns the oauth2-connection for given information in the $data.
     * $data must contains the server_id and at least one of the username and account_id. 
     * 
     * @param array $data
     * @return User_OAuth2Connection
     */
    private function getConnection($data){
        $sql = new Pluf_SQL('server_id=%d', array($data['server_id']));
        if(isset($data['username'])){
            $sql->SAnd(new Pluf_SQL('username=%s', array($data['username'])));
        }
        if(isset($data['account_id'])){
            $sql->SAnd(new Pluf_SQL('account_id=%d', array($data['account_id'])));
        }
        $connection = Pluf::factory('User_OAuth2Connection')->getOne($sql->gen());
        return $connection;
    }
    
    /**
     * Registers a user account
     *
     * @param Pluf_HTTP_Request $request
     * @return User_OAuth2Connection
     */
    public function connect($request)
    {
        $user = $request->user;
        $authServer = new User_OAuth2Server($request->REQUEST['server_id']);
        if (! $authServer) {
            throw new Pluf_Exception_BadRequest('Authentication server does not exist');
        }
        try {
            $ownerDetails = $this->getOwnerDetails($request);
            // Use these details to create user account and profile
            $username = $ownerDetails->getEmail();
            $connection = $this->getConnection(array(
                'username' => $username,
                'server_id' => $authServer->id
            ));
            if($connection){
                throw new Pluf_Exception_BadRequest('The user is connected to the given authentication server already');
            }
            // Create server-connection
            $connection = $this->createConnection($authServer, $user, $username);
            return $connection;
        } catch (Exception $e) {
            // Failed to get user details
            throw new Pluf_Exception('Something went wrong: ' . $e->getMessage());
        }
    }
}