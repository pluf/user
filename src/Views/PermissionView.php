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
namespace Pluf\User\Views;

use Pluf\Paginator;
use Pluf\User\Role;
use Pluf\SQL;
use Pluf\HTTP\Request;

/**
 * لایه نمایش مدیریت کاربران را به صورت پیش فرض ایجاد می‌کند
 *
 * @author maso
 *        
 */
class User_Views_Permission
{

    /**
     *
     * @param Request $request
     * @param array $match
     */
    public function find($request, $match)
    {
        // XXX: maso, 1395: check user access.
        $model = Pluf_Shortcuts_GetObjectOr404('User_Account', $match['userId']);
        $pag = new Paginator(new Role());
        $pag->configure(array(), array( // search
            'name',
            'description'
        ), array( // sort
            'id',
            'name',
            'application',
            'version'
        ));
        $pag->action = array();
        $pag->sort_order = array(
            'version',
            'DESC'
        );
        $pag->setFromRequest($request);
        $pag->model_view = 'join_user';
        $pag->forced_where = new SQL('user_account_id=%s', array(
            $model->id
        ));
        return $pag;
    }

    /**
     *
     * @param Request $request
     * @param array $match
     */
    public function create(Request $request, $match)
    {
        // XXX: maso, 1395: check user access.
        // Hadi, 1396: check user access
        $user = Pluf_Shortcuts_GetObjectOr404('User_Account', $match['userId']);
        $roleId = array_key_exists('id', $request->REQUEST) ? $request->REQUEST['id'] : $request->REQUEST['role'];
        $perm = Pluf_Shortcuts_GetObjectOr404('User_Role', $roleId);
        $user->setAssoc($perm);
        return $user;
    }

    /**
     *
     * @param Request $request
     * @param array $match
     */
    public function get($request, $match)
    {
        $perm = Pluf_Shortcuts_GetObjectOr404('User_Role', $match['roleId']);
        return $perm;
    }

    /**
     *
     * @param Request $request
     * @param array $match
     */
    public function delete($request, $match)
    {
        // XXX: maso, 1395: check user access.
        // Hadi, 1396: check user access
        $user = Pluf_Shortcuts_GetObjectOr404('User_Account', $match['userId']);
        $perm = Pluf_Shortcuts_GetObjectOr404('User_Role', $match['roleId']);
        $user->delAssoc($perm);
        return $user;
    }
}
