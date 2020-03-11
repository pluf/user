<?php

/**
 * مدل داده‌ای کاربر را ایجاد می‌کند.
 * 
 * @param User_Account $object
 * @return User_Account
 */
function User_Shortcuts_UserDataFactory($object)
{
    if ($object == null || ! isset($object))
        return new User_Account();
    return $object;
}

/**
 * بررسی حالت پسورد جدید
 * 
 * @param String $pass
 * @throws Pluf_Exception
 * @return String
 */
function User_Shortcuts_CheckPassword($pass)
{
    if ($pass == null || ! isset($pass))
        throw new Pluf_Exception("Pasword must not be null");
    return $pass;
}

/**
 * Deletes avatar of given user.
 *
 * @param User_Account $user            
 * @return Pluf_HTTP_Response_Json
 */
function User_Shortcuts_DeleteAvatar($user)
{
    $avatar = Pluf::factory('User_Avatar')->getOne('account_id=' . $user->id);
    if ($avatar) {
        $avatar->delete();
    }
    return new Pluf_HTTP_Response_Json($avatar);
}

/**
 * Returns avatar of given user if is existed.
 *
 * @param User_Account $user            
 */
function User_Shortcuts_GetAvatar($user)
{
    // get avatar
    $avatar = new User_Avatar();
    $avatar = $avatar->getOne('account_id=' . $user->id);
    if ($avatar) {
        return new Pluf_HTTP_Response_File($avatar->getAbsloutPath(), $avatar->mimeType);
    }
    // default avatar
    $file = Pluf::f('user_avatar_default');
    return new Pluf_HTTP_Response_File($file, Pluf_FileUtil::getMimeType($file));
}

/**
 * Sets (updates or creates) avatar for given user
 * @param User_Account $user
 * @param array $data
 * @return Pluf_HTTP_Response_Json
 */
function User_Shortcuts_UpdateAvatar($user, $data = array())
{
    $avatar = Pluf::factory('User_Avatar')->getOne('account_id=' . $user->id);
    if ($avatar) {
        $form = new User_Form_Avatar($data, array(
            'model' => $avatar,
            'user' => $user
        ));
    } else {
        $form = new User_Form_Avatar($data, array(
            'model' => new User_Avatar(),
            'user' => $user
        ));
    }
    return $form->save();
}

/**
 * Returns list count for given request. 
 * 
 * If count is not set in request or count is more than a threshold (50) returns a default value (50).
 * 
 * @param Pluf_HTTP_Request $request
 * @return number
 */
function User_Shortcuts_GetListCount($request)
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