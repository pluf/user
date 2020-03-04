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
namespace Pluf\User;

use Pluf\Model;

/**
 * ساختارهای پیام سیستم را ایجاد می‌کند
 *
 * این پیام‌ها توسط سیستم ایجاد شده و برای کاربران ارسال می‌شود. این پیام‌ها
 * تغییراتی که مربوط به داده‌ها و یا موجودیت‌های کاربر باشد صادر می‌شود و باید
 * به مرور زمان از بین برود.
 *
 * @author maso
 *        
 */
class Message extends Model
{

    /**
     *
     * {@inheritdoc}
     * @see Model::init()
     */
    function init()
    {
        $this->_a['table'] = 'user_messages';
        $this->_a['verbose'] = 'user messages';
        $this->_a['cols'] = array(
            // It is mandatory to have an "id" column.
            'id' => array(
                'type' => '\Pluf\DB\Field\Sequence',
                // It is automatically added.
                'blank' => true,
                'editable' => false,
                'readable' => true
            ),
            'account_id' => array(
                'type' => '\Pluf\DB\Field\Foreignkey',
                'model' => '\Pluf\User\Account',
                'name' => 'account',
                'graphql_name' => 'account',
                'relate_name' => 'messages',
                'is_null' => false,
                'editable' => false
            ),
            'message' => array(
                'type' => '\Pluf\DB\Field\Text',
                'is_null' => false,
                'editable' => false,
                'readable' => true
            ),
            'creation_dtime' => array(
                'type' => '\Pluf\DB\Field\Datetime',
                'is_null' => true,
                'editable' => false,
                'readable' => true
            )
        );
        $this->_a['idx'] = array(
            'message_user_idx' => array(
                'type' => 'normal',
                'col' => 'account_id'
            )
        );
    }

    /**
     *
     * {@inheritdoc}
     * @see Model::__toString()
     */
    function __toString()
    {
        return $this->message;
    }

    /**
     *
     * {@inheritdoc}
     * @see Model::preSave()
     */
    function preSave($create = false)
    {
        if ($this->id == '') {
            $this->creation_dtime = gmdate('Y-m-d H:i:s');
        }
    }
}
