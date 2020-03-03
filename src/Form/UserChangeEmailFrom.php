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

use Pluf\DB\Field;
use Pluf\Bootstrap;
use Pluf\Form;
use Pluf\FormException;
use Pluf\Crypt;

/**
 * رایانامه یک کاربر را تغییر می‌دهد
 *
 * تغییر رایانامه تنها بر اساس کلیدی انجام می‌شود که در سیستم ایجاد شده است.
 */
class UserChangeEmailFrom extends Form
{

    protected $user;

    public function initFields($extra = array())
    {
        $this->fields['key'] = new Field\Varchar(array(
            'required' => true,
            'label' => __('Your verification key'),
            'initial' => '',
            'widget_attrs' => array(
                'size' => 50
            )
        ));
    }

    /**
     * مقدار کلید را تعیین می‌کند.
     *
     * @return multitype:
     */
    function clean_key()
    {
        self::validateKey($this->cleaned_data['key']);
        return $this->cleaned_data['key'];
    }

    /**
     * کلید کاربر را اعتبار سنجی می‌کند.
     *
     * در صورتی که کلید معتبر نباشد خطای Pluf_Form_Invalid صادر خواهد شد.
     *
     * @param
     *            string Key
     * @return array array($new_email, $user_id, time())
     */
    public static function validateKey($key)
    {
        $hash = substr($key, 0, 2);
        $encrypted = substr($key, 2);
        if ($hash != substr(md5(Bootstrap::f('secret_key') . $encrypted), 0, 2)) {
            throw new FormException('The validation key is not valid. Please copy/paste it from your confirmation email.', $this);
        }
        $cr = new Crypt(md5(Bootstrap::f('secret_key')));
        return explode(':', $cr->decrypt($encrypted), 3);
    }

    /**
     * فرم تغییر ایمیل کاربر را ذخیره می‌کند.
     *
     * @param
     *            bool Commit در صورتی که درستی باشد تغیرهای کاربر ذخیره می‌شود.
     * @return Object Model
     */
    function save($commit = true)
    {
        if (! $this->isValid()) {
            throw new FormException('Cannot save the model from an invalid form.', $this);
        }
        return Bootstrap::f('url_base') . Pluf_HTTP_URL_urlForView('IDF_Views_User::changeEmailDo', array(
            $this->cleaned_data['key']
        ));
    }
}
