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

use Pluf\Model;

/**
 * User data model
 *
 * این مدل داده‌ای، یک مدل داده‌ای کلی است و همواره به صورت پیش فرض استفاده
 * می‌شود.
 * در صورت تمایل می‌توان از ساختارهای داده‌ای دیگر به عنوان مدل داده‌ای برای
 * کاربران
 * استفاده کرد.
 */
class Email extends Model
{

    function init()
    {
        $this->_a['verbose'] = 'emails';
        $this->_a['table'] = 'user_emails';
        $this->_a['cols'] = array(
            'id' => array(
                'type' => '\Pluf\DB\Field\Sequence',
                'is_null' => true,
                'editable' => false,
                'readable' => true
            ),
            'email' => array(
                'type' => '\Pluf\DB\Field\Varchar',
                'is_null' => false,
                'size' => 128,
                'editable' => false,
                'readable' => true
            ),
            'type' => array(
                'type' => '\Pluf\DB\Field\Varchar',
                'is_null' => true,
                'size' => 64,
                'editable' => true,
                'readable' => true
            ),
            'is_verified' => array(
                'type' => '\Pluf\DB\Field\Boolean',
                'is_null' => false,
                'editable' => false,
                'readable' => true
            ),
            /*
             * Relations
             */
            'account_id' => array(
                'type' => '\Pluf\DB\Field\Foreignkey',
                'model' => 'User_Account',
                'name' => 'account',
                'relate_name' => 'emails',
                'graphql_name' => 'account',
                'is_null' => false,
                'editable' => false
            )
        );

        $this->_a['idx'] = array(
            'account_email_unique_idx' => array(
                'col' => 'email, account_id',
                'type' => 'unique', // normal, unique, fulltext, spatial
                'index_type' => '', // hash, btree
                'index_option' => '',
                'algorithm_option' => '',
                'lock_option' => ''
            )
        );
    }
}
