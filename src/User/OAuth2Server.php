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
class User_OAuth2Server extends Pluf_Model
{

    public $data = array();
    public $touched = false;
    
    /**
     * Load data models
     *
     * @see Pluf_Model::init()
     */
    function init()
    {
        $this->_a['table'] = 'user_oauth2_servers';
        $this->_a['cols'] = array(
            // شناسه‌ها
            'id' => array(
                'type' => Engine::SEQUENCE,
                'is_null' => false,
                'editable' => false
            ),
            'title' => array(
                'type' => Engine::VARCHAR,
                'is_null' => true,
                'size' => 256,
                'default' => 'no title',
                'editable' => true
            ),
            'description' => array(
                'type' => Engine::VARCHAR,
                'is_null' => true,
                'size' => 2048,
                'default' => '',
                'editable' => true
            ),
            'symbol' => array(
                'type' => Engine::VARCHAR,
                'is_null' => true,                
                'size' => 256,
                'default' => ''
            ),
            'client_id' => array(
                'type' => Engine::VARCHAR,
                'is_null' => false,
                'size' => 256,
                'secure' => true,
                'editable' => false,
                'readable' => false
            ),
            'client_secret' => array(
                'type' => Engine::VARCHAR,
                'is_null' => false,
                'size' => 1024,
                'secure' => true,
                'editable' => false,
                'readable' => false
            ),
            'meta' => array(
                'type' => Engine::VARCHAR,
                'is_null' => true,
                'size' => 3000,
                'secure' => true,
                'editable' => false,
                'readable' => false
            ),
            'engine' => array(
                'type' => Engine::VARCHAR,
                'is_null' => false,
                'size' => 64
            ),
            'deleted' => array(
                'type' => Engine::BOOLEAN,
                'is_null' => false,
                'default' => false,
                'readable' => true,
                'editable' => false
            ),
            'creation_dtime' => array(
                'type' => 'Datetime',
                'is_null' => true,
                'verbose' => 'creation date'
            ),
            'modif_dtime' => array(
                'type' => 'Datetime',
                'is_null' => true,
                'verbose' => 'modification date'
            )
        );

        $this->_a['idx'] = array(
            'oauth2server_engine_idx' => array(
                'col' => 'engine',
                'type' => 'normal', // normal, unique, fulltext, spatial
                'index_type' => '', // hash, btree
                'index_option' => '',
                'algorithm_option' => '',
                'lock_option' => ''
            )
        );
    }
    
    /*
     * @see Pluf_Model::preSave()
     */
    function preSave($create = false)
    {
        $this->meta = serialize($this->data);
        if ($this->id == '') {
            $this->creation_dtime = gmdate('Y-m-d H:i:s');
        }
        $this->modif_dtime = gmdate('Y-m-d H:i:s');
    }
    
    /**
     * داده‌های ذخیره شده را بازیابی می‌کند
     *
     * تمام داده‌هایی که با کلید payMeta ذخیره شده را بازیابی می‌کند.
     */
    function restore()
    {
        $this->data = unserialize($this->meta);
    }
    
    /**
     * تمام داده‌های موجود در نشت را پاک می‌کند.
     *
     * تمام داده‌های ذخیره شده در نشست را پاک می‌کند.
     */
    function clear()
    {
        $this->data = array();
        $this->touched = true;
    }
    
    /**
     * تعیین یک داده در نشست
     *
     * با استفاده از این فراخوانی می‌توان یک داده با کلید جدید در نشست ایجاد
     * کرد. این کلید برای دستیابی‌های
     * بعد مورد استفاده قرار خواهد گرفت.
     *
     * @param
     *            کلید داده
     * @param
     *            داده مورد نظر. در صورتی که مقدار آن تهی باشد به معنی
     *            حذف است.
     */
    function setMeta($key, $value = null)
    {
        if (is_null($value)) {
            unset($this->data[$key]);
        } else {
            $this->data[$key] = $value;
        }
        $this->touched = true;
    }
    
    /**
     * داده معادل با کلید تعیین شده را برمی‌گرداند
     *
     * در صورتی که داده تعیین نشده بود مقدار پیش فرض تعیین شده به عنوان نتیجه
     * این فراخوانی
     * برگردانده خواهد شد.
     */
    function getMeta($key = null, $default = '')
    {
        if (is_null($key)) {
            return parent::getData();
        }
        if (isset($this->data[$key])) {
            return $this->data[$key];
        } else {
            return $default;
        }
    }
    
    /**
     *
     * @return User_OAuth2_Engine
     */
    public function get_engine()
    {
        Pluf::loadFunction('User_OAuth2_Shortcuts_GetEngineOr404');
        return User_OAuth2_Shortcuts_GetEngineOr404($this->engine);
    }
    
    public function authenticate($request){
        $engine = $this->get_engine();
        $request->REQUEST['server_id'] = $this->getId();
        return $engine->authenticate($request);
    }
}

