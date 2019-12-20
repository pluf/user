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
 * User phone data model
 * 
 * It is used to store information of different type of calls 
 * contains phone numbers and mobile numbers of a user. 
 */
class User_Phone extends Pluf_Model
{

    function init()
    {
        $this->_a['verbose'] = 'phones';
        $this->_a['table'] = 'user_phones';
        $this->_a['cols'] = array(
            'id' => array(
                'type' => 'Pluf_DB_Field_Sequence',
                'is_null' => true,
                'editable' => false,
                'readable' => true
            ),
            'phone' => array(
                'type' => 'Pluf_DB_Field_Varchar',
                'is_null' => false,
                'size' => 32,
                'editable' => false,
                'readable' => true
            ),
            'type' => array(
                'type' => 'Pluf_DB_Field_Varchar',
                'is_null' => true,
                'size' => 64,
                'editable' => true,
                'readable' => true
            ),
            'is_verified' => array(
                'type' => 'Pluf_DB_Field_Boolean',
                'is_null' => false,
                'editable' => false,
                'readable' => true
            ),
            /*
             * Relations
             */
            'account_id' => array(
                'type' => 'Pluf_DB_Field_Foreignkey',
                'model' => 'User_Account',
                'name' => 'account',
                'relate_name' => 'phones',
                'graphql_name' => 'account',
                'is_null' => false,
                'editable' => false
            )
        );
        
        $this->_a['idx'] = array(
            'account_phone_unique_idx' => array(
                'col' => 'phone, account_id',
                'type' => 'unique', // normal, unique, fulltext, spatial
                'index_type' => '', // hash, btree
                'index_option' => '',
                'algorithm_option' => '',
                'lock_option' => ''
            )
        );
    }
}
