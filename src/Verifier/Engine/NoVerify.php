<?php

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
 *
 * @author hadi
 *        
 */
class Verifier_Engine_NoVerify extends Verifier_Engine
{

    /*
     *
     */
    public function getTitle()
    {
        return 'No Verify';
    }

    /*
     *
     */
    public function getDescription()
    {
        return 'This verifier always says yes to verify an entity.';
    }

    public function verify($verification)
    {
        $obj = null;
        switch ($verification->subject_class) {
            case 'User_Account':
                $obj = Pluf_Shortcuts_GetObjectOr404($verification->subject_class, $verification->subject_id);
                $obj->is_active = true;
                $obj->update();
                break;
        }
        $verification->delete();
        return $obj;
    }

    /*
     *
     */
    public function getExtraParam()
    {
        return array();
    }
}