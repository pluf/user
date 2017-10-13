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
    public function get($request, $match)
    {
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
        $docMap = User_Views_CProfile::getDocumentMap($profileDoc);
        $docMap['user'] = $userId;
        return new Pluf_HTTP_Response_Json($docMap);
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
    public function update($request, $match)
    {
        $currentUser = $request->user;
        $user = Pluf_Shortcuts_GetObjectOr404('Pluf_User', $match['userId']);
        if($currentUser->getId() === $user->getId() || Pluf_Precondition::ownerRequired($request)){
            $profileDoc = User_Views_CProfile::get_profile_document($user->id);
            User_Views_CProfile::putDocumentMap($profileDoc, $request->REQUEST);
            $docMap = User_Views_CProfile::getDocumentMap($profileDoc);
            $docMap['user'] = $user->id;
            return new Pluf_HTTP_Response_Json($docMap);
        }
        throw new Pluf_Exception_PermissionDenied("Permission is denied");
    }
    
    /**
     * Fetch profile of the user
     * 
     * If the profile does not exist then, a new profile will be crated.
     * @param integer $userId
     * @return Collection_Document
     */
    static function get_profile_document($userId){
        $user = Pluf_Shortcuts_GetObjectOr404('Pluf_User', $userId);
        // Find collection profile
        $collection = Collection_Shortcuts_GetCollectionByName(User_Constants::PROFILE_COLLECTION_NAME);
        if($collection === null){
            $collection = new Collection_Collection();
            $collection->name = User_Constants::PROFILE_COLLECTION_NAME;
            $collection->title= 'Collection for saving profile of users';
            $collection->create();
        }
        $cprofile = new User_CProfile();
        $cprofile = $cprofile->getOne('user = ' . $userId);
        if($cprofile === null){
            // create cprofile and document for profile of user
            $document = new Collection_Document();
            $document->collection = $collection;
            $document->create();
            $cprofile = new User_CProfile();
            $cprofile->user = $user;
            $cprofile->profile = $document;
            $cprofile->create();
        }
        $profileDoc = new Collection_Document($cprofile->profile);
        return $profileDoc;
    }
    
    /**
     * Gets all attributes of a document and return as map
     *
     * @param $document
     * @return array of attributes
     */
    static function getDocumentMap($document){
        // TODO: hadi 1396-07: It is better to move this function to Collection_Document class
        $attr = new Collection_Attribute();
        $map = $attr->getList(
            array(
                'filter' => 'document=' . $document->id
            ));
        $result = array();
        $iterator = $map->getIterator();
        while ($iterator->valid()) {
            $attr = $iterator->current();
            $result[$attr->key] = $attr->value;
            $iterator->next();
        }
        
        $result['id'] = $document->id;
        $result['collection'] = $document->collection;
        return $result;
    }
    
    static function putDocumentMap($document, $map){
        // TODO: hadi 1396-07: It is better to move this function to Collection_Document class
        $attrModel = new Collection_Attribute();
        foreach ($map as $key => $value) {
            // Ignore main attributes
            if ($key === 'id' || $key === 'collection') {
                continue;
            }
            $attr = $attrModel->getOne(
                array(
                    'filter' => array(
                        '`document`=' . $document->id,
                        "`key`='" . $key . "'"
                    )
                ));
            // FIXME: maso, 2017: remove key if value is empty
            if ($attr === null) {
                $attr2 = new Collection_Attribute();
                $attr2->document = $document;
                $attr2->key = $key;
                $attr2->value = $value;
                $attr2->create();
            } else {
                $attr->value = $value;
                $attr->update();
            }
        }
    }
}
