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

use Pluf\Form;
use Pluf\Form\Field;
use Pluf\FormException;
use Pluf\Exception;
use Pluf\User\Account;
use Pluf\User\Shortcuts;

// Pluf::loadFunction('Pluf_HTTP_URL_urlForView');
// Pluf::loadFunction('User_Shortcuts_UserDataFactory');

/**
 * Update user info
 *
 * @author maso
 *        
 */
class AccountCrudForms extends Form
{

    public $user_data = null;

    /**
     * مقدار دهی فیلدها.
     *
     * @see Form::initFields()
     */
    public function initFields($extra = array())
    {
        if (array_key_exists('user', $extra)) {
            $this->user_data = $extra['user'];
        }
        $this->user_data = Shortcuts::serDataFactory($this->user_data);

        $this->fields['login'] = new Field\Varchar(array(
            'required' => true,
            'label' => __('login'),
            'initial' => $this->user_data->login
        ));

        $this->fields['is_active'] = new Field\Boolean(array(
            'required' => false,
            'label' => __('active'),
            'initial' => $this->user_data->is_active
        ));
    }

    /**
     * مدل داده‌ای را ذخیره می‌کند
     *
     * مدل داده‌ای را بر اساس تغییرات تعیین شده توسط کاربر به روز می‌کند. در
     * صورتی
     * که پارامتر ورودی با نا درستی مقدار دهی شود تغییراد ذخیره نمی شود در غیر
     * این
     * صورت داده‌ها در پایگاه داده ذخیره می‌شود.
     *
     * @param $commit داده‌ها
     *            ذخیره شود یا نه
     * @return AccountCrudForms مدل داده‌ای ایجاد شده
     */
    function save($commit = true): Account
    {
        if (! $this->isValid()) {
            throw new FormException('Cannot save the model from an invalid form.', $this);
        }
        $this->user_data->setFromFormData($this->cleaned_data);
        if ($commit) {
            if (! $this->user_data->create()) {
                throw new Exception('Fail to create new user.');
            }
        }
        return $this->user_data;
    }

    /**
     * داده‌های کاربر را به روز می‌کند.
     *
     * @throws Exception
     */
    function update($commit = true): Account
    {
        if (! $this->isValid()) {
            throw new FormException('Cannot save the model from an invalid form.', $this);
        }
        $this->user_data->setFromFormData($this->cleaned_data);
        if ($commit) {
            // FIXME: maso, 1394: بررسی صحت رایانامه
            $this->user_data->update();
        }
        return $this->user_data;
    }
}
