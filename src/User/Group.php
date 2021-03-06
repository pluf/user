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
 * Group
 *
 * @author maso<mostafa.barmshory@dpq.co.ir>
 *        
 * @version 2.3.0 Row permsission i removed from the system model
 */
class User_Group extends Pluf_Model
{

    /**
     * Cache of the permissions.
     */
    public $_cache_perms = null;

    function init()
    {
        $this->_a['verbose'] = 'groups';
        $this->_a['table'] = 'user_groups';
        $this->_a['cols'] = array(
            // It is mandatory to have an "id" column.
            'id' => array(
                'type' => 'Sequence',
                'blank' => true,
                'readable' => true,
                'editable' => false
            ),
            'name' => array(
                'type' => 'Varchar',
                'is_null' => false,
                'size' => 50,
                'verbose' => 'name'
            ),
            'description' => array(
                'type' => 'Varchar',
                'is_null' => true,
                'size' => 250,
                'verbose' => 'description'
            ),
            /*
             * Relations
             */
            'roles' => array(
                'type' => 'Manytomany',
                'model' => 'User_Role',
                'is_null' => true,
                'editable' => false,
                'relate_name' => 'groups',
                'graphql_name' => 'roles'
            )
        );
    }

    public function loadViews(): array
    {
        $engine = $this->getEngine();
        $schema = $engine->getSchema();

        /*
         * Views
         */
        $r_asso = $schema->getRelationTable($this, new User_Role());
        $u_asso = $schema->getRelationTable($this, new User_Account());
        $t_group = $schema->getTableName($this);

        $group_fk = $schema->getAssocField($this);
        return array(
            'join_user' => array(
                'join' => 'LEFT JOIN ' . $u_asso . ' ON ' . $t_group . '.id=' . $group_fk
            ),
            'join_role' => array(
                'join' => 'LEFT JOIN ' . $r_asso . ' ON ' . $t_group . '.id=' . $group_fk
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
        return $this->name;
    }

    /**
     * Gets all roles of a group
     *
     * این که گواهی مربوط به یک سطر است یا نه به صورت کلی تعیین شده است مهم نیست
     * و تنها وجود گواهی برای گروه در نظر گرفته می‌شود.
     *
     * @param
     *            bool Force the reload of the list of permissions (false)
     * @return array List of roles
     */
    function getAllRoles($force = false)
    {
        if ($force == false and ! is_null($this->_cache_perms)) {
            return $this->_cache_perms;
        }
        $this->_cache_perms = array();
        // load group permissions
        $this->_cache_perms = (array) $this->get_roles_list();
        return $this->_cache_perms;
    }

    /**
     * Check role
     *
     * یگ گواهی برای یک مدل خاص است، در اینجا می‌توان تعیین کرد که آیا گروه
     * به شئی مورد نظر این گواهی را دارد.
     *
     * @param
     *            string Permission
     * @param
     *            Object Object for row level permission (null)
     * @return bool درستی اگر گروه گواهی مورد نظر برای شئی را دارد.
     */
    function hasPerm($perm, $obj = null)
    {
        $perms = $this->getAllRoles(false);
        if (in_array($perm, $perms))
            return true;
        return false;
    }

    /**
     * Check an application role
     *
     * @return bool is true if ther is an application role related to the group
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
}
