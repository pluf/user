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
class User_Profile extends Pluf_Model
{

    function init()
    {
        $langs = Pluf::f('languages', array(
            'en'
        ));
        $this->_a['verbose'] = 'profiles';
        $this->_a['table'] = 'user_profiles';
        $this->_a['cols'] = array(
            // It is mandatory to have an "id" column.
            'id' => array(
                'type' => 'Sequence',
                // It is automatically added.
                'is_null' => true,
                'editable' => false,
                'readable' => true
            ),
            'first_name' => array(
                'type' => 'Varchar',
                'is_null' => true,
                'size' => 100,
                'editable' => true
            ),
            'last_name' => array(
                'type' => 'Varchar',
                'is_null' => false,
                'size' => 100,
                'editable' => true
            ),
            'public_email' => array(
                'type' => 'Email',
                'is_null' => true,
                // @note: hadi, 1395-07-14: change email is done by
                // another process.
                'editable' => true,
                'readable' => true
            ),
            'language' => array(
                'type' => 'Varchar',
                'is_null' => true,
                'default' => $langs[0],
                'size' => 5,
                'editable' => true
            ),
            'timezone' => array(
                'type' => 'Varchar',
                'is_null' => true,
                'default' => date_default_timezone_get(),
                'size' => 45,
                'verbose' => __('time zone'),
                'help_text' => __('Time zone of the user to display the time in local time.'),
                'editable' => true
            ),
            'national_code' => array(
                'type' => 'Varchar',
                'is_null' => true,
                'size' => 32,
                'editable' => true
            ),
            'gender' => array(
                'type' => 'Varchar',
                'is_null' => true,
                'size' => 16,
                'editable' => true
            ),
            'weight' => array(
                'type' => 'Float',
                'is_null' => true,
                'editable' => true
            ),
            'birthday' => array(
                'type' => 'Date',
                'default' => '0000-00-00',
                'is_null' => true,
                'editable' => true
            ),
            /*
             * Foreign keys
             */
            'account_id' => array(
                'type' => 'Foreignkey',
                'model' => 'User_Account',
                'name' => 'account',
                'relate_name' => 'profiles',
                'graphql_name' => 'account',
                'is_null' => false,
                'editable' => false
            )
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
        return trim($repr);
    }
}
