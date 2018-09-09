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
 * Manages groups of a role.
 *
 * @author hadi
 *        
 */
class Role_Views_Group extends Pluf_Views
{

    /**
     * Add new group to a role.
     * In other word, grant a role to a group.
     * Id of added group should be specified in request.
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     */
    public static function add($request, $match)
    {
        $perm = Pluf_Shortcuts_GetObjectOr404('User_Role', $match['role_id']);
        $group = Pluf_Shortcuts_GetObjectOr404('User_Group', isset($match['group_id']) ? $match['group_id'] : $request->REQUEST['group_id']);
        $perm->setAssoc($group);
        return $group;
    }

    /**
     * Returns list of groups of a role.
     * Resulted list can be customized by using filters, conditions and sort rules.
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     */
    public static function find($request, $match)
    {
        $perm = new User_Role($match['role_id']);
        $grModel = new User_Group();
        $pag = new Pluf_Paginator($grModel);
        $sql = new Pluf_SQL('user_role_id=%s', array(
            $perm->id
        ));
        $pag->forced_where = $sql;
        $pag->list_filters = array(
            'version',
            'name',
            'description'
        );
        $pag->sort_order = array(
            'id',
            'ASC'
        );
        $search_fields = array(
            'name',
            'description'
        );
        $list_display = array(
            'name' => __('name'),
            'description' => __('description')
        );
        $sort_fields = array(
            'id',
            'name',
            'description'
        );
        $pag->model_view = 'join_role';
        $pag->configure($list_display, $search_fields, $sort_fields);
        $pag->setFromRequest($request);
        return $pag->render_object();
    }

    /**
     * Returns information of a group of a role.
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     */
    public static function get($request, $match)
    {
        $perm = Pluf_Shortcuts_GetObjectOr404('User_Role', $match['role_id']);
        $groupModel = new User_Group();
        $param = array(
            'view' => 'join_role',
            'filter' => array(
                'id=' . $match['group_id'],
                'role_id=' . $perm->id
            )
        );
        $groups = $groupModel->getList($param);
        if ($groups->count() == 0) {
            throw new Pluf_Exception_DoesNotExist('Group has not such role');
        }
        return $groups[0];
    }

    /**
     * Deletes a group from a role.
     * Id of deleted group should be specify in the match.
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     */
    public static function delete($request, $match)
    {
        $perm = Pluf_Shortcuts_GetObjectOr404('User_Role', $match['role_id']);
        $owner = Pluf_Shortcuts_GetObjectOr404('User_Group', $match['group_id']);
        $perm->delAssoc($owner);
        return $owner;
    }
}
