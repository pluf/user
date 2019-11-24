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
Pluf::loadFunction('User_Shortcuts_GetListCount');

/**
 * Manage users (CRUD on users account)
 */
class User_Views_Account
{

    /**
     * Creates new account (register new user) and a credential for it
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     * @return User_Account created account
     */
    public static function create($request, $match)
    {
        // Create account
        $extra = array();
        $data = array_merge($request->REQUEST, $request->FILES);
        $usr = User_Account::getUser($data['login']);
        if ($usr) {
            throw new Pluf_Exception('Username is existed already.', 400);
        }
        $form = new User_Form_Account($data, $extra);
        $cuser = $form->save();
        // Create credential
        $credit = new User_Credential();
        $credit->setFromFormData(array(
            'account_id' => $cuser->id
        ));
        $credit->setPassword($data['password']);
        $success = $credit->create();
        if (! $success) {
            throw new Pluf_Exception('An internal error is occured while create credential');
        }
        // Verifying account
        $cuser = self::doVerify($cuser);
        return $cuser;
    }

    private static function doVerify($account)
    {
        // Load verifier engine and verify account
        $type = class_exists('Tenant_Service') ? Tenant_Service::setting('account.verifier.engine', 'noverify') : //
        Pluf::f('account.verifier.engine', 'noverify');
        if($type === 'manual'){
            return $account;
        }
        if ($type === 'noverify') {
            $account->is_active = true;
            $account->update();
        } else {
            $engine = Verifier_Service::getEngine($type);
            $verification = Verifier_Service::createVerification($account, $account);
            $success = $engine->send($verification);
            if(!$success){
                throw new Verifier_Exception_VerificationSend();
            }
            // Add verification information to the object to be verified
            $account->verification = $verification;
        }
        return $account;
    }

    /**
     * Updates information of specified user (by id)
     * 
     * This function almost is used to activate or deactivate an account manually.
     * So the user calling this function should has owner permission.
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     * @return User_Account
     */
    public static function update($request, $match)
    {
        $model = Pluf_Shortcuts_GetObjectOr404('User_Account', $match['userId']);
        $model->_a['cols']['is_active']['editable'] = true;
        $form = Pluf_Shortcuts_GetFormForUpdateModel($model, $request->REQUEST, array());
        $request->user->setMessage(sprintf(__('Account data has been updated.'), (string) $model));
        return $form->save();
    }

    /**
     * Delete specified user (by id)
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     */
    public static function delete($request, $match)
    {
        $usr = new User_Account($match['userId']);
        // $usr->delete();
        $usr->setDeleted(true);
        // TODO: Hadi, 1397-05-26: delete credentials and profile
        return $usr;
    }

    public static function activate($request, $match)
    {
        // Check verification code and activate the account
        $account = Pluf_Shortcuts_GetObjectOr404('User_Account', $match['userId']);
        $verification = Verifier_Service::getVerification($account, $account, $match['code']);
        if (! Verifier_Service::validateVerification($verification, $match['code'])) {
            throw new Verifier_Exception_VerificationFailed();
        }
        $account->is_active = true;
        $account->update();
        Verifier_Service::clearVerifications($account);
        return $account;
    }

    public static function verify($request, $match)
    {
        // create verification code
        $account = Pluf_Shortcuts_GetObjectOr404('User_Account', $match['userId']);
        if($account->is_active){
            // Account is activated already
            return $account;
        }
        return self::doVerify($account);
    }

}

