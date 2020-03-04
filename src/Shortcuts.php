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
namespace Pluf\User;

use Pluf\Bootstrap;
use Pluf\Exception;
use Pluf\FileUtil;
use Pluf\HTTP\Response;

class Shortcuts extends \Pluf\Shortcuts
{

    /**
     * مدل داده‌ای کاربر را ایجاد می‌کند.
     *
     * @param Account $object
     * @return Account
     */
    function userDataFactory(Account $object)
    {
        if ($object == null || ! isset($object)) {
            return new Account();
        }
        return $object;
    }

    /**
     * بررسی حالت پسورد جدید
     *
     * @param String $pass
     * @throws Exception
     * @return String
     */
    function checkPassword($pass)
    {
        if ($pass == null || ! isset($pass)) {
            throw new Exception("Pasword must not be null");
        }
        return $pass;
    }

    /**
     * Deletes avatar of given user.
     *
     * @param Account $user
     * @return Response\Json
     */
    function deleteAvatar($user)
    {
        $avatar = new Avatar();
        $avatar->getOne('account_id=' . $user->id);
        if ($avatar) {
            $avatar->delete();
        }
        return new Response\Json($avatar);
    }

    /**
     * Returns avatar of given user if is existed.
     *
     * @param Account $user
     */
    function getAvatar($user)
    {
        // get avatar
        $avatar = new Avatar();
        $avatar->getOne('account_id=' . $user->id);
        if ($avatar) {
            return new Response\File($avatar->getAbsloutPath(), $avatar->mimeType);
        }
        // default avatar
        $file = Bootstrap::f('user_avatar_default');
        return new Response\File($file, FileUtil::getMimeType($file));
    }

    /**
     * Sets (updates or creates) avatar for given user
     *
     * @param Account $user
     * @param array $data
     * @return Response\Json
     */
    function updateAvatar($user, $data = array())
    {
        $avatar = new Avatar();
        $avatar->getOne('account_id=' . $user->id);
        if ($avatar) {
            $form = new Form\AvatarCrudForm($data, array(
                'model' => $avatar,
                'user' => $user
            ));
        } else {
            $form = new Form\AvatarCrudForm($data, array(
                'model' => new Avatar(),
                'user' => $user
            ));
        }
        return $form->save();
    }
}