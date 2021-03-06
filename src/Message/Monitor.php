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

/**
 *
 * @author maso<mostafa.barmshory@dpq.co.ir>
 * @author hadi<mohammad.hadi.mansouri@dpq.co.ir>
 * @since 0.1.0
 */
class Message_Monitor
{

    /**
     * Retruns messages count
     *
     * @param Pluf_HTTP_Request $request            
     * @param array $match            
     */
    public static function count ($request, $match)
    {
        $userId = $request->user ? $request->user->id : 0;
        $sql = new Pluf_SQL('account_id=%s', array($userId));
        
        $message = new User_Message();
        $res = $message->getList(
                array(
                        'count' => true,
                        'filter' => $sql->gen()
                ));
        // Check permission
        return $res[0]['nb_items'];
    }
}
