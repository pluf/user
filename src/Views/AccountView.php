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
namespace Pluf\User\Views;

use Pluf\HTTP\Request;
use Pluf\User\AccountView;
use Pluf\Exception;
use Pluf\User\Form\AccountCrudForms;
use Pluf\User\Credential;
use Pluf\User\Profile;
use Pluf\ModelUtils;
use Pluf\User\Email;
use Pluf\User\Phone;
use Pluf\User\Address;
use Pluf\Bootstrap;
use Pluf\User\Verifier\Service;
use Pluf\User\Verifier\EngineLoadException;
use Pluf\User\Verifier\VerificationSendException;
use Pluf\User\Verifier\VerificationFailedException;

// Pluf::loadFunction('Pluf_HTTP_URL_urlForView');
// Pluf::loadFunction('Pluf_Shortcuts_GetObjectOr404');
// Pluf::loadFunction('Pluf_Shortcuts_GetFormForModel');
// Pluf::loadFunction('User_Shortcuts_GetListCount');

/**
 * Manage users (CRUD on users account)
 */
class User_Views_Account
{

    /**
     * Creates new account (register new user) and a credential for it
     *
     * @param Request $request
     * @param array $match
     * @return AccountView created account
     */
    public static function create(Request $request, $match)
    {
        // Create account
        $extra = array();
        $data = array_merge($request->REQUEST, $request->FILES);
        $usr = AccountView::getUser($data['login']);
        if ($usr) {
            throw new Exception('Username is existed already.', 400);
        }
        $form = new AccountCrudForms($data, $extra);
        $cuser = $form->save();
        // Create credential
        $credit = new Credential();
        $credit->setFromFormData(array(
            'account_id' => $cuser->id
        ));
        $credit->setPassword($data['password']);
        $success = $credit->create();
        if (! $success) {
            throw new Exception('An internal error is occured while create credential');
        }
        // XXX: hadi, 1398: create email,phone and address entities if are existed
        // Create profile
        $profile = new Profile();
        $form = ModelUtils::getCreateForm($profile, $data);
        $profile = $form->save(false);
        $profile->account_id = $cuser;
        $profile->create();
        $cuser->profile = $profile;
        // Create email
        if (array_key_exists('email', $data)) {
            $email = new Email();
            $email->_a['cols']['email']['editable'] = true;
            $form = ModelUtils::getCreateForm($email, $data);
            $email = $form->save(false);
            $email->account_id = $cuser;
            $email->create();
            $cuser->email = $email;
        }
        // Create phone
        if (array_key_exists('phone', $data)) {
            $phone = new Phone();
            $phone->_a['cols']['phone']['editable'] = true;
            $form = ModelUtils::getCreateForm($phone, $data);
            $phone = $form->save(false);
            $phone->account_id = $cuser;
            $phone->create();
            $cuser->phone = $phone;
        }
        // Create address
        if (array_key_exists('country', $data) || array_key_exists('province', $data) || array_key_exists('city', $data) || array_key_exists('address', $data) || array_key_exists('address', $data) || array_key_exists('location', $data) || array_key_exists('postal_code', $data)) {
            $adr = new Address();
            $form = ModelUtils::getCreateForm($adr, $data);
            $adr = $form->save(false);
            $adr->account_id = $cuser;
            $adr->create();
            $cuser->address = $adr;
        }
        // Verifying account
        $cuser = self::doVerify($cuser);
        // Add all data to returning object
        return $cuser;
    }

    private static function doVerify($account)
    {
        // Load verifier engine and verify account
        $type = class_exists('Tenant_Service') ? Tenant_Service::setting('account.verifier.engine', 'noverify') : //
        Bootstrap::f('account.verifier.engine', 'noverify');
        if ($type === 'manual') {
            return $account;
        }
        if ($type === 'noverify') {
            $account->is_active = true;
            $account->update();
        } else {
            $engine = Service::getEngine($type);
            if (! $engine) {
                throw new EngineLoadException('Defined verifier engine does not exist.');
            }
            $verification = Service::createVerification($account, $account);
            $engineResponse = $engine->send($verification);
            if (! $engineResponse) {
                throw new VerificationSendException();
            }
            // Add verification information to the object to be verified
            $account->verification = $verification;
            // Add response of the engine to the objeect to be verified
            $account->verifier_response = $engineResponse;
        }
        return $account;
    }

    /**
     * Updates information of specified user (by id)
     *
     * This function almost is used to activate or deactivate an account manually.
     * So the user calling this function should has owner permission.
     *
     * @param Request $request
     * @param array $match
     * @return AccountView
     */
    public static function update(Request $request, $match)
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
     * @param Request $request
     * @param array $match
     */
    public static function delete($request, $match)
    {
        $usr = new AccountView($match['userId']);
        // $usr->delete();
        $usr->setDeleted(true);
        // TODO: Hadi, 1397-05-26: delete credentials and profile
        return $usr;
    }

    public static function activate($request, $match)
    {
        // Check verification code and activate the account
        $account = Pluf_Shortcuts_GetObjectOr404('User_Account', $match['userId']);
        $verification = Service::getVerification($account, $account, $match['code']);
        if (! Service::validateVerification($verification, $match['code'])) {
            throw new VerificationFailedException();
        }
        $account->is_active = true;
        $account->update();
        /Service::clearVerifications($account);
        return $account;
    }

    public static function verify($request, $match)
    {
        // create verification code
        $account = Pluf_Shortcuts_GetObjectOr404('User_Account', $match['userId']);
        if ($account->is_active) {
            // Account is activated already
            return $account;
        }
        return self::doVerify($account);
    }
}

