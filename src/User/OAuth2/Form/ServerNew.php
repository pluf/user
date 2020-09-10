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
 * The general form to create a new oauth2-server
 *
 * This form determines the parameters used for the authentication servers and 
 * filters the given data based on the parameters. At last, this form creates
 * a new authentication server by using given data.
 *
 * @author hadi <mohammad.hadi.mansouri@dpq.co.ir>
 *        
 */
class User_OAuth2_Form_ServerNew extends Pluf_Form
{

    /**
     * نوع متور پرداخت را تعیین می‌کند.
     *
     * @var string
     */
    var $engine;

    /*
     *
     */
    public function initFields($extra = array())
    {
        $this->engine = $extra['engine'];

        $params = $this->engine->getParameters();
        foreach ($params['children'] as $param) {
            $options = array(
                // 'required' => $param['required']
                'required' => false
            );
            $field = null;
            switch ($param['type']) {
                case 'Integer':
                    $field = new Pluf_Form_Field_Integer($options);
                    break;
                case 'String':
                    $field = new Pluf_Form_Field_Varchar($options);
                    break;
            }
            $this->fields[$param['name']] = $field;
        }
    }

    /**
     * Creates a new authentication server
     *
     * @param string $commit
     * @throws \Pluf\Exception
     * @return User_OAuth2Server
     */
    function save($commit = true)
    {
        if (! $this->isValid()) {
            // TODO: maso, 1395: باید از خطای مدل فرم استفاده شود.
            throw new  \Pluf\Exception('Cannot save the authentication server from an invalid form.');
        }
        // Set attributes
        $server = new User_OAuth2Server();
        $server->setFromFormData($this->cleaned_data);
        $server->engine = $this->engine->getType();
        $params = $this->engine->getParameters();
        foreach ($params['children'] as $param) {
            if ($param['name'] === 'title' || $param['name'] === 'description' || 
                $param['name'] === 'symbol' || $param['name'] === 'client_id' ||
                $param['name'] === 'client_secret')
                continue;
            $server->setMeta($param['name'], $this->cleaned_data[$param['name']]);
        }
        // TODO: maso, 1395: تنها پارامترهایی اضافه باید به صورت کد شده در
        // موجودیت قرار گیرد.
        if ($commit) {
            if (! $server->create()) {
                throw new  \Pluf\Exception('Fail to create the authentication server.');
            }
        }
        return $server;
    }
}

