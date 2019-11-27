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
class Verifier_Engine_SmsIr extends Verifier_Engine
{
    const ENGINE_PARAMETER_API_KEY = 'verifier.engine.SmsIr.ApiKey';
    const ENGINE_PARAMETER_SECRET_KEY = 'verifier.engine.SmsIr.SecretKey';
    const ENGINE_PARAMETER_TEMPLATE_ID = 'verifier.engine.SmsIr.TemplateId';
    
    /*
     *
     */
    public function getTitle()
    {
        return 'SMS IR';
    }

    /*
     *
     */
    public function getDescription()
    {
        return 'This verifier sends SMS to verify an entity. This verifier uses the sms.ir panel to send messages.';
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
        // Get token
        $token = $this->getToken();
        // Send SMS
        $response = $this->sendSms($verification, $mobile, $token);
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

    private function getToken()
    {
        $backend = 'http://RestfulSms.com';
        $path = '/api/Token';
        $apiKey = Tenant_Service::setting(self::ENGINE_PARAMETER_API_KEY, '');
        $secKey = Tenant_Service::setting(self::ENGINE_PARAMETER_SECRET_KEY, '');
        $param = array(
            'UserApiKey' => $apiKey,
            'SecretKey' => $secKey
        );
        $client = new GuzzleHttp\Client();
        $response = $client->request('POST', $backend . $path, [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode($param)
        ]);
        if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
            throw new Verifier_Exception_VerificationSend($response->getBody()->getContents());
        }
        $contents = $response->getBody()->getContents();
        $result = json_decode($contents, true);
        return $result['TokenKey'];
    }

    private function sendSms($verification, $mobile, $token)
    {
        $backend = 'http://RestfulSms.com';
        $headers = array(
            'x-sms-ir-secure-token' => $token,
            'Content-Type' => 'application/json'
        );
        $templateId = (int) Tenant_Service::setting(self::ENGINE_PARAMETER_TEMPLATE_ID, 0);
        $path = $templateId > 0 ? '/api/UltraFastSend' : '/api/VerificationCode';
        $param = array();
        if ($templateId > 0) {
            $param['MobileNumber'] = $mobile;
            $param['TemplateId'] = $templateId;
            $param['ParameterArray'] = array(
                array(
                    'Parameter' => 'VerificationCode',
                    'ParameterValue' => $verification->code
                )
            );
        } else {
            $param['MobileNumber'] = $mobile;
            $param['Code'] = $verification->code;
        }
        $client = new GuzzleHttp\Client();
        $response = $client->request('POST', $backend . $path, [
            'headers' => $headers,
            'body' => json_encode($param)
        ]);
        if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
            throw new Pluf_Exception($response->getBody()->getContents());
        }
        $contents = $response->getBody()->getContents();
        return json_decode($contents, true);
    }
}
