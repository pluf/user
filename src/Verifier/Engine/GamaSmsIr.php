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
class Verifier_Engine_GamaSmsIr extends Verifier_Engine
{
    const ENGINE_PARAMETER_USERNAME = 'verifier.engine.GamaSmsIr.username';
    const ENGINE_PARAMETER_PASSWORD = 'verifier.engine.GamaSmsIr.password';
    const ENGINE_PARAMETER_FROM = 'verifier.engine.GamaSmsIr.from';
    /**
     * The string [code] in the template will be replaced with the verification code. 
     * @var string
     */
    const ENGINE_PARAMETER_TEMPLATE = 'verifier.engine.GamaSmsIr.template';
    
    
    /*
     *
     */
    public function getTitle()
    {
        return 'Gama SMS (Gama Payamak)';
    }

    /*
     *
     */
    public function getDescription()
    {
        return 'This verifier sends SMS to verify an entity. This verifier uses the gamasms.ir (gamapayamak.com) panel to send messages.';
    }

    /*
     *
     */
    public function getExtraParam()
    {
        return array();
    }

    public function send($verification)
    {
        // Get mobile number
        $mobile = $this->getMobile($verification);
        if (! $mobile) {
            throw new Verifier_Exception_VerificationSend('There is no phone related to user to send verification SMS.');
        }
        // Send SMS
        $response = $this->sendSms($verification, $mobile);
        return $response;
    }

    private function getMobile($verification)
    {
        $subject = $verification->getSubject();
        switch ($subject->_a['model']) {
            case 'User_Account':
                $phones = $subject->get_phones_list();
                if (! $phones || count($phones) === 0) {
                    return false;
                }
                return $phones[0]->phone;
            case 'User_Phone':
                return $subject->phone;
        }
        return null;
    }

    private function sendSms($verification, $mobile)
    {
        $backend = 'https://rest.payamak-panel.com';
        $path = '/api/SendSMS/SendSMS';
        $headers = array(
            'Content-Type' => 'application/x-www-form-urlencoded'
        );
        $param = $this->initParameters($verification->code, $mobile);
        $client = new GuzzleHttp\Client();
        $response = $client->request('POST', $backend . $path, [
            'headers' => $headers,
            'form_params' => $param
        ]);
        if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
            throw new Pluf_Exception($response->getBody()->getContents());
        }
        $contents = $response->getBody()->getContents();
        return json_decode($contents, true);
    }
    
    /**
     * Provides and returns needed parameters to send SMS.
     * @return array
     */
    private function initParameters($verificationCode, $mobile){
        $username = Tenant_Service::setting(self::ENGINE_PARAMETER_USERNAME);
        $password = Tenant_Service::setting(self::ENGINE_PARAMETER_PASSWORD);
        $from = Tenant_Service::setting(self::ENGINE_PARAMETER_FROM);
        $template = Tenant_Service::setting(self::ENGINE_PARAMETER_TEMPLATE);
        $text = str_replace('[code]', $verificationCode, $template);
        $param = array(
            'username' => $username,
            'password' => $password,
            'from' => $from,
            'to' => $mobile,
            'text' => $text,
            'isFlash' => false
        );
        return $param;
    }
}
