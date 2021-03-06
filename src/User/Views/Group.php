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
 * لایه نمایش مدیریت گروه‌ها را به صورت پیش فرض ایجاد می‌کند
 *
 * @author maso
 *        
 */
class User_Views_Group extends Pluf_Views
{

    /**
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     */
    public function find($request, $match)
    {
        $pag = new Pluf_Paginator(new User_Group());
        $sql = new Pluf_SQL('user_account_id=%s', array(
            $match['userId']
        ));
        $pag->forced_where = $sql;
        $pag->list_filters = array(
            'id',
            'version',
            'name'
        );
        $search_fields = array(
            'name',
            'description'
        );
        $sort_fields = array(
            'id',
            'name',
            'version'
        );
        $pag->model_view = 'join_user';
        $pag->configure(array(), $search_fields, $sort_fields);
        $pag->setFromRequest($request);
        return $pag;
    }

    /**
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     */
    public function add($request, $match)
    {
        $user = Pluf_Shortcuts_GetObjectOr404('User_Account', $match['userId']);
        if (array_key_exists('id', $request->REQUEST)) {
            $group = Pluf_Shortcuts_GetObjectOr404('User_Group', $request->REQUEST['id']);
        } elseif (array_key_exists('group_id', $request->REQUEST)) {
            $group = Pluf_Shortcuts_GetObjectOr404('User_Group', $request->REQUEST['group_id']);
        } elseif (array_key_exists('group', $request->REQUEST)) {
            $group = Pluf_Shortcuts_GetObjectOr404('User_Group', $request->REQUEST['group']);
        } elseif (array_key_exists('group_name', $request->REQUEST)) {
            $group = new User_Group();
            $group = $group->getOne(array(
                'filter' => 'name="' . $request->REQUEST['group_name'] . '"'
            ));
            if (! isset($group) || $group->id === 0) {
                throw new Pluf_HTTP_Error404('Group not found');
            }
        }
        $user->setAssoc($group);
        return new Pluf_HTTP_Response_Json(array(
            'group_id' => $group->id,
            'group_name' => $group->name,
            'account_id' => $user->id,
            'user_login' => $user->login
        ));
    }

    /**
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     */
    public function get($request, $match)
    {
        $usr = Pluf_Shortcuts_GetObjectOr404('User_Account', $match['userId']);
        $groupModel = new User_Group();
        $param = array(
            'view' => 'join_user',
            'filter' => array(
                'id=' . $match['groupId'],
                'user_account_id=' . $usr->id
            )
        );
        $groups = $groupModel->getList($param);
        if ($groups->count() == 0) {
            throw new Pluf_Exception_DoesNotExist('User is not member of such group');
        }
        return new Pluf_HTTP_Response_Json($groups);
    }

    /**
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     */
    public function delete($request, $match)
    {
        $user = Pluf_Shortcuts_GetObjectOr404('User_Account', $match['userId']);
        $group = Pluf_Shortcuts_GetObjectOr404('User_Group', $match['groupId']);
        $user->delAssoc($group);
        return new Pluf_HTTP_Response_Json(array(
            'group_id' => $group->id,
            'group_name' => $group->name,
            'user_id' => $user->id,
            'user_login' => $user->login
        ));
    }
}
