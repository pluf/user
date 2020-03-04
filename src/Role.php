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
use Pluf\SQL;
use Pluf\Bootstrap;
use Pluf\ModelUtils;

/**
 *
 * @author maso
 *        
 */
class Role extends Model
{

    private $_cache_to_string;

    function init()
    {
        $this->_a['verbose'] = 'roles';
        $this->_a['table'] = 'user_roles';
        $this->tableName = 'user_roles';
        $this->_a['cols'] = array(
            // It is mandatory to have an "id" column.
            'id' => array(
                'type' => '\Pluf\DB\Field\Sequence',
                // It is automatically added.
                'blank' => true,
                'editable' => false,
                'readable' => true
            ),
            'name' => array(
                'type' => '\Pluf\DB\Field\Varchar',
                'is_null' => false,
                'size' => 50,
                'verbose' => 'name'
            ),
            'description' => array(
                'type' => '\Pluf\DB\Field\Varchar',
                'is_null' => true,
                'size' => 250,
                'verbose' => 'description'
            ),
            'application' => array(
                'type' => '\Pluf\DB\Field\Varchar',
                'size' => 150,
                'is_null' => false,
                'verbose' => 'application',
                'help_text' => 'The application using this permission, for example "YourApp", "CMS" or "SView".'
            ),
            'code_name' => array(
                'type' => '\Pluf\DB\Field\Varchar',
                'is_null' => false,
                'size' => 100,
                'verbose' => 'code name',
                'help_text' => 'The code name must be unique for each application. Standard permissions to manage a model in the interface are "Model_Name-create", "Model_Name-update", "Model_Name-list" and "Model_Name-delete".'
            )
        );
        /*
         * Indeces
         */
        $this->_a['idx'] = array(
            'code_name_idx' => array(
                'type' => 'normal',
                'col' => 'code_name'
            ),
            'application_idx' => array(
                'type' => 'normal',
                'col' => 'application'
            ),
            'perme_idx' => array(
                'col' => 'application, code_name',
                'type' => 'unique', // normal, unique, fulltext, spatial
                'index_type' => '', // hash, btree
                'index_option' => '',
                'algorithm_option' => '',
                'lock_option' => ''
            )
        );
        /*
         * Views
         */
        $g_asso = ModelUtils::getAssocTable(new Group(), $this);
        $u_asso = ModelUtils::getAssocTable(new Account(), $this);
        $t_perm = ModelUtils::getTable($this);
        $role_fk = Shortcuts::getForeignKeyName($this);
        $this->_a['views'] = array(
            'join_group' => array(
                'join' => 'LEFT JOIN ' . $g_asso . ' ON ' . $t_perm . '.id=' . $role_fk
            ),
            'join_user' => array(
                'join' => 'LEFT JOIN ' . $u_asso . ' ON ' . $t_perm . '.id=' . $role_fk
            )
        );
    }

    /**
     * Get the matching permission object from the permission string.
     *
     * @param
     *            string Permission string, for example 'User.create'.
     * @return false|Role The matching permission or false.
     */
    public static function getFromString($perm)
    {
        list ($app, $code) = explode('.', trim($perm));
        $sql = new SQL('code_name=%s AND application=%s', array(
            $code,
            $app
        ));
        $permModel = new Role();
        $perms = $permModel->getList(array(
            'filter' => $sql->gen()
        ));
        if ($perms->count() != 1) { // permission does not exist
            if (Bootstrap::f('user_role_autoCreate', true)) {
                $permModel->code_name = $code;
                $permModel->application = $app;
                if ($permModel->create()) {
                    return $permModel;
                }
            }
            return false;
        }
        return $perms[0];
    }

    /**
     *
     * {@inheritdoc}
     * @see Model::__toString()
     */
    public function __toString()
    {
        if (isset($this->_cache_to_string)) {
            return $this->_cache_to_string;
        }
        $this->_cache_to_string = sprintf('%s.%s', $this->application, $this->code_name);
        return $this->_cache_to_string;
    }
}

