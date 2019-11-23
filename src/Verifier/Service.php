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
class Verifier_Service
{

    /**
     * Creates a new verification.
     *
     * @param User_Account $user the account who is owner of the verification 
     * @param Pluf_Model $subject
     * @return Verifier_Verification
     */
    public static function createVerification($user, $subject){
        $verification = new Verifier_Verification();
        $verification->code = Pluf_Utils::getRandomString(7);
        $verification->subject_class = $subject->_a['model'];
        $verification->subject_id = $subject->id;
        $verification->account_id = $user;
        if(true !== $verification->create()){
            throw new Verifier_Exception_VerificationGenerate();
        }
        return $verification;
    }

    /**
     * Finds a verification
     *
     * @param Pluf_Model $subject
     * @param integer $subjectId
     */
    public static function find($subject, $subjectId = null)
    {
        // get class
        if ($subject instanceof Pluf_Model) { // Pluf module
            $subjectClass = $subject->getClass();
            $subjectId = $subject->getId();
        } elseif (! is_null($subject)) { // module
            $subjectClass = $subject;
        }

        // get list
        $verification = new Verifier_Verification();
        $q = new Pluf_SQL('subject_class=%s AND subject_id=%s', array(
            $subjectClass,
            $subjectId
        ));
        $list = $verification->getList(array(
            'filter' => $q->gen()
        ));
        return $list;
    }

    /**
     * Find engine
     *
     * @param string $type
     * @return Verifier_Engine engine
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
            new Verifier_Engine_NoVerify(),
            new Verifier_Engine_Manual()
        );
    }

    
}