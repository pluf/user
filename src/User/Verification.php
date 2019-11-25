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
 * Verification data model
 *
 */
class User_Verification extends Pluf_Model
{

    function init()
    {
        $this->_a['verbose'] = 'verifications';
        $this->_a['table'] = 'user_verifications';
        $this->_a['cols'] = array(
            'id' => array(
                'type' => 'Pluf_DB_Field_Sequence',
                'is_null' => true,
                'editable' => false,
                'readable' => true
            ),
            'code' => array(
                'type' => 'Pluf_DB_Field_Varchar',
                'is_null' => false,
                'size' => 64,
                'readable' => false
            ),
            'subject_class' => array(
                'type' => 'Pluf_DB_Field_Varchar',
                'is_null' => false,
                'size' => 64,
                'readable' => false
            ),
            'subject_id' => array(
                'type' => 'Pluf_DB_Field_Integer',
                'is_null' => false,
                'readable' => false
            ),
            'expiry_count' => array(
                'type' => 'Pluf_DB_Field_Integer',
                'editable' => false
            ),
            'expiry_dtime' => array(
                'type' => 'Pluf_DB_Field_Datetime',
                'editable' => false
            ),
            'creation_dtime' => array(
                'type' => 'Pluf_DB_Field_Datetime',
                'is_null' => false,
                'editable' => false
            ),
            // Foreign keys
            'account_id' => array(
                'type' => 'Pluf_DB_Field_Foreignkey',
                'model' => 'User_Account',
                'name' => 'account',
                'relate_name' => 'profiles',
                'graphql_name' => 'account',
                'is_null' => false,
                'editable' => false
            )
        );
        
        $this->_a['idx'] = array(
            'verification_code_unique_idx' => array(
                'col' => 'subject_class, subject_id, code',
                'type' => 'unique', // normal, unique, fulltext, spatial
                'index_type' => '', // hash, btree
                'index_option' => '',
                'algorithm_option' => '',
                'lock_option' => ''
            )
        );
    }

    function preSave($create = false)
    {
        if ($this->id == '') {
            $this->creation_dtime = gmdate('Y-m-d H:i:s');
            $this->expiry_dtime = gmdate('Y-m-d H:i:s', time() + 15 * 60);
        }
    }

    /**
     * Check if the verification is expired
     *
     * @return boolean
     */
    public function isExpired()
    {
        $now = new DateTime(gmdate('Y-m-d H:i:s'));
        $expire = new Datetime($this->expiry_dtime);
        return $now >= $expire;
    }
}
