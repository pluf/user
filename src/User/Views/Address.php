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
Pluf::loadFunction('Pluf_Shortcuts_GetObjectOr404');

/**
 * Manage addresses of account
 */
class User_Views_Address extends Pluf_Views
{

    /**
     * Creates new address for account
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     * @return User_Address created account
     */
    public function create($request, $match)
    {
        $usr = self::checkAccess($request, $match);
        $match['parentId'] = $usr->id;
        $p = array(
            'parent' => 'User_Account',
            'parentKey' => 'account_id',
            'model' => 'User_Address'
        );
        return $this->createManyToOne($request, $match, $p);
    }

    /**
     * Returns specified address of an account
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     * @return User_Address
     */
    public function get($request, $match)
    {
        $usr = self::checkAccess($request, $match);
        $match['parentId'] = $usr->id;
        $match['modelId'] = $match['addressId'];
        $p = array(
            'parent' => 'User_Account',
            'parentKey' => 'account_id',
            'model' => 'User_Address'
        );
        return $this->getManyToOne($request, $match, $p);
    }
    
    /**
     * Lists addresses of an account
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
            'model' => 'User_Address'
        );
        return $this->findManyToOne($request, $match, $p);
    }
    
    /**
     * Updates information of the address of an account.
     *
     * Note that the address address could not be changed by updating process.
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     * @return User_Address
     */
    public function update($request, $match)
    {
        $usr = self::checkAccess($request, $match);
        $match['parentId'] = $usr->id;
        $match['modelId'] = $match['addressId'];
        $p = array(
            'parent' => 'User_Account',
            'parentKey' => 'account_id',
            'model' => 'User_Address'
        );
        return $this->updateManyToOne($request, $match, $p);
    }
    
    /**
     * Delete specified address of an account
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     * @return User_Address
     */
    public function delete($request, $match)
    {
        $usr = self::checkAccess($request, $match);
        $match['parentId'] = $usr->id;
        $match['modelId'] = $match['addressId'];
        $p = array(
            'parent' => 'User_Account',
            'parentKey' => 'account_id',
            'model' => 'User_Address'
        );
        return $this->deleteManyToOne($request, $match, $p);
    }
    
    /**
     * Checks permission to change addresses and returns account which its address will be changed.
     *
     * Checks the permission of the requester user to change addresses of defined account in the request.
     * The requester user have permission to change addresses of an account if and only if he is tenant owner
     * or he is owner of the account.
     *
     * If user has not necessary permission this function will throw Pluf_Exception_Forbidden exception.
     * If user is not defined this function wil throw Pluf_Exception_BadRequest exception.
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     * @throws Pluf_Exception_Forbidden
     * @throws Pluf_Exception_BadRequest
     * @return User_Account account which its addresses will be changed.
     */
    private static function checkAccess($request, $match)
    {
        if (array_key_exists('accountId', $match)) {
            // Check user is owner
            if ($request->user->id != $match['accountId'] && ! User_Precondition::isOwner($request)) {
                throw new Pluf_Exception_Forbidden('Not allowed to change others` addresses');
            }
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

