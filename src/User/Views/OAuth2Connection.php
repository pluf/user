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

/**
 * Manage oauth2-connection of the account
 */
class User_Views_OAuth2Connection extends Pluf_Views
{

    /**
     * Creates new oauth2-connnection for the account
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     * @return User_OAuth2Connection created connection
     */
    public static function create($request, $match)
    {
        // Create connection
        $authServer = isset($request->REQUEST['server_id']) ? new User_OAuth2Server($request->REQUEST['server_id']) : null;
        if(!$authServer){
            throw new \Pluf\Exception('Authentication server does not exist!');
        }
        $engine = $authServer->get_engine();
        return $engine->connect($request);
    }

    /**
     * Returns specified authetincatin settings of an account
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     * @return User_OAuth2Connection
     */
    public function get($request, $match)
    {
        $usr = self::checkAccess($request, $match);
        $match['parentId'] = $usr->id;
        $match['modelId'] = $match['oauth2connId'];
        $p = array(
            'parent' => 'User_Account',
            'parentKey' => 'account_id',
            'model' => 'User_OAuth2Connection'
        );
        return $this->getManyToOne($request, $match, $p);
    }
    
    /**
     * Lists authentication connections of an account
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     * @return Pluf_Paginator
     */
    public function find($request, $match)
    {
        $usr = self::checkAccess($request, $match);
        $match['parentId'] = $usr->id;
        $p = array(
            'parent' => 'User_Account',
            'parentKey' => 'account_id',
            'model' => 'User_OAuth2Connection'
        );
        return $this->findManyToOne($request, $match, $p);
    }
    
    /**
     * Delete specified oauth2 connection of an account
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     * @return User_OAuth2Connection
     */
    public function delete($request, $match)
    {
        $usr = self::checkAccess($request, $match);
        $match['parentId'] = $usr->id;
        $match['modelId'] = $match['oauth2connId'];
        $p = array(
            'parent' => 'User_Account',
            'parentKey' => 'account_id',
            'model' => 'User_OAuth2Connection'
        );
        return $this->deleteManyToOne($request, $match, $p);
    }
    
    /**
     * Checks permission to change authentication settings and returns account which its authentication settings will be changed.
     *
     * Checks the permission of the requester user to change authentication connections of defined account in the request.
     * The requester user have permission to change authentication connections of an account if and only if he is tenant owner
     * or he is owner of the account.
     *
     * If user has not necessary permission this function will throw Pluf_Exception_Forbidden exception.
     * If user is not defined this function wil throw Pluf_Exception_BadRequest exception.
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     * @throws Pluf_Exception_Forbidden
     * @throws Pluf_Exception_BadRequest
     * @return User_Account account which its authentication connections will be changed.
     */
    private static function checkAccess($request, $match)
    {
        if (array_key_exists('accountId', $match)) {
            // Check user is owner
            if ($request->user->id != $match['accountId'] && ! User_Precondition::isOwner($request)) {
                throw new Pluf_Exception_Forbidden('Not allowed to access or change others` authentication settings');
            }
            Pluf::loadFunction('Pluf_Shortcuts_GetObjectOr404');
            $user = Pluf_Shortcuts_GetObjectOr404('User_Account', $match['accountId']);
        } else {
            $user = $request->user;
        }
        if ($user->isAnonymous()) {
            throw new Pluf_Exception_BadRequest('User is not defined');
        }
        return $user;
    }
    
}

