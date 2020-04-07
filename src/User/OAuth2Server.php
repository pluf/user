<?php

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
 * <ul>
 * <li>client_id</li>
 * <li>client_secret</li>
 * <li>url_authorize</li>
 * <li>url_access_token</li>
 * <li>url_resource_owner_details</li>
 * </ul>
 *
 * @author hadi <mohammad.hadi.mansouri@dpq.co.ir>
 * @author maso <mostafa.barmshory@dpq.co.ir>
 *        
 */
class OAuth2_Server extends Pluf_Model
{

    /**
     * Load data models
     *
     * @see Pluf_Model::init()
     */
    function init()
    {
        $this->_a['table'] = 'oauth2_servers';
        $this->_a['cols'] = array(
            // شناسه‌ها
            'id' => array(
                'type' => 'Sequence',
                'blank' => false,
                'verbose' => 'first name',
                'help_text' => 'id',
                'editable' => false
            ),
            'title' => array(
                'type' => 'Varchar',
                'blank' => true,
                'size' => 250,
                'default' => 'no title',
                'verbose' => 'title',
                'help_text' => 'OAuth2 server title',
                'editable' => true
            ),
            'description' => array(
                'type' => 'Varchar',
                'blank' => true,
                'size' => 2048,
                'default' => 'auto created content',
                'verbose' => 'description',
                'help_text' => 'OAuth2 content description',
                'editable' => true
            ),

            'client_id' => array(
                'type' => 'Varchar',
                'blank' => true,
                'size' => 1024,
                'default' => 'application/octet-stream',
                'verbose' => 'Client ID',
                'help_text' => 'Client ID',
                'editable' => true
            ),
            'client_secret' => array(
                'type' => 'Varchar',
                'blank' => true,
                'size' => 1024,
                'default' => 'application/octet-stream',
                'verbose' => 'Client secret',
                'help_text' => 'content mime type',
                'readable' => true,
                'editable' => true,
            ),
            'url_authorize' => array(
                'type' => 'Varchar',
                'blank' => true,
                'size' => 1024,
                'default' => 'application/octet-stream',
                'verbose' => 'URL authorize',
                'help_text' => 'URL authorize',
                'editable' => true
            ),
            'url_access_token' => array(
                'type' => 'Varchar',
                'blank' => true,
                'size' => 1024,
                'default' => 'application/octet-stream',
                'verbose' => 'URL access token',
                'help_text' => 'URL authorize',
                'editable' => true
            ),
            'url_resource_owner_details' => array(
                'type' => 'Varchar',
                'blank' => true,
                'size' => 1024,
                'default' => 'application/octet-stream',
                'verbose' => 'URL resource owner',
                'help_text' => 'URL resource owner',
                'editable' => true
            ),
        );

        $this->_a['idx'] = array(
            'oauth_server' => array(
                'col' => 'type',
                'type' => 'normal', // normal, unique, fulltext, spatial
                'index_type' => '', // hash, btree
                'index_option' => '',
                'algorithm_option' => '',
                'lock_option' => ''
            )
        );
    }
}