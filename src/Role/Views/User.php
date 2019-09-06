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
Pluf::loadFunction('Pluf_Shortcuts_GetFormForModel');

/**
 * Manages users of a role.
 *
 * @author maso
 * @author hadi
 *        
 */
class Role_Views_User extends Pluf_Views
{

    /**
     * Add new user to a role.
     * In other word, grant a role to a user.
     * Id of added user should be specified in request.
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     */
    public static function add($request, $match)
    {
        $perm = Pluf_Shortcuts_GetObjectOr404('User_Role', $match['role_id']);
        if (array_key_exists('user_id', $match)) {
            $user = Pluf_Shortcuts_GetObjectOr404('User_Account', $match['user_id']);
        } elseif (array_key_exists('user_id', $request->REQUEST)) {
            $user = Pluf_Shortcuts_GetObjectOr404('User_Account', $request->REQUEST['user_id']);
        } elseif (array_key_exists('login', $request->REQUEST)) {
            $user = new User_Account();
            $user = $user->getUser($request->REQUEST['login']);
        }
        if (! isset($user) || $user->isAnonymous()) {
            throw new Pluf_HTTP_Error404(__('User not found'));
        }
        $perm->setAssoc($user);
        return $user;
    }

    /**
     * Returns list of users of a role.
     * Resulted list can be customized by using filters, conditions and sort rules.
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     */
    public static function find($request, $match)
    {
        $perm = Pluf_Shortcuts_GetObjectOr404('User_Role', $match['role_id']);
        $pag = new Pluf_Paginator(new User_Account());
        $sql = new Pluf_SQL('user_role_id=%s', array(
            $perm->id
        ));
        $pag->forced_where = $sql;
        $pag->list_filters = array(
            'is_active',
            'login',
            'first_name',
            'last_name',
            'email'
        );
        $pag->sort_order = array(
            'id',
            'ASC'
        );
        $search_fields = array(
            'login',
            'first_name',
            'last_name',
            'email'
        );
        $list_display = array(
            'login' => __('login'),
            'first_name' => __('first name'),
            'last_name' => __('last name'),
            'email' => __('email')
        );
        $sort_fields = array(
            'id',
            'login',
            'first_name',
            'last_name',
            'date_joined',
            'last_login'
        );
        $pag->model_view = 'join_role';
        $pag->configure($list_display, $search_fields, $sort_fields);
        $pag->setFromRequest($request);
        return $pag;
    }

    /**
     * Returns information of a user of a role.
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     */
    public static function get($request, $match)
    {
        $perm = Pluf_Shortcuts_GetObjectOr404('User_Role', $match['role_id']);
        $userModel = new User_Account();
        $param = array(
            'view' => 'join_role',
            'filter' => array(
                'id=' . $match['user_id'],
                'role_id=' . $perm->id
            )
        );
        $users = $userModel->getList($param);
        if ($users->count() == 0) {
            throw new Pluf_Exception_DoesNotExist('User has not such role');
        }
        return $users[0];
    }

    /**
     * Deletes a user from a role.
     * Id of deleted user should be specified in the match.
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     */
    public static function delete($request, $match)
    {
        $perm = Pluf_Shortcuts_GetObjectOr404('User_Role', $match['role_id']);
        $owner = Pluf_Shortcuts_GetObjectOr404('User_Account', $match['user_id']);
        $perm->delAssoc($owner);
        return $owner;
    }
}
