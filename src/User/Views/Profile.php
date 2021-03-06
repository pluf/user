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

/**
 * Manage profile information of users.
 *
 * This profile data model is default data model for profile which has some specific constant feilds.
 * To using dynamic profile see User_Views_Profile (which saves feilds for profiles in some collection).
 *
 * @author maso
 * @author hadi
 *        
 */
class User_Views_Profile
{

    /**
     * Returns profile information of specified user.
     * In this server each account has at most one profile.
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     */
    public static function get($request, $match)
    {
        if(isset($match['userId'])){        
            $user = Pluf_Shortcuts_GetObjectOr404('User_Account', $match['userId']);
        }else{
            $user = $request->user;
        }
        if (array_key_exists('profileId', $match)) {
            return Pluf_Shortcuts_GetObjectOr404('User_Profile', $match['profileId']);
        }
        $profile = self::getProfileOfUser($user);
        if($profile === null){
            $profile = new User_Profile();
            $profile->account_id = $user;
        }
        return $profile;
    }

    /**
     * Returns profile of given user. If no profile is created for given user returns null.
     * @param User_Account $user
     */
    public static function getProfileOfUser($user){
        $profiles = $user->get_profiles_list();
        if (count($profiles) === 0 || $profiles[0]->isAnonymous()) {
            return null;
        } else {
            return $profiles[0];
        }
    }
    
    /**
     * Update profile of specified user.
     * In this server each user has at most one profile.
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     * @param array $p
     * @throws \Pluf\Exception
     * @return Pluf_HTTP_Response_Json
     */
    public static function update($request, $match)
    {
        // Check access
        if(isset($match['userId'])){
            $user = Pluf_Shortcuts_GetObjectOr404('User_Account', $match['userId']);
        }else{
            $user = $request->user;
        }
        $profile = null;
        if (array_key_exists('profileId', $match)) {
            $profile = Pluf_Shortcuts_GetObjectOr404('User_Profile', $match['profileId']);
            if(!User_Precondition::isOwner($request) && $profile->account_id !== $user->id){
                throw new Pluf_HTTP_Error404('You are not allowed to change this profile.');
            }
            $form = Pluf_Shortcuts_GetFormForUpdateModel($profile, $request->REQUEST, array());
            $profile = $form->save();
        }else{
            $profile = self::getProfileOfUser($user);
            if ($profile === null) {
                $profile = new User_Profile();
                $profile->account_id = $user;
                $form = Pluf_Shortcuts_GetFormForModel($profile, $request->REQUEST, array());
                $profile = $form->save();
            } else {
                $form = Pluf_Shortcuts_GetFormForUpdateModel($profile, $request->REQUEST, array());
                $profile = $form->save();
            }
        }
        return $profile;
    }

    /**
     * Deletes profile of specified user.
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     * @param array $p
     * @throws \Pluf\Exception
     * @return Pluf_HTTP_Response_Json
     */
    public static function delete($request, $match)
    {
        // Check access
        if(isset($match['userId'])){
            $user = Pluf_Shortcuts_GetObjectOr404('User_Account', $match['userId']);
        }else{
            $user = $request->user;
        }
        $profile = null;
        if (array_key_exists('profileId', $match)) {
            $profile = Pluf_Shortcuts_GetObjectOr404('User_Profile', $match['profileId']);
            if($profile->account_id !== $user->id){
                throw new Pluf_HTTP_Error404('Profile is not blong to given user');
            }
        }else{
            $profile = self::getProfileOfUser($user);
            if ($profile === null) {
                return new User_Profile();
            }
        }
        $profile->delete();
        return $profile;
    }

    public static function find($request, $match)
    {
        if(isset($match['userId'])){
            $user = Pluf_Shortcuts_GetObjectOr404('User_Account', $match['userId']);
        }else{
            $user = $request->user;
        }
        $pag = new Pluf_Paginator(new User_Profile());
        $sql = new Pluf_SQL('account_id=%s', array(
            $user->id
        ));
        $pag->forced_where = $sql;
        $pag->list_filters = array(
            'id',
            'first_name',
            'last_name',
            'language',
            'timezone',
            'public_email',
            'account_id'
        );
        $search_fields = array(
            'first_name',
            'last_name',
            'language',
            'timezone',
            'public_email'
        );
        $sort_fields = array(
            'id',
            'first_name',
            'last_name',
            'language',
            'timezone',
            'public_email',
            'account_id'
        );
        $pag->configure(array(), $search_fields, $sort_fields);
        $pag->setFromRequest($request);
        return $pag->render_object();
    }

}


