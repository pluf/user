<?php

/**
 * مدل داده‌ای کاربر را ایجاد می‌کند.
 * 
 * @param User $object
 * @return User
 */
function User_Shortcuts_UserDateFactory($object)
{
    if ($object == null || ! isset($object))
        return new User();
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
 * @param User $user            
 * @return Pluf_HTTP_Response_Json
 */
function User_Shortcuts_DeleteAvatar($user)
{
    $avatar = Pluf::factory('User_Avatar')->getOne('user=' . $user->id);
    if ($avatar) {
        $avatar->delete();
    }
    return new Pluf_HTTP_Response_Json($avatar);
}

/**
 * Returns avatar of given user if is existed.
 *
 * @param User $user            
 */
function User_Shortcuts_GetAvatar($user)
{
    // get avatar
    $avatar = Pluf::factory('User_Avatar')->getOne('user=' . $user->id);
    if ($avatar) {
        return new Pluf_HTTP_Response_File($avatar->getAbsloutPath(), $avatar->mimeType);
    }
    // default avatar
    $file = Pluf::f('user_avatar_default');
    return new Pluf_HTTP_Response_File($file, Pluf_FileUtil::getMimeType($file));
}

/**
 * Sets (updates or creates) avatar for given user
 * @param User $user
 * @param array $data
 * @return Pluf_HTTP_Response_Json
 */
function User_Shortcuts_UpdateAvatar($user, $data = array())
{
    $avatar = Pluf::factory('User_Avatar')->getOne('user=' . $user->id);
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