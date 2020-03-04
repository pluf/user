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
use Pluf\Utils;
use \DateTime;

/**
 * Credential data model
 *
 * Stores credential information of a user.
 */
class Credential extends Model
{

    function init()
    {
        $this->_a['verbose'] = 'credentials';
        $this->_a['table'] = 'user_credentials';
        $this->_a['cols'] = array(
            // It is mandatory to have an "id" column.
            'id' => array(
                'type' => '\Pluf\DB\Field\Sequence',
                // It is automatically added.
                'is_null' => true,
                'editable' => false,
                'readable' => true
            ),
            'password' => array(
                'type' => '\Pluf\DB\Field\Password',
                'blank' => false,
                'size' => 150,
                'help_text' => 'Format: [algo]:[salt]:[hash]',
                'editable' => false,
                'readable' => false
            ),
            'expiry_count' => array(
                'type' => '\Pluf\DB\Field\Integer',
                'editable' => false
            ),
            'expiry_dtime' => array(
                'type' => '\Pluf\DB\Field\Datetime',
                'editable' => false
            ),
            'creation_dtime' => array(
                'type' => '\Pluf\DB\Field\Datetime',
                'is_null' => false,
                'editable' => false
            ),
            'is_deleted' => array(
                'type' => '\Pluf\DB\Field\Boolean',
                'is_null' => false,
                'default' => false,
                'editable' => false
            ),
            // Foreign keys
            'account_id' => array(
                'type' => '\Pluf\DB\Field\Foreignkey',
                'model' => 'User_Account',
                'relate_name' => 'account',
                'is_null' => false,
                'editable' => false
            )
        );

        // Assoc. table
        $accountModel = new Account();
        $accountTable = $this->_con->pfx . $accountModel->_a['table'];
        $credentialTable = $this->_con->pfx . $this->_a['table'];
        $this->_a['views'] = array(
            'join_credential_account' => array(
                'join' => 'LEFT JOIN ' . $accountTable . ' ON ' . $credentialTable . '.account_id=' . $accountTable . 'id'
            )
        );
    }

    /**
     * Set the password of a user.
     *
     * You need to manually save the user to store the password in the
     * database. The supported algorithms are md5, crc32 and sha1,
     * sha1 being the default.
     *
     * @param
     *            string New password
     * @return bool Success
     */
    function setPassword($password)
    {
        // TODO: maso, 2017: check password
        $salt = Utils::getRandomString(5);
        $this->password = 'sha1:' . $salt . ':' . sha1($salt . $password);
        return true;
    }

    /**
     * Check if password is valid
     *
     * Checks if given password is valid. If password is not set yet it returns false.
     *
     * @param
     *            string password
     * @return boolean true if password is valid else false
     */
    function checkPassword($password)
    {
        if ($this->password == '') {
            return false;
        }
        list ($algo, $salt, $hash) = explode(':', $this->password);
        if ($hash == $algo($salt . $password)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns credential for given account id
     * @param int $userId
     * @return boolean|Credential
     */
    public static function getCredential($userId)
    {
        $model = new Credential();
        $where = 'account_id = ' . $model->_toDb($userId, 'account_id');
        $creds = $model->getList(array(
            'filter' => $where
        ));
        if ($creds === false or count($creds) !== 1) {
            return false;
        }
        return $creds[0];
    }
    
    /**
     * Check if the login creditential is valid.
     *
     * @param
     *            string Login
     * @param
     *            string Password
     * @return boolean True if password is correct for given login else returns false
     */
    public static function checkCredential($login, $password)
    {
        $account = Account::getUser($login);
        $credit = new Credential();
        $credit = $credit->getOne("account_id=" . $account->id);
        if(!$credit){
            return false;
        }
        return $credit->checkPassword($password);
    }

    /**
     * Set the last_login and date_joined before creating.
     */
    function preSave($create = false)
    {
        if ($this->id == '') {
            $this->creation_dtime = gmdate('Y-m-d H:i:s');
            $this->expiry_dtime = gmdate('Y-m-d H:i:s', time() + 24 * 60 * 60);
        }
    }

    /**
     * Check if the token is expired
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
