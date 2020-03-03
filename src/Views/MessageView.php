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
Pluf::loadFunction('Pluf_Shortcuts_GetObjectOr404');

/**
 * لایه دسترسی به پیام‌ها
 *
 * سیستم بر اساس رویداده‌ها پیام‌هایی را برای کاربران ایجاد می‌کند. این
 * نمایش امکان دسترسی به این پیام‌ها را برای کاربر فراهم می‌کند.
 *
 * @author maso<mostafa.barmshory@dpq.co.ir>
 *        
 */
class MessageView extends \Pluf\Views
{

    /**
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     * @return Pluf_HTTP_Response_Json
     */
    public function find($request, $match)
    {
        $sql = new Pluf_SQL('account_id=%s', array(
            $request->user->id
        ));
        $p = array(
            'model' => 'User_Message',
            'sql' => $sql
        );
        return parent::findObject($request, $match, $p);
    }

    /**
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     * @return Pluf_HTTP_Response_Json
     */
    public function get($request, $match)
    {
        $message = Pluf_Shortcuts_GetObjectOr404('User_Message', $match['messageId']);
        Message_Security::canAccessMessage($request, $message);
        return $message;
    }

    /**
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     * @return Pluf_HTTP_Response_Json
     */
    public function delete($request, $match)
    {
        $message = Pluf_Shortcuts_GetObjectOr404('User_Message', $match['modelId']);
        Message_Security::canAccessMessage($request, $message);
        return parent::deleteObject($request, $match, array(
            'model' => 'User_Message'
        ));
    }

    /**
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     * @return Pluf_HTTP_Response_Json
     */
    public function deleteAll($request, $match)
    {
//         $user = $request->user;
//         $messageList = $user->get_messages_list();
        $messagePage = $this->find($request, $match)->render_object();
        $messageList = $messagePage['items'];
        foreach ($messageList as $msg) {
            $msg->delete();
        }
        return $this->find($request, $match);
    }
}

