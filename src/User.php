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
class User extends Pluf_Model
{

    /**
     * کلد جلسه کاربر را تعیین می‌کند.
     */
    public $session_key = '_PX_User_auth';

    /**
     * Cache of the permissions.
     */
    public $_cache_perms = null;

    function init()
    {
        $langs = Pluf::f('languages', array(
            'en'
        ));
        $this->_a['verbose'] = 'user';
        $this->_a['table'] = 'users';
        $this->_a['cols'] = array(
            // It is mandatory to have an "id" column.
            'id' => array(
                'type' => 'Pluf_DB_Field_Sequence',
                // It is automatically added.
                'blank' => true,
                'editable' => false,
                'readable' => true
            ),
            'version' => array(
                'type' => 'Pluf_DB_Field_Integer',
                'blank' => true,
                'editable' => false,
                'readable' => false
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
            'email' => array(
                'type' => 'Pluf_DB_Field_Email',
                'blank' => false,
                'verbose' => __('email'),
                // @note: hadi, 1395-07-14: change email is done by
                // another process.
                'editable' => false,
                'readable' => true
            ),
            'password' => array(
                'type' => 'Pluf_DB_Field_Password',
                'blank' => false,
                'verbose' => __('password'),
                'size' => 150,
                'help_text' => __('Format: [algo]:[salt]:[hash]'),
                'secure' => true,
                // @note: hadi, 1395-07-14: change password is done by
                // another process.
                'editable' => false,
                'readable' => false
            ),
            'groups' => array(
                'type' => 'Pluf_DB_Field_Manytomany',
                'blank' => true,
                'model' => Pluf::f('pluf_custom_group', 'Pluf_Group'),
                'relate_name' => 'users'
            ),
            'permissions' => array(
                'type' => 'Pluf_DB_Field_Manytomany',
                'blank' => true,
                'model' => 'Pluf_Permission'
            ),
            'active' => array(
                'type' => 'Pluf_DB_Field_Boolean',
                'default' => true,
                'blank' => true,
                'verbose' => __('active'),
                'editable' => false
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
            ),
            'date_joined' => array(
                'type' => 'Pluf_DB_Field_Datetime',
                'blank' => true,
                'verbose' => __('date joined'),
                'editable' => false
            ),
            'last_login' => array(
                'type' => 'Pluf_DB_Field_Datetime',
                'blank' => true,
                'verbose' => __('last login'),
                'editable' => false
            )
        );
        $this->_a['idx'] = array(
            'login_idx' => array(
                'col' => 'login',
                'type' => 'unique'
            )
        );
        $hay = array(
            strtolower(Pluf::f('pluf_custom_group', 'Pluf_Group')),
            strtolower($this->_a['model'])
        );
        sort($hay);
        $t_asso = $this->_con->pfx . $hay[0] . '_' . $hay[1] . '_assoc';
        $t_user = $this->_con->pfx . $this->_a['table'];
        $this->_a['views'] = array(
            'all' => array(
                'select' => $this->getSelect()
            ),
            'secure' => array(
                'select' => $this->getSecureSelect()
            ),
            'user_permission' => array(
                'select' => $this->getSecureSelect(),
                'join' => 'LEFT JOIN rowpermissions ON ' . $t_user . '.id=rowpermissions.owner_id AND rowpermissions.owner_class="' . $this->_a['model'] . '"'
            ),
            'roled_user' => array(
                'select' => $this->getSecureSelect(),
                'join' => 'JOIN (SELECT DISTINCT owner_id, owner_class, tenant FROM rowpermissions) AS B '.
                'ON (' . $t_user . '.id=B.owner_id AND B.owner_class="Pluf_User")'
            ),
            'user_group' => array(
                'join' => 'LEFT JOIN ' . $t_asso . ' ON ' . $t_user . '.id=pluf_user_id'
            )
        );
//         if (Pluf::f('pluf_custom_user', false))
//             $this->extended_init();
    }

    /**
     * تمام داده‌های امنیتی را از فهرست انتخاب حذف می‌کند.
     */
    function getSecureSelect()
    {
        if (isset($this->_cache['getSecureSelect']))
            return $this->_cache['getSecureSelect'];
        $select = array();
        $table = $this->getSqlTable();
        foreach ($this->_a['cols'] as $col => $val) {
            if ($val['type'] != 'Pluf_DB_Field_Manytomany' && ! (array_key_exists('secure', $val) && $val['secure'] == true)) {
                $select[] = $table . '.' . $this->_con->qn($col) . ' AS ' . $this->_con->qn($col);
            }
        }
        $this->_cache['getSecureSelect'] = implode(', ', $select);
        return $this->_cache['getSecureSelect'];
    }

    /**
     * Hook for extended class
     */
    function extended_init()
    {
        return;
    }

    /**
     * نمایش رشته‌ای از یک کاربر
     *
     * این کلاس یک نمایش رشته‌ای از کاربر ایجاد می‌کند.
     */
    function __toString()
    {
        $repr = $this->last_name;
        if (strlen($this->first_name) > 0) {
            $repr = $this->first_name . ' ' . $repr;
        }
        return $repr;
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
         * Pluf_User::preDelete
         *
         * [sender]
         *
         * Pluf_User
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
        Pluf_Signal::send('Pluf_User::preDelete', 'Pluf_User', $params);
        
        if (Pluf::f('pluf_use_rowpermission', false)) {
            $_rpt = Pluf::factory('Pluf_RowPermission')->getSqlTable();
            $sql = new Pluf_SQL('owner_class=%s AND owner_id=%s', array(
                $this->_a['model'],
                $this->_data['id']
            ));
            $this->_con->execute('DELETE FROM ' . $_rpt . ' WHERE ' . $sql->gen());
        }
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
     * @return boolean|ArrayObject
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
     * تمام دسترسی‌هایی که یک کاربر دارد را تعیین می‌کند.
     *
     * این که گواهی مربوط به یک سطر است یا نه به صورت کلی تعیین شده است مهم نیست
     * و تنها وجود گواهی برای کاربر در نظر گرفته می‌شود.
     *
     * گواهی می‌تواند به کاربر و یا به گروه انتصاب داده شده باشد.
     *
     * @param
     *            bool Force the reload of the list of permissions (false)
     * @return array List of permissions
     */
    function getAllPermissions($force = false)
    {
        if ($force == false and ! is_null($this->_cache_perms)) {
            return $this->_cache_perms;
        }
        $this->_cache_perms = array();
        if ($this->isAnonymous()) {
            return $this->_cache_perms;
        }
        // load user permissions
        $this->_cache_perms = (array) $this->get_permissions_list();
        
        // Load groups
        $groups = $this->get_groups_list();
        $ids = array();
        foreach ($groups as $group) {
            $ids[] = $group->id;
        }
        // load groups permisson
        if (count($ids) > 0) {
            $this->loadGroupPermissions($ids);
        }
        // load row permission
        if (Pluf::f('pluf_use_rowpermission', false) and $this->id) {
            $this->loadRowPermissions($ids);
        }
        return $this->_cache_perms;
    }

    /**
     * فهرستی از شی داده شده را برمی‌گرداند که این کاربر دسترسی تعیین شده را روی
     * آن‌ها دارد.
     * به عنوان مثال فراخوانی این متد به صورت
     * getAllPermittedObject('App.manage',
     * new Pluf_Group(), 1)
     * فهرستی از Pluf_Group هایی را برمی‌گرداند که کاربر جاری روی ان‌ها دسترسی
     * 'manage' رو در ملک با
     * شناسه یک دارد.
     *
     * @param Pluf_Model $object
     *            نمونه از شی مورد نظر
     * @param string $permission
     *            رشته حاوی code_name مربوط به گواهی مورد نظر
     */
    function getAllPermittedObject($permission, $object)
    {
        $permPattern = $permission . '#' . $object->_a['model'];
        $permList = $this->getAllPermissions(false);
        $result = array();
        $m = array();
        foreach ($permList as $rowPerm) {
            try {
                preg_match('/^(?P<perm>' . $permPattern . ')\((?P<id>\d+)\)/', $rowPerm, $m);
                $obj = new $object->_a['model']($m['id']);
                array_push($result, $obj);
            } catch (Exception $e) {}
        }
        return $result;
    }

    /*
     * فهرست گروه‌ها را لود می‌کند.
     */
    private function loadGroupPermissions($ids)
    {
        $gperm = new Role();
        $f_name = 'group_id';
        $perms = (array) $gperm->getList(array(
            'filter' => $f_name . ' IN (' . join(', ', $ids) . ')',
            'view' => 'join_group'
        ));
        foreach ($perms as $perm) {
            $tos = $perm->toString();
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
    function hasPerm($perm/* , $obj = null */)
    {
        if (! $this->active)
            return false;
        if ($this->administrator)
            return true;
        $perms = $this->getAllPermissions(false);
//         if (! is_null($obj)) {
//             $perm_row = $perm . '#' . $obj->_a['model'] . '(' . $obj->id . ')';
//             if (in_array('!' . $perm_row, $perms))
//                 return false;
//             if (in_array($perm_row, $perms))
//                 return true;
//         }
        if (in_array($perm, $perms))
            return true;
        return false;
    }

    /**
     * تعیین می‌کند که آیا کاربر یکی از مجوزهای نرم افزار را دارد یا نه.
     *
     * @return bool درستی اگر یکی از مجوزها وجود داشته باشد.
     */
    function hasAppPerms($app)
    {
        if ($this->administrator)
            return true;
        foreach ($this->getAllPermissions() as $perm) {
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
        $m = new Pluf_Message();
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
        if (false == $pclass) {
            throw new Pluf_Exception_SettingError(__('"user_profile_class" setting not defined.'));
        }
        $db = $this->getDbConnection();
        $sql = new Pluf_SQL(sprintf('%s=%%s', $db->qn('user')), array(
            $this->id
        ));
        $users = Pluf::factory($pclass)->getList(array(
            'filter' => $sql->gen()
        ));
        if ($users->count() != 1) {
            throw new Pluf_Exception_DoesNotExist(sprintf(__('No profiles available for user: %s'), (string) $this));
        }
        return $users[0];
    }
}
