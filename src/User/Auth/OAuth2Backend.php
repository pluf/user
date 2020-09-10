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

/**
 * Backend to authenticate with the Google
 */
class User_Auth_OAuth2Backend
{

    /**
     * Given a user id, retrieve it.
     *
     * In the case of the OAuth2 backend, the $username is the user id on the OAuth2 server.
     *
     * 
     * @param string $username
     * @param integer $server_id
     * @return User_Account
     */
    public static function getUser($username, $server_id)
    {
        $sql = new Pluf_SQL('username=%s AND server_id=%d', array(
            $username, $server_id
        ));
        $connection = Pluf::factory('User_OAuth2Connection')->getOne($sql->gen());
        return $connection->get_account();
    }

    /**
     * Returns credential data of given user if exist
     *
     * In the case of the OAuth2 backend, the $username is the user id on the server and
     * the credential is the oauth2-connection 
     *
     * @return User_OAuth2Connection
     */
    public static function getCredential($username, $server_id)
    {
        self::getConnection($username, $server_id);
    }
    
    public static function getConnection($username, $server_id){
        $sql = new Pluf_SQL('username=%s AND server_id=%d', array(
            $username, $server_id
        ));
        $connection = Pluf::factory('User_OAuth2Connection')->getOne($sql->gen());
        return $connection;
    }
    
    /**
     * Given an array with the authentication data, auth the user and return it.
     */
    public static function authenticate($request)
    {
        $serverId = $request->REQUEST['server_id'];
        $authServer = new User_OAuth2Server($serverId);
        return $authServer->authenticate($request);
    }
    
}

