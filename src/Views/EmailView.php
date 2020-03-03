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
use Pluf\BadRequestException;
use Pluf\Utils;
use Pluf\User\Email;
use Pluf\ModelUtils;
use Pluf\Paginator;
use Pluf\User\Precondition;
use Pluf\User\PermissionDeniedException;
use Pluf\User\Verifier\Service;
use Pluf\User\Verifier\VerificationSendException;
use Pluf\User\Verifier\VerificationFailedException;
use Pluf\Bootstrap;

/**
 * Manage emails of account
 */
class EmailView extends \Pluf\Views
{

    /**
     * Creates new email for account
     *
     * @param Request $request
     * @param array $match
     * @return Email created account
     */
    public function create($request, $match)
    {
        $user = self::checkAccess($request, $match);
        // Create email
        $data = $request->REQUEST;
        if (! Utils::isValidEmail($data['email'])) {
            throw new BadRequestException('Email is not a valid email address.');
        }
        $email = new Email();
        $email->_a['cols']['email']['editable'] = true;
        $form = ModelUtils::getCreateForm($email, $data);
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
     * @param Request $request
     * @param array $match
     * @return Email
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
     * @param Request $request
     * @param array $match
     * @return Paginator
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
     * @param Request $request
     * @param array $match
     * @return Email
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
     * @param Request $request
     * @param array $match
     * @return Email
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
     * @param Request $request
     * @param array $match
     */
    private static function checkAccess($request, $match)
    {
        if (array_key_exists('accountId', $match)) {
            // Check user is owner
            if ($request->user->id != $match['accountId'] && ! Precondition::isOwner($request)) {
                throw new PermissionDeniedException('Not allowed to change others` emails');
            }
            $user = Pluf_Shortcuts_GetObjectOr404('User_Account', $match['accountId']);
        } else {
            $user = $request->user;
        }
        if ($user->isAnonymous()) {
            throw new BadRequestException('User is not defined');
        }
        return $user;
    }

    private static function doVerify($email)
    {
        // Load verifier engine and verify account
        $type = class_exists('Tenant_Service') ? Tenant_Service::setting('email.verifier.engine', 'noverify') : //
        Bootstrap::f('email.verifier.engine', 'noverify');
        if ($type === 'noverify' || $type === 'manual') {
            // Do nothing.
            // We could not verify an email address without verification process.
            return $email;
        }
        $engine = Service::getEngine($type);
        $user = $email->get_account();
        $verification = Service::createVerification($user, $email);
        $engineResponse = $engine->send($verification);
        if (! $engineResponse) {
            throw new VerificationSendException();
        }
        // Add verification information to the object to be verified
        $email->verification = $verification;
        $email->verifier_response = $engineResponse;
        return $email;
    }

    public static function activate($request, $match)
    {
        // Check verification code and activate the email
        $account = Pluf_Shortcuts_GetObjectOr404('User_Account', $match['accountId']);
        $email = Pluf_Shortcuts_GetObjectOr404('User_Email', $match['emailId']);
        $verification = Service::getVerification($account, $email, $match['code']);
        if (! Service::validateVerification($verification, $match['code'])) {
            throw new VerificationFailedException();
        }
        $email->is_verified = true;
        $email->update();
        Service::clearVerifications($email);
        return $email;
    }

    public function verify($request, $match)
    {
        // create verification code
        $account = Pluf_Shortcuts_GetObjectOr404('User_Account', $match['accountId']);
        $email = Pluf_Shortcuts_GetObjectOr404('User_Email', $match['emailId']);
        if ($email->account_id !== $account->id) {
            throw new BadRequestException('Determined account has not email with given email id.');
        }
        if ($email->is_verified) {
            // Email is verified already
            return $email;
        }
        return self::doVerify($email);
    }
}

