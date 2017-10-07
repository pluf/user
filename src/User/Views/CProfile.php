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

Pluf::loadFunction('Pluf_HTTP_URL_urlForView');
Pluf::loadFunction('Pluf_Shortcuts_GetObjectOr404');
Pluf::loadFunction('Pluf_Shortcuts_GetFormForModel');
Pluf::loadFunction('User_Shortcuts_UpdateProfile');

/**
 * Manage profile information of users.
 *
 * @author maso
 * @author hadi
 *        
 */
class User_Views_CProfile
{

    /**
     * Returns profile information of specified user.
     * Data model of profile can be different in each system. Also loading information of user is lazy,
     * so profile is not loaded until a request occure.
     *
     * @param Pluf_HTTP_Request $request            
     * @param array $match            
     */
    public static function get($request, $match)
    {
        // Find collection profile
        $collection = Collection_Shortcuts_GetCollectionByNameOr404(Collection_Constants::PROFILE_COLLECTION_NAME);
        $userId = $match['userId'];
//         $user = Pluf_Shortcuts_GetObjectOr404('Pluf_User', $userId);
//         try {
//             $profile = $user->getProfile();
//         } catch (Pluf_Exception_DoesNotExist $ex) {
//             $profile = new $profile_model();
//             $profile->user = $user;
//             $profile->create();
//         }
//         return new Pluf_HTTP_Response_Json($profile);
        $profileDoc = User_Views_CProfile::get_profile_document($userId);
        return new Pluf_HTTP_Response_Json($profileDoc);
    }

    private static function get_profile_document($userId){
        $cprofile = new User_CProfile();
        $cprofile = $cprofile->getOne('user = ' . $userId);
        if($cprofile === null){
            // create cprofile and document for profile of user
            $document = new Collection_Document();
            $document->collection = $collection->id;
            $document->create();
            $cprofile = new User_CProfile();
            $cprofile->user = $userId;
            $cprofile->profile = $document->id;
            $cprofile->create();
        }
        $profileDoc = new Collection_Document($cprofile->profile);
        return $profileDoc;
    }
    
    /**
     * Update profile of specified user.
     *
     * @param Pluf_HTTP_Request $request            
     * @param array $match    
     * @param array $p        
     * @throws Pluf_Exception
     * @return Pluf_HTTP_Response_Json
     */
    public static function update($request, $match, $p)
    {
        $currentUser = $request->user;
        $user = Pluf_Shortcuts_GetObjectOr404('Pluf_User', $match['userId']);
        if($currentUser->getId() === $user->getId() || Pluf_Precondition::ownerRequired($request)){
            $profileDoc = User_Views_CProfile::get_profile_document($user->id);
            $this->putDocumentMap($document, $request->REQUEST);
            return new Pluf_HTTP_Response_Json($this->getDocumentMap($document));
        }
        throw new Pluf_Exception_PermissionDenied("Permission is denied");
    }
}
