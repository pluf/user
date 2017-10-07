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


/**
 * Provide authentication functionality for users.
 *
 * @author maso <mostafa.barmshory@dpq.co.ir>
 * @author hadi<mohammad.hadi.mansouri@dpq.co.ir>
 *        
 */
class User_Views_Authentication
{

    /**
     * Login user
     *
     * As a result, returns information of loged in user as JSON information (if login was successful).
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     * @return Pluf_User
     */
    public function login($request, $match)
    {
        if (! $request->user->isAnonymous()) {
            return $request->user;
        }
        
        $backends = Pluf::f('auth_backends', array(
            'Pluf_Auth_ModelBackend'
        ));
        foreach ($backends as $backend) {
            $user = call_user_func(array(
                $backend,
                'authenticate'
            ), $request->POST);
            if ($user !== false) {
                break;
            }
        }
        
        if (false === $user) {
            throw new Pluf_Exception(__('user authentication incorrect'));
        }
        
        $request->user = $user;
        $request->session->clear();
        $request->session->setData('login_time', gmdate('Y-m-d H:i:s'));
        $user->last_login = gmdate('Y-m-d H:i:s');
        $user->update();
        
        return $user;
    }

    /**
     * Logout session
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     * @return Pluf_User
     */
    public function logout($request, $match)
    {
        $user_model = Pluf::f('pluf_custom_user', 'Pluf_User');
        $request->user = new $user_model();
        $request->session->clear();
        $request->session->setData('logout_time', gmdate('Y-m-d H:i:s'));
        return $request->user;
    }
}
