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
 * User data model
 *
 * این مدل داده‌ای، یک مدل داده‌ای کلی است و همواره به صورت پیش فرض استفاده
 * می‌شود.
 * در صورت تمایل می‌توان از ساختارهای داده‌ای دیگر به عنوان مدل داده‌ای برای
 * کاربران
 * استفاده کرد.
 */
class User_Email extends Pluf_Model
{

    /**
     * Cache of the Role.
     */
    public $_cache_perms = null;

    function init()
    {
        $this->_a['verbose'] = 'emails';
        $this->_a['table'] = 'user_emails';
        $this->_a['cols'] = array(
            // It is mandatory to have an "id" column.
            'id' => array(
                'type' => 'Pluf_DB_Field_Sequence',
                // It is automatically added.
                'blank' => true,
                'editable' => false,
                'readable' => true
            ),
            'address' => array(
                'type' => 'Pluf_DB_Field_Varchar',
                'blank' => true,
                'size' => 128,
                'verbose' => 'Email address',
                'editable' => true,
                'readable' => true
            ),
            'verified' => array(
                'type' => 'Pluf_DB_Field_Boolean',
                'blank' => false,
                'verbose' => 'Verified',
                'editable' => true,
                'readable' => true
            ),
            'verification_token' => array(
                'type' => 'Pluf_DB_Field_Varchar',
                'blank' => true,
                'verbose' => 'Verification code',
                'editable' => false,
                'readable' => false
            ),
        );
    }

    /**
     *
     * {@inheritdoc}
     * @see Pluf_Model::__toString()
     */
    function __toString()
    {
        $repr = $this->last_name;
        if (strlen($this->first_name) > 0) {
            $repr = $this->first_name . ' ' . $repr;
        }
        return $repr;
    }
}