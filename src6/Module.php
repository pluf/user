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

use Pluf;

class Module extends \Pluf\Module
{

    const moduleJsonPath = __DIR__ . '/module.json';

    const relations = array(
        'User_Account' => array(
            'relate_to_many' => array(
                'User_Group',
                'User_Role'
            )
        ),
        'User_Message' => array(
            'relate_to' => array(
                'User_Account'
            )
        ),
        'User_Profile' => array(
            'relate_to' => array(
                'User_Account'
            )
        ),
        'User_Avatar' => array(
            'relate_to' => array(
                'User_Account'
            )
        ),
        'User_Verification' => array(
            'relate_to' => array(
                'User_Account'
            )
        ),
        'User_Email' => array(
            'relate_to' => array(
                'User_Account'
            )
        ),
        'User_Phone' => array(
            'relate_to' => array(
                'User_Account'
            )
        ),
        'User_Address' => array(
            'relate_to' => array(
                'User_Account'
            )
        ),
        'User_Group' => array(
            'relate_to_many' => array(
                'User_Role',
            )
        ),
    );

    const urlsPath = __DIR__ . '/urls.php';

    public function init(Pluf $bootstrap): void
    {}
}

