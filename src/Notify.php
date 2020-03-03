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

/**
 * User notification service
 *
 * @author maso
 *        
 */
class Notify
{

    /**
     * Push message for the user
     *
     * @param Account $user
     * @param array $templates
     * @param array $context
     */
    public static function push($user, $templates, $context)
    {
        if (defined('IN_UNIT_TESTS')) {
            return;
        }
        foreach ($templates as $engineName => $template) {
            $engineClass = 'User_Notify_Engine_' . $engineName;
            $engine = new $engineClass();
            $engine->push($user, $template, $context);
        }
    }
}