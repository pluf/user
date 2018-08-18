// <?php
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
Pluf::loadFunction('Pluf_HTTP_URL_urlForView');
Pluf::loadFunction('User_Shortcuts_UserDataFactory');

/**
 * Update user info
 *
 * @author maso
 *        
 */
class User_Form_Account extends Pluf_Form
{

    public $user_data = null;

    /**
     * مقدار دهی فیلدها.
     *
     * @see Pluf_Form::initFields()
     */
    public function initFields($extra = array())
    {
        if (array_key_exists('user', $extra)){
            $this->user_data = $extra['user'];
        }
        $this->user_data = User_Shortcuts_UserDataFactory($this->user_data);

        $this->fields['login'] = new Pluf_Form_Field_Varchar(array(
            'required' => true,
            'label' => __('login'),
            'initial' => $this->user_data->login
        ));

        $this->fields['is_active'] = new Pluf_Form_Field_Boolean(array(
            'required' => false,
            'label' => __('active'),
            'initial' => $this->user_data->active
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
     * @return User_Account مدل داده‌ای ایجاد شده
     */
    function save($commit = true)
    {
        if (! $this->isValid()) {
            throw new Pluf_Exception_Form(__('Cannot save the model from an invalid form.'), $this);
        }
        $this->user_data->setFromFormData($this->cleaned_data);
        $user_active = Pluf::f('user_signup_active', false);
        $this->user_data->active = $user_active;
        if ($commit) {
            if (! $this->user_data->create()) {
                throw new Pluf_Exception(__('Fail to create new user.'));
            }
        }
        return $this->user_data;
    }

    /**
     * داده‌های کاربر را به روز می‌کند.
     *
     * @throws Pluf_Exception
     */
    function update($commit = true)
    {
        if (! $this->isValid()) {
            throw new Pluf_Exception(__('Cannot save the model from an invalid form.'));
        }
        $this->user_data->setFromFormData($this->cleaned_data);
        if ($commit) {
            // FIXME: maso, 1394: بررسی صحت رایانامه
            $this->user_data->update();
        }
        return $this->user_data;
    }

}
