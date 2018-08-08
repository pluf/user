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
class Account extends Pluf_Model
{

    /**
     * Cache of the Role.
     */
    public $_cache_perms = null;

    function init()
    {
        $this->_a['verbose'] = 'accounts';
        $this->_a['table'] = 'user_accounts';
        $this->_a['cols'] = array(
            // It is mandatory to have an "id" column.
            'id' => array(
                'type' => 'Pluf_DB_Field_Sequence',
                // It is automatically added.
                'blank' => true,
                'editable' => false,
                'readable' => true
            ),
            'login' => array(
                'type' => 'Pluf_DB_Field_Varchar',
                'blank' => false,
                'unique' => true,
                'size' => 50,
                'verbose' => __('login'),
                'editable' => false,
                'readable' => true
            ),
            'groups' => array(
                'type' => 'Pluf_DB_Field_Manytomany',
                'blank' => true,
                'model' => 'Group',
                'relate_name' => 'users',
                'editable' => false,
                'readable' => false
            ),
            'roles' => array(
                'type' => 'Pluf_DB_Field_Manytomany',
                'blank' => true,
                'relate_name' => 'users',
                'editable' => false,
                'readable' => false,
                'model' => 'Role'
            ),
            'active' => array(
                'type' => 'Pluf_DB_Field_Boolean',
                'default' => true,
                'blank' => true,
                'verbose' => 'Is the account active',
                'editable' => false
            ),
            'deleted' => array(
                'type' => 'Pluf_DB_Field_Boolean',
                'default' => false,
                'blank' => true,
                'verbose' => 'Id the account deleted',
                'editable' => false
            ),
            'date_joined' => array(
                'type' => 'Pluf_DB_Field_Datetime',
                'blank' => true,
                'verbose' => 'date joined',
                'editable' => false
            ),
            'last_login' => array(
                'type' => 'Pluf_DB_Field_Datetime',
                'blank' => true,
                'verbose' => 'last login',
                'editable' => false
            )
        );
        // @Note: hadi - 1396-10: when define an attribute as 'unique => true', pluf automatically
        // create an unique index for it (for example login field here). So following codes are extra.
        // $this->_a['idx'] = array(
        // 'login_idx' => array(
        // 'col' => 'login',
        // 'type' => 'unique'
        // )
        // );
        // Assoc. table
        $g_asso = $this->_con->pfx . 'group_user_assoc';
        $r_asso = $this->_con->pfx . 'role_user_assoc';
        $t_user = $this->_con->pfx . $this->_a['table'];
        $this->_a['views'] = array(
            'join_role' => array(
                'join' => 'LEFT JOIN ' . $r_asso . ' ON ' . $t_user . '.id=user_id'
            ),
            'join_group' => array(
                'join' => 'LEFT JOIN ' . $g_asso . ' ON ' . $t_user . '.id=user_id'
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
        return $this->login;
    }

    /**
     * فراخوانی‌های پیش از حذف کاربر
     *
     * پیش از این که کاربر حذف شود یک سیگنال به کل سیستم ارسال شده و حذف کاربر
     * گزارش می‌شود.
     */
    function preDelete()
    {
        /**
         * [signal]
         *
         * User::preDelete
         *
         * [sender]
         *
         * User
         *
         * [description]
         *
         * This signal allows an application to perform special
         * operations at the deletion of a user.
         *
         * [parameters]
         *
         * array('user' => $user)
         */
        $params = array(
            'user' => $this
        );
        Pluf_Signal::send('User::preDelete', 'User', $params);

        // if (Pluf::f('pluf_use_rowpermission', false)) {
        // $_rpt = Pluf::factory('Pluf_RowPermission')->getSqlTable();
        // $sql = new Pluf_SQL('owner_class=%s AND owner_id=%s', array(
        // $this->_a['model'],
        // $this->_data['id']
        // ));
        // $this->_con->execute('DELETE FROM ' . $_rpt . ' WHERE ' . $sql->gen());
        // }
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
        $salt = Pluf_Utils::getRandomString(5);
        $this->password = 'sha1:' . $salt . ':' . sha1($salt . $password);
        return true;
    }

    /**
     * تعیین صحت گذرواژه کاربر
     *
     * در صورتی که گذرواژه کاربر تعیین شود، این متد بررسی می‌کن که آیا مقدار
     * درستی برای
     * آن تعیین شده است یا نه.
     *
     * @param
     *            string گذرواژه
     * @return bool مقدار درستی در صورت موفقیت
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
     * Check if the login creditentials are valid.
     *
     * @param
     *            string Login
     * @param
     *            string Password
     * @return mixed False or matching user
     */
    function checkCreditentials($login, $password)
    {
        $where = 'login = ' . $this->_toDb($login, 'login');
        $users = $this->getList(array(
            'filter' => $where
        ));
        if ($users === false or count($users) !== 1) {
            return false;
        }
        if ($users[0]->active and $users[0]->checkPassword($password)) {
            return $users[0];
        }
        return false;
    }

    /**
     * خصوصیت‌های کاربر را استخراج کرده و در اختیار قرار می دهد.
     *
     * @param string $login
     * @return Account
     */
    function getUser($login)
    {
        $where = 'login = ' . $this->_toDb($login, 'login');
        $users = $this->getList(array(
            'filter' => $where
        ));
        if ($users === false or count($users) !== 1) {
            return false;
        }
        return $users[0];
    }

    /**
     * Set the last_login and date_joined before creating.
     */
    function preSave($create = false)
    {
        if (! ($this->id > 0)) {
            $this->last_login = gmdate('Y-m-d H:i:s');
            $this->date_joined = gmdate('Y-m-d H:i:s');
        }
    }

    /**
     * Gets all user roles
     *
     *
     * @param
     *            bool Force the reload of the list of roles (false)
     * @return array List of roles
     */
    function getAllRoles($force = false)
    {
        if ($force == false and ! is_null($this->_cache_perms)) {
            return $this->_cache_perms;
        }
        $this->_cache_perms = array();
        if ($this->isAnonymous()) {
            return $this->_cache_perms;
        }
        // load user permissions
        $this->_cache_perms = (array) $this->get_roles_list();

        // Load groups
        $groups = $this->get_groups_list();
        $ids = array();
        foreach ($groups as $group) {
            $ids[] = $group->id;
        }
        // load groups permisson
        if (count($ids) > 0) {
            $this->loadGroupRoles($ids);
        }
        return $this->_cache_perms;
    }

    /**
     * Gets list of roles of groups
     *
     * @param array $ids
     *            is list of group ids
     */
    private function loadGroupRoles($ids)
    {
        $gperm = new Role();
        $roles = (array) $gperm->getList(array(
            'filter' => 'group_id IN (' . join(', ', $ids) . ')',
            'view' => 'join_group'
        ));
        foreach ($roles as $role) {
            $tos = $role->__toString();
            if (! in_array($tos, $this->_cache_perms)) {
                $this->_cache_perms[] = $tos;
            }
        }
    }

    /**
     * تعیین گواهی برای شئی تعیین شده
     *
     * یگ گواهی برای یک مدل خاص است، در اینجا می‌توان تعیین کرد که آیا کاربر
     * به شئی مورد نظر این گواهی را دارد.
     *
     * <ul>
     * <li>کاربر باید در سیستم فعال باشد</li>
     * <li>کاربر مدیر تمام دسترسی‌ها را دارد</li>
     * <li></li>
     * </ul>
     *
     * @param
     *            string Permission
     * @return bool true if user has the permission
     */
    function hasPerm($perm)
    {
        if (! $this->active) {
            return false;
        }
        $perms = $this->getAllRoles(false);
        return in_array($perm, $perms);
    }

    /**
     * تعیین می‌کند که آیا کاربر یکی از مجوزهای نرم افزار را دارد یا نه.
     *
     * @return bool درستی اگر یکی از مجوزها وجود داشته باشد.
     */
    function hasAppPerms($app)
    {
        foreach ($this->getAllRoles() as $perm) {
            if (0 === strpos($perm, $app . '.')) {
                return true;
            }
        }
        return false;
    }

    /**
     * تعیین پیام برای کاربر
     *
     * یک پیام جدید را ایجاد کرده و به کاربر اضافه می‌کند. در صورتی که کاربر در
     * سیستم ایجاد نشده باشد یک خطا صادر خواهد شد.
     *
     * @param
     *            string Message
     * @return bool Success
     */
    function setMessage($message)
    {
        if ($this->isAnonymous()) {
            throw new Pluf_Exception_DoesNotExist(__("User not exist, while you are trying to add message?!"));
        }
        $m = new User_Message();
        $m->user = $this;
        $m->message = $message;
        if (! $m->create()) {
            throw new Pluf_Exception(__("not possible to create a message"));
        }
        return $m;
    }

    /**
     * پروفایل کاربر را تعیین می‌کند.
     *
     * Retrieve the profile of the current user. If not profile in the
     * database a Pluf_Exception_DoesNotExist exception is thrown,
     * just catch it and create a profile.
     *
     * @return Pluf_Model User profile
     */
    function getProfile()
    {
        $pclass = Pluf::f('user_profile_class', false);
        if (false === $pclass) {
            throw new Pluf_Exception_SettingError('"user_profile_class" setting not defined.');
        }
        $db = $this->getDbConnection();
        $sql = new Pluf_SQL(sprintf('%s=%%s', $db->qn('user')), array(
            $this->id
        ));
        $users = Pluf::factory($pclass)->getList(array(
            'filter' => $sql->gen()
        ));
        if ($users->count() != 1) {
            throw new Pluf_Exception_DoesNotExist(sprintf('No profiles available for user: %s', (string) $this));
        }
        return $users[0];
    }
}
