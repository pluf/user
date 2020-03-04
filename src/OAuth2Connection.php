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
 * Content data model
 *
 * @author hadi <mohammad.hadi.mansouri@dpq.co.ir>
 * @author maso <mostafa.barmshory@dpq.co.ir>
 *        
 */
class OAuth2Connection extends Model
{

    /**
     * مدل داده‌ای را بارگذاری می‌کند.
     *
     * @see Model::init()
     */
    function init()
    {
        $this->_a['table'] = 'oauth2_connections';
        $this->_a['cols'] = array(
            // شناسه‌ها
            'id' => array(
                'type' => '\Pluf\DB\Field\Sequence',
                'blank' => false,
                'verbose' => 'first name',
                'help_text' => 'id',
                'editable' => false
            ),
            // فیلدها
            'user_name' => array(
                'type' => '\Pluf\DB\Field\Varchar',
                'blank' => false,
                'size' => 64,
                'unique' => true,
                'verbose' => 'name',
                'help_text' => 'content name',
                'editable' => true
            ),
            'user_id' => array(
                'type' => '\Pluf\DB\Field\Foreignkey',
                'model' => 'User',
                'blank' => false,
                'editable' => false,
                'readable' => true,
                'relate_name' => 'user'
            ),
            'server_id' => array(
                'type' => '\Pluf\DB\Field\Foreignkey',
                'model' => 'OAuth2_Server',
                'blank' => false,
                'editable' => false,
                'readable' => true,
                'relate_name' => 'user'
            ),
        );
        
        $this->_a['idx'] = array(
            'content_server_user_id' => array(
                'col' => 'user_name, user_id, server_id,',
                'type' => 'unique', // normal, unique, fulltext, spatial
                'index_type' => '', // hash, btree
                'index_option' => '',
                'algorithm_option' => '',
                'lock_option' => ''
            )
        );
    }
}