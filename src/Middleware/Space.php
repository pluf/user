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

use Pluf\HTTP\Request;
use Pluf\HTTP\Response;

/**
 * User space information
 *
 *
 * Allow to manage some user specific data and settings.
 *
 * @author hadi <mohammad.hadi.mansouri@dpq.co.ir>
 */
class Space /* extends \Pluf\Middleware */
{

    /**
     * Process the request.
     *
     * When processing the request, if user is found related user-space
     * will be fetch and loaded into $request->user_space.
     *
     * @param
     *            Pluf_HTTP_Request The request
     * @return bool false
     */
    function process_request(&$request)
    {
        if (! isset($request->user) || $request->user->isAnonymous()) {
            // user is not set so we set an empty fake UserSpace
            $request->user_space = new \Pluf\User\Space();
        } else {
            $userId = $request->user->getId();
            $space = new \Pluf\User\Space();
            $userSpace = $space->getOne('user=' . $userId);
            if ($userSpace == null) {
                $userSpace = new \Pluf\User\Space();
                $userSpace->__set('user', $request->user);
            }
            $request->user_space = $userSpace;
        }
        return false;
    }

    /**
     * Process the response of a view.
     *
     * If the user_space has been modified save it into the database.
     *
     * @param
     *            Pluf_HTTP_Request The request
     * @param
     *            Pluf_HTTP_Response The response
     * @return Response The response
     */
    function process_response(Request $request, Response $response): Response
    {
        if (! isset($request->user) || $request->user->isAnonymous()) {
            return $response;
        }
        if ($request->user_space->touched) {
            if ($request->user_space->id > 0) {
                $request->user_space->update();
            } else {
                $request->user_space->create();
            }
        }
        return $response;
    }
}
