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
use Pluf\ModelUtils;
use Pluf\User\Phone;
use Pluf\User\PermissionDeniedException;
use Pluf\BadRequestException;
use Pluf\User\Verifier\Service;
use Pluf\User\Verifier\VerificationSendException;
use Pluf\User\Verifier\VerificationFailedException;
use Pluf\Bootstrap;
use Pluf\User\Precondition;

// Pluf::loadFunction('Pluf_Shortcuts_GetObjectOr404');

/**
 * Manage phones of account
 */
class PhoneView extends \Pluf\Views
{

    /**
     * Creates new phone for account
     *
     * @param Request $request
     * @param array $match
     * @return Phone created phone
     */
    public static function create(Request $request, $match)
    {
        $user = self::checkAccess($request, $match);
        // Create phone
        $data = $request->REQUEST;
        $phone = new Phone();
        $phone->_a['cols']['phone']['editable'] = true;
        $form = ModelUtils::getCreateForm($phone, $data);
        $phone = $form->save(false);
        $phone->account_id = $user;
        $phone->create();
        // Verifying phone
        $phone = self::doVerify($phone);
        return $phone;
    }

    /**
     * Returns specified phone of an account
     *
     * @param Request $request
     * @param array $match
     * @return Phone
     */
    public function get($request, $match)
    {
        $usr = self::checkAccess($request, $match);
        $match['parentId'] = $usr->id;
        $match['modelId'] = $match['phoneId'];
        $p = array(
            'parent' => 'User_Account',
            'parentKey' => 'account_id',
            'model' => 'User_Phone'
        );
        return $this->getManyToOne($request, $match, $p);
    }

    /**
     * Lists phones of an account
     *
     * @param Request $request
     * @param array $match
     */
    public function find($request, $match)
    {
        $usr = self::checkAccess($request, $match);
        $match['parentId'] = $usr->id;
        $p = array(
            'parent' => 'User_Account',
            'parentKey' => 'account_id',
            'model' => 'User_Phone'
        );
        return $this->findManyToOne($request, $match, $p);
    }

    /**
     * Updates information of the phone of an account.
     *
     * Note that the phone address could not be changed by updating process.
     *
     * @param Request $request
     * @param array $match
     * @return Phone
     */
    public function update($request, $match)
    {
        $usr = self::checkAccess($request, $match);
        $match['parentId'] = $usr->id;
        $match['modelId'] = $match['phoneId'];
        $p = array(
            'parent' => 'User_Account',
            'parentKey' => 'account_id',
            'model' => 'User_Phone'
        );
        return $this->updateManyToOne($request, $match, $p);
    }

    /**
     * Delete specified phone of an account
     *
     * @param Request $request
     * @param array $match
     * @return Phone
     */
    public function delete($request, $match)
    {
        $usr = self::checkAccess($request, $match);
        $match['parentId'] = $usr->id;
        $match['modelId'] = $match['phoneId'];
        $p = array(
            'parent' => 'User_Account',
            'parentKey' => 'account_id',
            'model' => 'User_Phone'
        );
        return $this->deleteManyToOne($request, $match, $p);
    }

    /**
     * Checks permission to change phones and returns account which its phone will be changed.
     *
     * Checks the permission of the requester user to change phones of defined account in the request.
     * The requester user have permission to change phones of an account if and only if he is tenant owner
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
                throw new PermissionDeniedException('Not allowed to change others` phones');
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

    private static function doVerify($phone)
    {
        // Load verifier engine and verify account
        $type = class_exists('Tenant_Service') ? Service::setting('phone.verifier.engine', 'noverify') : //
        Bootstrap::f('phone.verifier.engine', 'noverify');
        if ($type === 'noverify' || $type === 'manual') {
            // Do nothing.
            // We could not verify an phone address without verification process.
            return $phone;
        }
        $engine = Service::getEngine($type);
        $user = $phone->get_account();
        $verification = Service::createVerification($user, $phone);
        $engineReponse = $engine->send($verification);
        if (! $engineReponse) {
            throw new VerificationSendException();
        }
        // Add verification information to the object to be verified
        $phone->verification = $verification;
        $phone->engineResponse = $engineReponse;
        return $phone;
    }

    public static function activate($request, $match)
    {
        // Check verification code and activate the phone
        $account = Pluf_Shortcuts_GetObjectOr404('User_Account', $match['accountId']);
        $phone = Pluf_Shortcuts_GetObjectOr404('User_Phone', $match['phoneId']);
        $verification = Service::getVerification($account, $phone, $match['code']);
        if (! Service::validateVerification($verification, $match['code'])) {
            throw new VerificationFailedException();
        }
        $phone->is_verified = true;
        $phone->update();
        Service::clearVerifications($phone);
        return $phone;
    }

    public static function verify($request, $match)
    {
        // create verification code
        $account = Pluf_Shortcuts_GetObjectOr404('User_Account', $match['accountId']);
        $phone = Pluf_Shortcuts_GetObjectOr404('User_Phone', $match['phoneId']);
        if ($phone->account_id !== $account->id) {
            throw new BadRequestException('Determined account has not phone with given phone id.');
        }
        if ($phone->is_verified) {
            // Phone is verified already
            return $phone;
        }
        return self::doVerify($phone);
    }
}

