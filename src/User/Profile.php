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
class Profile extends Pluf_Model
{

    /**
     * Cache of the Role.
     */
    public $_cache_perms = null;

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
                'type' => 'Pluf_DB_Field_Sequence',
                // It is automatically added.
                'blank' => true,
                'editable' => false,
                'readable' => true
            ),
            'first_name' => array(
                'type' => 'Pluf_DB_Field_Varchar',
                'blank' => true,
                'size' => 100,
                'verbose' => __('first name'),
                'editable' => true,
                'readable' => true
            ),
            'last_name' => array(
                'type' => 'Pluf_DB_Field_Varchar',
                'blank' => false,
                'size' => 100,
                'verbose' => __('last name'),
                'editable' => true,
                'readable' => true
            ),
            'public_email' => array(
                'type' => 'Pluf_DB_Field_Email',
                'blank' => false,
                'verbose' => __('email'),
                // @note: hadi, 1395-07-14: change email is done by
                // another process.
                'editable' => false,
                'readable' => true
            ),
            'language' => array(
                'type' => 'Pluf_DB_Field_Varchar',
                'blank' => true,
                'default' => $langs[0],
                'size' => 5,
                'verbose' => __('language'),
                'help_text' => __('Prefered language of the user for the interface. Use the 2 or 5 letter code like "fr", "en", "fr_QC" or "en_US".')
            ),
            'timezone' => array(
                'type' => 'Pluf_DB_Field_Varchar',
                'blank' => true,
                'default' => date_default_timezone_get(),
                'size' => 45,
                'verbose' => __('time zone'),
                'help_text' => __('Time zone of the user to display the time in local time.')
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
        return $repr;
    }
}
