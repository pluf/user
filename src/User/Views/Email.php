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
Pluf::loadFunction('Pluf_Shortcuts_GetObjectOr404');

/**
 * Manage emails of account
 */
class User_Views_Email extends Pluf_Views
{

    /**
     * Creates new email for account
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     * @return User_Email created account
     */
    public static function create($request, $match)
    {
        $user = self::checkAccess($request, $match);
        // Create email
        $data = $request->REQUEST;
        if(!Pluf_Utils::isValidEmail($data['email'])){
            throw new Pluf_Exception_BadRequest('Email is not a valid email address.');
        }
        $email = new User_Email();
        $email->_a['cols']['email']['editable'] = true;
        $form = Pluf_ModelUtils::getCreateForm($email, $data);
        $email = $form->save(false);
        $email->account_id = $user;
        $email->create();
        // Verifying email
        $email = self::doVerify($email);
        return $email;
    }

    /**
     * Returns specified email of an account
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     * @return User_Email
     */
    public function get($request, $match)
    {
        $usr = self::checkAccess($request, $match);
        $match['parentId'] = $usr->id;
        $match['modelId'] = $match['emailId'];
        $p = array(
            'parent' => 'User_Account',
            'parentKey' => 'account_id',
            'model' => 'User_Email'
        );
        return $this->getManyToOne($request, $match, $p);
    }
    
    /**
     * Lists emails of an account
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     * @return Pluf_Paginator
     */
    public function find($request, $match)
    {
        $usr = self::checkAccess($request, $match);
        $match['parentId'] = $usr->id;
        $p = array(
            'parent' => 'User_Account',
            'parentKey' => 'account_id',
            'model' => 'User_Email'
        );
        return $this->findManyToOne($request, $match, $p);
    }
    
    /**
     * Updates information of the email of an account.
     *
     * Note that the email address could not be changed by updating process.
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     * @return User_Email
     */
    public function update($request, $match)
    {
        $usr = self::checkAccess($request, $match);
        $match['parentId'] = $usr->id;
        $match['modelId'] = $match['emailId'];
        $p = array(
            'parent' => 'User_Account',
            'parentKey' => 'account_id',
            'model' => 'User_Email'
        );
        return $this->updateManyToOne($request, $match, $p);
    }
    
    /**
     * Delete specified email of an account
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     * @return User_Email
     */
    public function delete($request, $match)
    {
        $usr = self::checkAccess($request, $match);
        $match['parentId'] = $usr->id;
        $match['modelId'] = $match['emailId'];
        $p = array(
            'parent' => 'User_Account',
            'parentKey' => 'account_id',
            'model' => 'User_Email'
        );
        return $this->deleteManyToOne($request, $match, $p);
    }
    
    /**
     * Checks permission to change emails and returns account which its email will be changed.
     *
     * Checks the permission of the requester user to change emails of defined account in the request.
     * The requester user have permission to change emails of an account if and only if he is tenant owner
     * or he is owner of the account.
     *
     * If user has not necessary permission this function will throw Pluf_Exception_Forbidden exception.
     * If user is not defined this function wil throw Pluf_Exception_BadRequest exception.
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     * @throws Pluf_Exception_Forbidden
     * @throws Pluf_Exception_BadRequest
     * @return User_Account account which its emails will be changed.
     */
    private static function checkAccess($request, $match)
    {
        if (array_key_exists('accountId', $match)) {
            // Check user is owner
            if ($request->user->id != $match['accountId'] && ! User_Precondition::isOwner($request)) {
                throw new Pluf_Exception_Forbidden('Not allowed to change others` emails');
            }
            $user = Pluf_Shortcuts_GetObjectOr404('User_Account', $match['accountId']);
        } else {
            $user = $request->user;
        }
        if ($user->isAnonymous()) {
            throw new Pluf_Exception_BadRequest('User is not defined');
        }
        return $user;
    }
    
    private static function doVerify($email)
    {
        // Load verifier engine and verify account
        $type = class_exists('Tenant_Service') ? Tenant_Service::setting('email.verifier.engine', 'noverify') : //
        Pluf::f('email.verifier.engine', 'noverify');
        if ($type === 'noverify' || $type === 'manual') {
            // Do nothing.
            // We could not verify an email address without verification process.
            return $email;
        }
        $engine = Verifier_Service::getEngine($type);
        $user = $email->get_account();
        $verification = Verifier_Service::createVerification($user, $email);
        $success = $engine->send($verification);
        if (! $success) {
            throw new Verifier_Exception_VerificationSend();
        }
        // Add verification information to the object to be verified
        $email->verification = $verification;
        return $email;
    }
    
    public static function activate($request, $match)
    {
        // Check verification code and activate the email
        $account = Pluf_Shortcuts_GetObjectOr404('User_Account', $match['accountId']);
        $email = Pluf_Shortcuts_GetObjectOr404('User_Email', $match['emailId']);
        $verification = Verifier_Service::getVerification($account, $email, $match['code']);
        if (! Verifier_Service::validateVerification($verification, $match['code'])) {
            throw new Verifier_Exception_VerificationFailed();
        }
        $email->is_verified = true;
        $email->update();
        Verifier_Service::clearVerifications($email);
        return $email;
    }

    public static function verify($request, $match)
    {
        // create verification code
        $account = Pluf_Shortcuts_GetObjectOr404('User_Account', $match['accountId']);
        $email = Pluf_Shortcuts_GetObjectOr404('User_Email', $match['emailId']);
        if($email->account_id !== $account->id){
            throw new Pluf_Exception_BadRequest('Determined account has not email with given email id.');
        }
        if ($email->is_verified) {
            // Email is verified already
            return $email;
        }
        return self::doVerify($email);
    }
}

