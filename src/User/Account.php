<?php
Pluf::loadFunction('Pluf_Shortcuts_GetAssociationTableName');
Pluf::loadFunction('Pluf_Shortcuts_GetForeignKeyName');

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
 * Account data model
 * 
 * Stores information of an account. An account actually is a user.
 */
class User_Account extends Pluf_Model
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
                'is_null' => true,
                'editable' => false,
                'readable' => true
            ),
            'login' => array(
                'type' => 'Pluf_DB_Field_Varchar',
                'is_null' => false,
                'unique' => true,
                'size' => 50,
                'editable' => false,
                'readable' => true
            ),
            'date_joined' => array(
                'type' => 'Pluf_DB_Field_Datetime',
                'is_null' => true,
                'editable' => false
            ),
            'last_login' => array(
                'type' => 'Pluf_DB_Field_Datetime',
                'is_null' => true,
                'editable' => false
            ),
            'is_active' => array(
                'type' => 'Pluf_DB_Field_Boolean',
                'is_null' => false,
                'default' => false,
                'editable' => false
            ),
            'is_deleted' => array(
                'type' => 'Pluf_DB_Field_Boolean',
                'is_null' => false,
                'default' => false,
                'editable' => false
            ),
            /*
             * Foreign keys 
             */
            'profile_id' => array(
                'type' => 'Pluf_DB_Field_Foreignkey',
                'model' => 'User_Profile',
                'relate_name' => 'profile',
                'is_null' => true,
                'editable' => false
            ),
            /*
             * Relations
             */
            'groups' => array(
                'type' => 'Pluf_DB_Field_Manytomany',
                'blank' => true,
                'model' => 'User_Group',
                'relate_name' => 'groups',
                'editable' => false,
                'readable' => false
            ),
            'roles' => array(
                'type' => 'Pluf_DB_Field_Manytomany',
                'blank' => true,
                'relate_name' => 'roles',
                'editable' => false,
                'readable' => false,
                'model' => 'User_Role'
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
        $g_asso = $this->_con->pfx . Pluf_Shortcuts_GetAssociationTableName('User_Account', 'User_Group');
        $r_asso = $this->_con->pfx . Pluf_Shortcuts_GetAssociationTableName('User_Account', 'User_Role');
        $t_user = $this->_con->pfx . $this->_a['table'];
        $user_fk = Pluf_Shortcuts_GetForeignKeyName('User_Account');
        $this->_a['views'] = array(
            'join_role' => array(
                'join' => 'LEFT JOIN ' . $r_asso . ' ON ' . $t_user . '.id=' . $user_fk
            ),
            'join_group' => array(
                'join' => 'LEFT JOIN ' . $g_asso . ' ON ' . $t_user . '.id=' . $user_fk
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
        Pluf_Signal::send('User_Account::preDelete', 'User_Account', $params);

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
     * Extract information of user and returns it.
     *
     * @param string $login
     * @return User_Account user information
     */
    public static function getUser($login)
    {
        $model = new User_Account();
        $where = 'login = ' . $model->_toDb($login, 'login');
        $users = $model->getList(array(
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
            if(Pluf::f('account_force_activate', false)){
                $this->is_active = false;
            }
            $this->is_active = $this->is_active && true;
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
        $gperm = new User_Role();
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
        if (! $this->isActive()) {
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

    /**
     * Checks if account is active
     * @return boolean true if account is active else false
     */
    function isActive(){
        return $this->is_active;
    }

    function setDeleted($deleted){
        $this->_data['is_deleted'] = $deleted;
        $this->update();
    }
}
