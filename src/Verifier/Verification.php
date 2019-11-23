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
 * Stores credential information of a user.
 */
class Verifier_Verification extends Pluf_Model
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
                'verbose' => 'name',
            ),
            'subject_class' => array(
                'type' => 'Pluf_DB_Field_Varchar',
                'is_null' => false,
                'size' => 64,
            ),
            'subject_id' => array(
                'type' => 'Pluf_DB_Field_Integer',
                'is_null' => false,
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
                'relate_name' => 'account',
                'is_null' => false,
                'editable' => false
            )
        );

        // Assoc. table
        $accountModel = new User_Account();
        $accountTable = $this->_con->pfx . $accountModel->_a['table'];
        $credentialTable = $this->_con->pfx . $this->_a['table'];
        $this->_a['views'] = array(
            'join_credential_account' => array(
                'join' => 'LEFT JOIN ' . $accountTable . ' ON ' . $credentialTable . '.account_id=' . $accountTable . 'id'
            )
        );
    }

    /**
     * Check if code is valid
     *
     * @param string code the code to validate
     * 
     * @return boolean true if code is valid else false
     */
    function validate($code)
    {
        // XXX: hadi 2019: check expiry time
        // XXX: hadi 2019: check user info
        
        // Check the code
        return $this->code === $code;
    }

    /**
     * Returns list of verifications of the given user account
     * @param int $userId
     * @return array
     */
    public static function getVerifications($userId)
    {
        $model = new Verifier_Verification();
        $where = 'account_id = ' . $model->_toDb($userId, 'account_id');
        $creds = $model->getList(array(
            'filter' => $where
        ));
        if ($creds === false) {
            return array();
        }
        return $creds;
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
