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
 * Finds and returns an authentication engine by given type
 *
 * @param string $type
 * @throws Pluf_Exception_DoesNotExist
 * @return User_OAuth2_Engine
 */
function User_OAuth2_Shortcuts_GetEngineOr404($type)
{
    $items = User_OAuth2_Service::engines();
    foreach ($items as $item) {
        if ($item->getType() === $type) {
            return $item;
        }
    }
    throw new User_OAuth2_Exception_EngineNotFound("Authentication engine not found: " . $type);
}

/**
 *
 * @param number $id
 * @throws Pluf_HTTP_Error404
 * @return User_OAuth2Server
 */
function User_OAuth2_Shortcuts_GetServerOr404($id)
{
    $item = new User_OAuth2Server($id);
    if ((int) $id > 0 && $item->id == $id) {
        return $item;
    }
    throw new Pluf_HTTP_Error404("Authentication server not found (" . $id . ")");
}

