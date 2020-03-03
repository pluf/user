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

use Pluf\User\Account;
use Pluf\HTTP\Response;
use Pluf\HTTP\Request;

/**
 * User session
 *
 *
 * Allow a session object in the request and the automatic
 * login/logout of a user if a standard authentication against the
 * User model is performed.
 */
class Session
{

    /**
     * کلد جلسه کاربر را تعیین می‌کند.
     */
    public $session_key = '_PX_User_auth';

    /**
     * Process the request.
     *
     * When processing the request, if a session is found with
     * User creditentials the corresponding user is loaded into
     * $request->user.
     *
     * FIXME: We should logout everybody when the session table is emptied.
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
        if ($request->session->containsKey($this->session_key)) {
            // We can get the corresponding user
            $id = $request->session->getData($this->session_key);
            $found_user = new Account($id);
            if ($found_user->id == $id) {
                // User_Account found!
                $request->user = $found_user;
                // If the last login is from 12h or more, set it to
                // now.
                if (43200 < \Pluf\Date::ompare($request->user->last_login)) {
                    $request->user->last_login = gmdate('Y-m-d H:i:s');
                    $request->user->update();
                }
                return false;
            }
        }
        $request->user = new Account();
        $request->user->id = 0;
        return false;
    }

    /**
     * Process the response of a view.
     *
     * If the session has been modified save it into the database.
     * Add the session cookie to the response.
     *
     * @param
     *            Request The request
     * @param
     *            Response The response
     * @return Response The response
     */
    function process_response(Request $request, Response $response): Response
    {
        if (! $request->user->isAnonymous()) {
            $request->session->setData($this->session_key, $request->user->id);
        }
        return $response;
    }
}


