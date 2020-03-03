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
 * User address data model
 *
 * Stores information about address or location of an account
 *
 * @author hadi <mohammad.hadi.mansouri@dpq.co.ir>
 *        
 */
class Address extends Model
{

    /**
     *
     * @see Model::init()
     */
    function init()
    {
        $this->_a['table'] = 'user_addresses';
        $this->_a['verbose'] = 'User Address';
        $this->_a['cols'] = array(
            'id' => array(
                'type' => 'Pluf_DB_Field_Sequence',
                'is_null' => true,
                'editable' => false
            ),
            'country' => array(
                'type' => 'Pluf_DB_Field_Varchar',
                'size' => 64,
                'is_null' => true,
                'editable' => true,
                'readable' => true
            ),
            'province' => array(
                'type' => 'Pluf_DB_Field_Varchar',
                'size' => 64,
                'is_null' => true,
                'editable' => true,
                'readable' => true
            ),
            'city' => array(
                'type' => 'Pluf_DB_Field_Varchar',
                'size' => 64,
                'is_null' => true,
                'editable' => true,
                'readable' => true
            ),
            'address' => array(
                'type' => 'Pluf_DB_Field_Varchar',
                'size' => 512,
                'is_null' => true,
                'editable' => true,
                'readable' => true
            ),
            'postal_code' => array(
                'type' => 'Pluf_DB_Field_Varchar',
                'size' => 16,
                'is_null' => true,
                'editable' => true,
                'readable' => true
            ),
            'location' => array(
                'type' => 'Pluf_DB_Field_Geometry',
                'is_null' => true,
                'editable' => true,
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
                'relate_name' => 'addresses',
                'graphql_name' => 'account',
                'is_null' => false,
                'editable' => false
            )
        );
    }
}