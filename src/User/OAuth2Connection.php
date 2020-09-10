<?php
use Pluf\Db\Engine;

/*
 * This file is part of Pluf Framework, a simple PHP Application Framework.
 * Copyright (C) 2010-2020 Phoinex Scholars Co. (http://dpq.co.ir)
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
 * Content data model
 *
 * @author hadi <mohammad.hadi.mansouri@dpq.co.ir>
 * @author maso <mostafa.barmshory@dpq.co.ir>
 *        
 */
class User_OAuth2Connection extends Pluf_Model
{

    /**
     * مدل داده‌ای را بارگذاری می‌کند.
     *
     * @see Pluf_Model::init()
     */
    function init()
    {
        $this->_a['table'] = 'user_oauth2_connections';
        $this->_a['cols'] = array(
            // شناسه‌ها
            'id' => array(
                'type' => Engine::SEQUENCE,
                'is_null' => false,
                'editable' => false,
                'readable' => true
            ),
            // فیلدها
            'username' => array(
                // Note: the username on the oauth2 server
                'type' => Engine::VARCHAR,
                'is_null' => false,
                'size' => 128,
                'editable' => true
            ),
            'account_id' => array(
                'type' => 'Foreignkey',
                'model' => 'User_Account',
                'name' => 'account',
                'relate_name' => 'oauth2_connections',
                'graphql_name' => 'account',
                'is_null' => false,
                'editable' => false
            ),
            'server_id' => array(
                'type' => 'Foreignkey',
                'model' => 'User_OAuth2Server',
                'name' => 'server',
                'relate_name' => 'connections',
                'graphql_name' => 'server',
                'is_null' => false,
                'editable' => false
            ),
        );
        
        $this->_a['idx'] = array(
            'oauth2connection_account_server_idx' => array(
                'col' => 'account_id, server_id',
                'type' => 'unique', // normal, unique, fulltext, spatial
                'index_type' => '', // hash, btree
                'index_option' => '',
                'algorithm_option' => '',
                'lock_option' => ''
            ),
            'oauth2connection_username_server_idx' => array(
                'col' => 'username, server_id',
                'type' => 'unique', // normal, unique, fulltext, spatial
                'index_type' => '', // hash, btree
                'index_option' => '',
                'algorithm_option' => '',
                'lock_option' => ''
            )
        );
    }
}