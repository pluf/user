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
namespace Pluf\User\Middleware;

use Pluf\Bootstrap;

/**
 * Basic Auth middleware
 *
 * Allow basic_auth for REST API.
 */
class BasicAuth
{

    /**
     * Process the request.
     *
     *
     * @param
     *            Pluf_HTTP_Request The request
     * @return bool false
     */
    function process_request(&$request)
    {
        if (isset($request->user) && ! $request->user->isAnonymous()) {
            return false;
        }
        if (! isset($request->SERVER['PHP_AUTH_USER'])) {
            return false;
        }

        $auth = array(
            'login' => $request->SERVER['PHP_AUTH_USER'],
            'password' => $request->SERVER['PHP_AUTH_PW']
        );
        foreach (Bootstrap::f('auth_backends', array(
            'User_Auth_ModelBackend'
        )) as $backend) {
            $user = call_user_func(array(
                $backend,
                'authenticate'
            ), $auth);
            if ($user !== false) {
                break;
            }
        }
        if ($user) {
            $request->user = $user;
        }
        return false;
    }
}