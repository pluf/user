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
Pluf::loadFunction('User_Shortcuts_GetListCount');

/**
 * Manage users (CRUD on users account)
 */
class User_Views_Account
{
    
    /**
     * Creates new account (register new user) and a credential for it
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     * @return User_Account created account
     */
    public static function create($request, $match)
    {
        // Create account
        $extra = array();
        $data = array_merge($request->REQUEST, $request->FILES);
        //         $form = new User_Form_User($data, $extra);
        //         $cuser = $form->save();
        $usr = User_Account::getUser($data['login']);
        if($usr){
            throw new Pluf_Exception('Username is existed already.', 400);
        }
        $form = new User_Form_Account($data, $extra);
        $cuser = $form->save();
        // Create credential
        $credit = new User_Credential();
        $credit->setFromFormData(array('account_id' => $cuser->id));
        $credit->setPassword($data['password']);
        $success = $credit->create();
        if(!$success){
            throw new Pluf_Exception('An internal error is occured while create credential');
        }
        if(Pluf::f('account_force_activate', false)){
            // TODO: hadi, 1397-05-26: send mail to activate account by user
        }
        // Return response
        return $cuser;
    }
    
    /**
     * Returns information of specified user by id.
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     */
    public static function get($request, $match)
    {
        $user = Pluf_Shortcuts_GetObjectOr404('User_Account', $match['userId']);
        return $user;
    }
    
    /**
     * Updates information of specified user (by id)
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     * @return User_Account
     */
    public static function update($request, $match)
    {
        $model = Pluf_Shortcuts_GetObjectOr404('User_Account', $match['userId']);
        $form = Pluf_Shortcuts_GetFormForUpdateModel($model, $request->REQUEST, array());
        $request->user->setMessage(sprintf(__('Account data has been updated.'), (string) $model));
        return $form->save();
    }
    
    /**
     * Delete specified user (by id)
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     */
    public static function delete($request, $match)
    {
        $usr = new User_Account($match['userId']);
        //         $usr->delete();
        $usr->setDeleted(true);
        // TODO: Hadi, 1397-05-26: delete credentials and profile
        return $usr;
    }
    
    /**
     * Returns list of users.
     * Returned list can be customized using search fields, filters or sort
     * fields.
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     */
    public static function find($request, $match)
    {
        $pag = new Pluf_Paginator(new User_Account());
        $pag->forced_where = new Pluf_SQL('is_deleted=false');
        $pag->list_filters = array(
            'is_active',
            'login'
        );
        $search_fields = array(
            'login'
        );
        $sort_fields = array(
            'id',
            'login',
            'date_joined',
            'last_login',
            'is_active'
        );
        
        $pag->sort_order = array(
            'id',
            'DESC'
        );
        $pag->configure(array(), $search_fields, $sort_fields);
        $pag->items_per_page = User_Shortcuts_GetListCount($request);
        $pag->setFromRequest($request);
        return $pag->render_object();
    }
    
    
}
