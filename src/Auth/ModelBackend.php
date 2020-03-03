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
namespace Pluf\User\Auth;

use Pluf\SQL;
use Pluf\User\Account;
use Pluf\User\Credential;

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

/**
 * Backend to authenticate against the User model.
 */
class ModelBackend
{

    /**
     * Given a user id, retrieve it.
     *
     * In the case of the User backend, the $user_id is the login.
     *
     * @return Account
     */
    public static function getUser($user_id)
    {
        $sql = new SQL('login=%s', array(
            $user_id
        ));
        $account = new Account();
        return $account->getOne($sql->gen());
    }

    /**
     * Returns credential data of given user if exist
     *
     * In the case of the User backend, the $user_id is the login.
     *
     * @return Account
     */
    public static function getCredential($user_id)
    {
        $sql = new SQL('login=%s', array(
            $user_id
        ));
        $account = new Account();
        return $account->getOne($sql->gen());
    }

    /**
     * Given an array with the authentication data, auth the user and return it.
     */
    public static function authenticate($auth_data)
    {
        $password = $auth_data['password'];
        $login = $auth_data['login'];
        $user = self::getUser($login);
        if (! $user) {
            return false;
        }
        if (! $user->isActive()) {
            return false;
        }
        return Credential::checkCredential($login, $password) ? $user : false;
    }
}

