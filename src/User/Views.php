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
Pluf::loadFunction('Pluf_HTTP_URL_urlForView');
Pluf::loadFunction('Pluf_Shortcuts_GetObjectOr404');
Pluf::loadFunction('Pluf_Shortcuts_GetFormForModel');
Pluf::loadFunction('Pluf_Shortcuts_GetFormForUpdateModel');
Pluf::loadFunction('User_Shortcuts_GetAvatar');
Pluf::loadFunction('User_Shortcuts_DeleteAvatar');
Pluf::loadFunction('User_Shortcuts_UpdateAvatar');

/**
 *
 * @author maso<mostafa.barmshory@dpq.co.ir>
 * @author hadi<mohammad.hadi.mansouri@dpq.co.ir>
 * @since 0.1.0
 */
class User_Views
{

    /**
     * Retruns account information of current user
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     */
    public function getAccount($request, $match)
    {
        return new Pluf_HTTP_Response_Json($request->user);
    }

    /**
     * Updates account information of current user
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     */
    public function updateAccount($request, $match)
    {
        $model = Pluf_Shortcuts_GetObjectOr404('Pluf_User', $request->user->id);
        $form = Pluf_Shortcuts_GetFormForUpdateModel($model, $request->REQUEST, array());
        return new Pluf_HTTP_Response_Json($form->save());
    }

    /**
     * Delete account of current user.
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     */
    public function deleteAccount($request, $match)
    {
        $user = Pluf_Shortcuts_GetObjectOr404('Pluf_User', $request->user->id);
        $request->user->delete();
        return new Pluf_HTTP_Response_Json($user);
    }
    
    
    /**
     * Log the user in.
     *
     * The login form is provided by the login_form.html template.
     * The '_redirect_after' hidden value is used to redirect the user
     * after successfull login. If the view is called with
     * _redirect_after set in the query as a GET variable it will be
     * available as $_redirect_after in the template.
     *
     * @param
     *            Request Request object
     * @param
     *            array Match
     * @param
     *            string Default redirect URL after login ('/')
     * @param
     *            array Extra context values (array()).
     * @param
     *            string Login form template ('login_form.html')
     * @return Pluf_HTTP_Response object
     */
    function login($request, $match, $success_url = '/', $extra_context = array(), $template = 'login_form.html')
    {
        if (! empty($request->REQUEST['_redirect_after'])) {
            $success_url = $request->REQUEST['_redirect_after'];
        }
        $error = '';
        if ($request->method == 'POST') {
            foreach (Pluf::f('auth_backends', array(
                'Pluf_Auth_ModelBackend'
            )) as $backend) {
                $user = call_user_func(array(
                    $backend,
                    'authenticate'
                ), $request->POST);
                if ($user !== false) {
                    break;
                }
            }
            if (false === $user) {
                $error = __('The login or the password is not valid. The login and the password are case sensitive.');
            } else {
                if (! $request->session->getTestCookie()) {
                    $error = __('You need to enable the cookies in your browser to access this website.');
                } else {
                    $request->user = $user;
                    $request->session->clear();
                    $request->session->setData('login_time', gmdate('Y-m-d H:i:s'));
                    $user->last_login = gmdate('Y-m-d H:i:s');
                    $user->update();
                    $request->session->deleteTestCookie();
                    return new Pluf_HTTP_Response_Redirect($success_url);
                }
            }
        }
        // Show the login form
        $request->session->createTestCookie();
        $context = new Pluf_Template_Context_Request($request, array_merge(array(
            'page_title' => __('Sign In'),
            '_redirect_after' => $success_url,
            'error' => $error
        ), $extra_context));
        $tmpl = new Pluf_Template($template);
        return new Pluf_HTTP_Response($tmpl->render($context));
    }
    
    /**
     * Logout the user.
     *
     * The success url is either an absolute url starting with
     * http(s):// or considered as an action.
     *
     * @param
     *            Request Request object
     * @param
     *            array Match
     * @param
     *            string Default redirect URL after login '/'
     * @return Pluf_HTTP_Response object
     */
    function logout($request, $match, $success_url = '/')
    {
        $user_model = Pluf::f('pluf_custom_user', 'Pluf_User');
        $request->user = new $user_model();
        $request->session->clear();
        $request->session->setData('logout_time', gmdate('Y-m-d H:i:s'));
        if (0 !== strpos($success_url, 'http')) {
            $murl = new Pluf_HTTP_URL();
            $success_url = Pluf::f('app_base') . $murl->generate($success_url);
        }
        return new Pluf_HTTP_Response_Redirect($success_url);
    }
    

}
