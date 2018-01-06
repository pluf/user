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
Pluf::loadFunction('Pluf_Shortcuts_GetObjectOr404');
Pluf::loadFunction('Pluf_Shortcuts_GetFormForModel');

/**
 * Manages users of a group.
 *
 * @author maso
 * @author hadi
 *        
 */
class Group_Views_User extends Pluf_Views
{

    /**
     * Adds new user to list of users of a group.
     * Id of added user should be specified in request.
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     */
    public static function add($request, $match)
    {
        $group = Pluf_Shortcuts_GetObjectOr404('Group', $match['group_id']);
        if (array_key_exists('user_id', $match)) {
            $user = Pluf_Shortcuts_GetObjectOr404('User', $match['user_id']);
        } elseif (array_key_exists('user_id', $request->REQUEST)) {
            $user = Pluf_Shortcuts_GetObjectOr404('User', $request->REQUEST['user_id']);
        } elseif (array_key_exists('login', $request->REQUEST)) {
            $user = new User();
            $user = $user->getUser($request->REQUEST['login']);
        }
        if (! isset($user) || $user->isAnonymous()) {
            throw new Pluf_HTTP_Error404(__('User not found'));
        }
        $group->setAssoc($user);
        return $user;
    }

    /**
     * Returns list of users of a group.
     * Resulted list can be customized by using filters, conditions and sort rules.
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     */
    public static function find($request, $match)
    {
        $pag = new Pluf_Paginator(new User());
        $sql = new Pluf_SQL('group_id=%s', array(
            $match['group_id']
        ));
        $pag->forced_where = $sql;
        $pag->list_filters = array(
//             'administrator',
//             'staff',
            'active'
        );
        $search_fields = array(
            'login',
            'first_name',
            'last_name',
            'email'
        );
        $sort_fields = array(
            'id',
            'login',
            'first_name',
            'last_name',
            'date_joined',
            'last_login'
        );
        $pag->model_view = 'join_group';
        $pag->configure(array(), $search_fields, $sort_fields);
        $pag->setFromRequest($request);
        return $pag->render_object();
    }

    /**
     * Returns information of a user of a group.
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     */
    public static function get($request, $match)
    {
        $group = Pluf_Shortcuts_GetObjectOr404('Group', $match['group_id']);
        $userModel = new User();
        $param = array(
            'view' => 'join_group',
            'filter' => array(
                'id=' . $match['user_id'],
                'group_id=' . $group->id
            )
        );
        $users = $userModel->getList($param);
        if ($users->count() == 0) {
            throw new Pluf_Exception_DoesNotExist('Group has not such user');
        }
        return $users[0];
    }

    /**
     * Deletes a user from a group.
     * Id of deleted user should be specified in the match.
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     */
    public static function delete($request, $match)
    {
        $group = Pluf_Shortcuts_GetObjectOr404('Group', $match['group_id']);
        $user = Pluf_Shortcuts_GetObjectOr404('User', $match['user_id']);
        $group->delAssoc($user);
        return $user;
    }
}
