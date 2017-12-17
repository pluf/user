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
     * @param unknown_type $request            
     * @param unknown_type $match            
     */
    public static function add($request, $match)
    {
        $perm = Pluf_Shortcuts_GetObjectOr404('Pluf_Permission', $match['id']);
        if (array_key_exists('user', $request->REQUEST)) {
            $user = Pluf_Shortcuts_GetObjectOr404('Pluf_User', $request->REQUEST['user']);
        } elseif (array_key_exists('login', $request->REQUEST)) {
            $user = new Pluf_User();
            $user = $user->getOne(array(
                'filter' => 'login="' . $request->REQUEST['login'].'"'
            ));
            if (! isset($user) || $user->isAnonymous()) {
                throw new Pluf_HTTP_Error404(__('User not found'));
            }
        }
        Pluf_Precondition::couldAddRole($request, $user->id, $perm->id);
        $row = Pluf_RowPermission::add($user, null, $perm, false);
        return new Pluf_HTTP_Response_Json($row);
    }

    /**
     * Returns list of users of a role.
     * Resulted list can be customized by using filters, conditions and sort rules.
     *
     * @param unknown_type $request            
     * @param unknown_type $match            
     */
    public static function find($request, $match)
    {
        $perm = new Pluf_Permission($match['id']);
        $pag = new Pluf_Paginator(new Pluf_User());
        $pag->items_per_page = Role_Views::getListCount($request);
        $sql = new Pluf_SQL('permission=%s AND owner_class=%s', array(
            $perm->id,
            // XXX: maso, 1395: user type is getting from config
            'Pluf_User'
        ));
        $pag->forced_where = $sql;
        $pag->list_filters = array(
            'administrator',
            'staff',
            'active',
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
        $pag->model_view = 'user_permission';
        $pag->configure($list_display, $search_fields, $sort_fields);
        $pag->setFromRequest($request);
        return new Pluf_HTTP_Response_Json($pag->render_object());
    }

    /**
     * Returns information of a user of a role.
     *
     * @param unknown_type $request            
     * @param unknown_type $match            
     */
    public static function get($request, $match)
    {
        $perm = Pluf_Shortcuts_GetObjectOr404('Pluf_Permission', $match['id']);
        $userModel = new Pluf_User();
        $param = array(
            'view' => 'user_permission',
            'filter' => array(
                $userModel->_a['table'].'.id=' . $match['userId'],
                'permission=' . $perm->id
            )
        );
        $users = $userModel->getList($param);
        if($users->count() == 0){
            throw new Pluf_Exception_DoesNotExist('User has not such role');
        }
        return new Pluf_HTTP_Response_Json($users);
    }

    /**
     * Deletes a user from a role.
     * Id of deleted user should be specified in the match.
     *
     * @param unknown_type $request            
     * @param unknown_type $match            
     */
    public static function delete($request, $match)
    {
        $perm = Pluf_Shortcuts_GetObjectOr404('Pluf_Permission', $match['id']);
        $owner = Pluf_Shortcuts_GetObjectOr404('Pluf_User', $match['userId']);
        Pluf_Precondition::couldRemoveRole($request, $owner->id, $perm->id);
        $row = Pluf_RowPermission::remove($owner, null, $perm);
        return new Pluf_HTTP_Response_Json($row);
    }
}
