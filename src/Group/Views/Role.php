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
 * Manages roles of a group.
 *
 * @author maso
 * @author hadi
 *        
 */
class Group_Views_Role extends Pluf_Views
{

    /**
     * Adds a role to list of roles of a group.
     * Id of added role should be specified in request.
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     */
    public static function add($request, $match)
    {
        $group = Pluf_Shortcuts_GetObjectOr404('Group', $match['group_id']);
        $role = Pluf_Shortcuts_GetObjectOr404('Role', (isset($match['role_id'])) ? $match['role_id'] : $request->REQUEST['role_id']);
        $group->setAssoc($role);
        return $role;
    }

    /**
     * Returns list of roles of a group.
     * Returned list can be customized with some filter, condition and sort.
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     */
    public static function find($request, $match)
    {
        $pag = new Pluf_Paginator(new Role());
        $pag->list_filters = array(
            'name',
            'code_name',
            'application'
        );
        $search_fields = array(
            'name',
            'code_name',
            'description',
            'application'
        );
        $sort_fields = array(
            'id',
            'name',
            'code_name',
            'application',
            'description'
        );
        $pag->forced_where = new Pluf_SQL('group_id=%s', array(
            $match['group_id']
        ));
        $pag->configure(array(), $search_fields, $sort_fields);
        $pag->setFromRequest($request);
        $pag->model_view = 'join_group';
        return $pag->render_object();
    }

    /**
     * Returns information of a role of a group.
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     */
    public static function get($request, $match)
    {
        $group = Pluf_Shortcuts_GetObjectOr404('Group', $match['group_id']);
        $role = new Role();
        $roles = $role->getList(array(
            'view' => 'join_group',
            'filter' => array(
                'group_id=' . $group->id
            )
        ));
        if ($roles->count() == 0) {
            throw new Pluf_Exception_DoesNotExist('Group has not such role');
        }
        return $roles[0];
    }

    /**
     * Deletes a role from roles of a group.
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     */
    public static function delete($request, $match)
    {
        $group = Pluf_Shortcuts_GetObjectOr404('Group', $match['group_id']);
        $role = Pluf_Shortcuts_GetObjectOr404('Role', $match['role_id']);
        $group->delAssoc($role);
        return $group;
    }
}
