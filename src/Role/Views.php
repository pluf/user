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
 * Manages roles
 *
 * @author maso
 *
 */
class Role_Views extends Pluf_Views
{

    /**
     * Creates new role.
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     */
    public static function create($request, $match)
    {
        $model = new User_Role();
        $form = Pluf_Shortcuts_GetFormForModel($model, $request->REQUEST, array());
        return new Pluf_HTTP_Response_Json($form->save());
    }

    /**
     * Returns information of a role.
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     */
    public function get($request, $match)
    {
        return new Pluf_HTTP_Response_Json(Pluf_Shortcuts_GetObjectOr404('User_Role', $match['id']));
    }

    /**
     * Updates information of a role.
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     */
    public static function update($request, $match)
    {
        $model = Pluf_Shortcuts_GetObjectOr404('User_Role', $match['id']);
        $form = Pluf_Shortcuts_GetFormForUpdateModel($model, $request->REQUEST, array());
        $model = $form->save();
        $request->user->setMessage(sprintf(__('Role data has been updated.'), (string) $model));
        return new Pluf_HTTP_Response_Json($model);
    }

    /**
     * Deletes a role.
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     */
    public function delete($request, $match)
    {
        $model = Pluf_Shortcuts_GetObjectOr404('User_Role', $match['id']);
        $model2 = new User_Role($match['id']);
        if ($model->delete()) {
            return new Pluf_HTTP_Response_Json($model2);
        }
        throw new Pluf_HTTP_Error500('Unexpected error while removing role: ' . $model2->code_name);
    }

    static function getListCount($request)
    {
        $count = 50;
        if (array_key_exists('_px_ps', $request->GET)) {
            $count = $request->GET['_px_ps'];
            if ($count == 0 || $count > 50) {
                $count = 50;
            }
        }
        return $count;
    }
}
