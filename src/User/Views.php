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
Pluf::loadFunction('Pluf_HTTP_URL_urlForView');
Pluf::loadFunction('Pluf_Shortcuts_GetObjectOr404');
Pluf::loadFunction('Pluf_Shortcuts_GetFormForModel');
Pluf::loadFunction('Pluf_Shortcuts_GetFormForUpdateModel');
Pluf::loadFunction('User_Shortcuts_GetAvatar');
Pluf::loadFunction('User_Shortcuts_DeleteAvatar');
Pluf::loadFunction('User_Shortcuts_UpdateAvatar');

/**
 *
 * @author maso<mostafa.barmshory@dpq.co.ir>
 * @author hadi<mohammad.hadi.mansouri@dpq.co.ir>
 * @since 0.1.0
 */
class User_Views extends Pluf_Views
{

    /**
     * Retruns account information of current user
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     */
    public function getAccount($request, $match)
    {
        return $request->user;
    }

    /**
     * Updates account information of current user
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     */
    public function updateAccount($request, $match)
    {
        $model = Pluf_Shortcuts_GetObjectOr404('User_Account', $request->user->id);
        $form = Pluf_Shortcuts_GetFormForUpdateModel($model, $request->REQUEST, array());
        return $form->save();
    }

    /**
     * Delete account of current user.
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     */
    public function deleteAccount($request, $match)
    {
        $user = Pluf_Shortcuts_GetObjectOr404('User', $request->user->id);
        $request->user->delete();
        return $user;
    }
}
