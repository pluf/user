<?php

/**
 * Security token
 * 
 * A security token to determine login state of user and to use as change password without knowing old password.
 * 
 * @author maso <mostafa.barmshory@dpq.co.ir>
 * @author hadi <mohammad.hadi.mansouri@dpq.co.ir>
 *
 */
class User_Token extends Pluf_Model
{

    /**
     *
     * {@inheritdoc}
     * @see Pluf_Model::init()
     */
    function init()
    {
        $this->_a['verbose'] = 'tokens';
        $this->_a['table'] = 'user_tokens';
        $this->_a['cols'] = array(
            'id' => array(
                'type' => 'Sequence',
                'is_null' => false,
                'editable' => false,
                'readable' => true
            ),
            'token' => array(
                'type' => 'Varchar',
                'is_null' => false,
                'size' => 150,
                'unique' => true,
                'editable' => false
            ),
            'agent' => array(
                'type' => 'Varchar',
                'size' => 100,
                'editable' => false
            ),
            'agent_address' => array(
                'type' => 'Varchar',
                'size' => 250,
                'editable' => false
            ),
            'type' => array(
                'type' => 'Varchar',
                'size' => 50,
                'is_null' => false,
                'editable' => false
            ),
            'expiry_count' => array(
                'type' => 'Integer',
                'editable' => false
            ),
            'expiry_dtime' => array(
                'type' => 'Datetime',
                'editable' => false
            ),
            'creation_dtime' => array(
                'type' => 'Datetime',
                'is_null' => false,
                'editable' => false
            ),
            'is_deleted' => array(
                'type' => 'Boolean',
                'is_null' => false,
                'default' => false,
                'editable' => false
            ),
            // Foreign keys
            'account_id' => array(
                'type' => 'Foreignkey',
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
     * @see Pluf_Model::preSave()
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
