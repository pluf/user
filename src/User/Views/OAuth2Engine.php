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
Pluf::loadFunction('User_OAuth2_Shortcuts_GetEngineOr404');

/**
 *
 * @author hadi <mohammad.hadi.mansouri@dpq.co.ir>
 *        
 */
class User_Views_OAuth2Engine
{

    /**
     *
     * @param Pluf_HTTP_Request $request            
     * @param array $match            
     */
    public function find ($request, $match)
    {
        $items = User_OAuth2_Service::engines();
        $page = array(
                'items' => $items,
                'counts' => count($items),
                'current_page' => 0,
                'items_per_page' => count($items),
                'page_number' => 1
        );
        return $page;
    }

    /**
     *
     * پارامترهایی که در این نمایش به عنوان ورودی در نظر گرفته می‌شوند عبارتند
     * از:
     *
     * <ul>
     * <li>type: نوع متور جستجو را تعیین می‌کند</li>
     * </ul>
     *
     * @param Pluf_HTTP_Request $request            
     * @param array $match            
     */
    public function get ($request, $match)
    {
        $engine = User_OAuth2_Shortcuts_GetEngineOr404($match['type']);
        return $engine->getParameters();
    }
}
