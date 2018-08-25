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
Pluf::loadFunction('Collection_Shortcuts_GetCollectionByName');

/**
 * Manage profile information of users.
 *
 * This profile data model is default data model for profile which has some specific constant feilds.
 * To using dynamic profile see User_Views_CProfile (which saves feilds for profiles in some collection).
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
        $user = Pluf_Shortcuts_GetObjectOr404('User_Account', $match['userId']);
        $profile = null;
        if (array_key_exists('profileId', $match)) {
            $profile = Pluf_Shortcuts_GetObjectOr404('User_Profile', $match['profileId']);
        } else {
            $profile = self::getProfileOfUser($user);
            if(!$profile){
                $profile = new User_Profile();
            }
        }
        return $profile;
    }

    /**
     * Returns profile of given user. If no profile is created for given user returns null.
     * @param User_Account $user
     */
    public static function getProfileOfUser($user){
        $profiles = $user->get_profile_list();
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
     * @throws Pluf_Exception
     * @return Pluf_HTTP_Response_Json
     */
    public static function update($request, $match)
    {
        // Check access
        $currentUser = $request->user;
        $user = Pluf_Shortcuts_GetObjectOr404('User_Account', $match['userId']);
        if ($currentUser->getId() !== $user->getId() && ! User_Precondition::isOwner($request)) {
            throw new Pluf_Exception_PermissionDenied("Permission is denied");
        }
        
        $profile = null;
        if (array_key_exists('profileId', $match)) {
            $profile = Pluf_Shortcuts_GetObjectOr404('User_Profile', $match['profileId']);
            if($profile->account_id !== $user->id){
                throw new Pluf_HTTP_Error404('Profile is not blong to given user');
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
}


