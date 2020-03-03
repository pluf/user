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
namespace Pluf\User\Verifier;

use Pluf\Model;
use Pluf\SQL;
use Pluf\Utils;
use Pluf\User\Account;
use Pluf\User\Verification;

/**
 *
 * @author hadi
 *        
 */
class Service
{

    /**
     * Creates a new verification.
     *
     * @param Account $user
     *            the account who is owner of the verification
     * @param Model $subject
     *            the model which should be verified
     * @return Verification
     */
    public static function createVerification(Account $user, Model $subject): Verification
    {
        $verification = new Verification();
        $verification->code = Utils::getRandomNumericString(7);
        $verification->subject_class = $subject->_a['model'];
        $verification->subject_id = $subject->id;
        $verification->account_id = $user;
        if (true !== $verification->create()) {
            throw new VerificationGenerateException();
        }
        return $verification;
    }

    /**
     * Returns list of verifications to verify given subject
     *
     * @param Model|string $subject
     * @param integer $subjectId
     */
    public static function findVerifications($subject, $subjectId = null)
    {
        // get class
        if ($subject instanceof Model) { // Pluf module
            $subjectClass = $subject->getClass();
            $subjectId = $subject->getId();
        } elseif (! is_null($subject)) { // model
            $subjectClass = $subject;
        }

        // get list
        $verification = new Verification();
        $q = new SQL('subject_class=%s AND subject_id=%s', array(
            $subjectClass,
            $subjectId
        ));
        $list = $verification->getList(array(
            'filter' => $q->gen()
        ));
        return $list;
    }

    /**
     * Returns the verification with given code which is created to verify the given subject.
     *
     * @param Account $account
     *            the account which is the owner of the verification
     * @param Model $subject
     *            the model to verify
     * @param string $code
     *            the code of the verification
     * @return Verification
     */
    public static function getVerification($account, $subject, $code)
    {
        // get list
        $model = new Verification();
        $q = new SQL('subject_class=%s AND subject_id=%s AND code=%s AND account_id=%s', array(
            $subject->_a['model'],
            $subject->id,
            $code,
            $account->id
        ));
        $list = $model->getList(array(
            'filter' => $q->gen()
        ));
        if ($list === false or count($list) !== 1) {
            return false;
        }
        return $list[0];
    }

    /**
     * Checks if given code and verification is acceptable
     *
     * @param Verification $verification
     * @param string $code
     * @return boolean
     */
    public static function validateVerification($verification, $code)
    {
        // Check null and false verification
        if (! $verification) {
            return false;
        }
        // XXX: hadi 2019: check expiry count
        // Check the code and expiry time
        if ($verification->isExpired()) {
            throw new VerificationFailedException('Verification code is expired.');
        }
        return $verification->code === $code;
    }

    /**
     * Clears verifications created to verify the given subject
     *
     * @param Model|string $subject
     * @param integer $subjectId
     * @return boolean true if the process is successful.
     */
    public static function clearVerifications($subject, $subjectId = null)
    {
        // get class
        if ($subject instanceof Model) { // Pluf module
            $subjectClass = $subject->getClass();
            $subjectId = $subject->getId();
        } elseif (! is_null($subject)) { // model
            $subjectClass = $subject;
        }
        // get list
        $verification = new Verification();
        $q = new SQL('subject_class=%s AND subject_id=%s', array(
            $subjectClass,
            $subjectId
        ));
        $list = $verification->getList(array(
            'filter' => $q->gen()
        ));
        // delete
        foreach ($list as $verification) {
            $verification->delete();
        }
        return true;
    }

    /**
     * Find engine
     *
     * @param string $type
     * @return Engine engine
     */
    public static function getEngine($type)
    {
        $items = self::engines();
        foreach ($items as $item) {
            if ($item->getType() === $type) {
                return $item;
            }
        }
        return null;
    }

    /**
     * Returns the list of supported verification engines.
     *
     * @return array the list of the supported verification engines.
     */
    public static function engines()
    {
        return array(
            new Engine\NoVerify(),
            new Engine\Manual(),
            new Engine\SmsIr(),
            new Engine\GamaSmsIr()
        );
    }
}