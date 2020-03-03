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

namespace Pluf\User\Form;


use Pluf\Tenant;
use Pluf\DB\Field;
use Pluf\Bootstrap;
use Pluf\Form\FormModelCreate;

/**
 * به روزرسانی یک محتوا
 *
 * با استفاده از این فرم می‌توان اطلاعات یک محتوا را به روزرسانی کرد.
 *
 * @author hadi <mohammad.hadi.mansouri@dpq.co.ir>
 *
 */
class AvatarCrudForm extends FormModelCreate
{

    var $user;

    public function initFields($extra = array())
    {
        $this->user = $extra['user'];
        parent::initFields($extra);

        // if (! is_dir($content->file_path)) {
        // if (false == @mkdir($content->file_path, 0777, true)) {
        // throw new Pluf_Form_Invalid(
        // 'An error occured when creating the upload path. Please try to send
        // the file again.');
        // }
        // }
        $tenant = Tenant::current();
        $path = $tenant->storagePath() . '/avatar';
        $this->fields['file'] = new Field\File(array(
            'required' => true,
            'max_size' => Bootstrap::f('user_avatar_max_size', 2097152),
            'move_function_params' => array(
                'upload_path' => $path,
                'file_name' => $this->user->id,
                'upload_path_create' => true,
                'upload_overwrite' => true
            )
        ));
    }

    /**
     * 
     * {@inheritDoc}
     * @see \Pluf\Form\FormModelCreate::save()
     */
    function save($commit = true)
    {
        $model = parent::save(false);

        // update the content
        {
            // Extract information of file
            $myFile = $this->data['file'];

            $tenant = Tenant::current();

            $model->fileName = $myFile['name'];
            $model->filePath = $tenant->storagePath() . '/avatar';
            $model->account_id = $this->user;
        }

        if ($commit && $model->id) {
            $model->update();
        } elseif ($commit) {
            $model->create();
        }
        return $model;
    }
}
