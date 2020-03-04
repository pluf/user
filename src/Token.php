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
use \DateTime;

/**
 * Security token
 * 
 * A security token to determine login state of user and to use as change password without knowing old password.
 * 
 * @author maso <mostafa.barmshory@dpq.co.ir>
 * @author hadi <mohammad.hadi.mansouri@dpq.co.ir>
 *
 */
class Token extends Model
{

    /**
     *
     * {@inheritdoc}
     * @see Model::init()
     */
    function init()
    {
        $this->_a['verbose'] = 'tokens';
        $this->_a['table'] = 'user_tokens';
        $this->_a['cols'] = array(
            'id' => array(
                'type' => '\Pluf\DB\Field\Sequence',
                'is_null' => false,
                'editable' => false,
                'readable' => true
            ),
            'token' => array(
                'type' => '\Pluf\DB\Field\Varchar',
                'is_null' => false,
                'size' => 150,
                'unique' => true,
                'editable' => false
            ),
            'agent' => array(
                'type' => '\Pluf\DB\Field\Varchar',
                'size' => 100,
                'editable' => false
            ),
            'agent_address' => array(
                'type' => '\Pluf\DB\Field\Varchar',
                'size' => 250,
                'editable' => false
            ),
            'type' => array(
                'type' => '\Pluf\DB\Field\Varchar',
                'size' => 50,
                'is_null' => false,
                'editable' => false
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
                'name' => 'account',
                'graphql_name' => 'account',
                'relate_name' => 'tokens',
                'is_null' => false,
                'editable' => false
            )
        );
        
//         $this->_a['idx'] = array(
//             'token_idx' => array(
//                 'col' => 'token',
//                 'type' => 'unique'
//             )
//         );
    }

    /**
     *
     * {@inheritdoc}
     * @see Model::preSave()
     */
    function preSave($create = false)
    {
        if ($this->id == '') {
            $this->creation_dtime = gmdate('Y-m-d H:i:s');
            $this->expiry_dtime = gmdate('Y-m-d H:i:s', time() + 24 * 60 * 60);
        }
        if($this->token == ''){
            $this->token = chunk_split(substr(md5(time() . rand(10000, 99999)), 0, 20), 6, '');
        }
    }
    
    /**
     * Check if the token is expired
     * 
     * @return boolean
     */
    public  function isExpired(){
        $now = new DateTime(gmdate('Y-m-d H:i:s'));
        $expire = new Datetime($this->expiry_dtime);
        return $now >= $expire;
    }
}
