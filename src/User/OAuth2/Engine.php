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
 * The general OAuth2 engine
 *
 * @author hadi<mohammad.hadi.mansouri@dpq.co.ir>
 */
abstract class User_OAuth2_Engine implements JsonSerializable
{
    const ClientId = 'client_id';
    const ClientSecret = 'client_secret';
    const RedirectUri = 'redirect_uri';
    const UrlAuthorize = 'url_authorize';
    const UrlAccessToken = 'url_accessToken';
    const UrlResourceOwnerDetails = 'url_resourceOwnerDetails';
    
    const ENGINE_PREFIX = 'user_oauth2_engine_';
    
    /**
     *
     * @return string
     */
    public function getType()
    {
        $name = strtolower(get_class($this));
        // NOTE: hadi, 1399: all of the engines should be placed in the oauth2/engine package
        if (strpos($name, User_OAuth2_Engine::ENGINE_PREFIX) !== 0) {
            throw new User_OAuth2_Exception_EngineLoad('Engine class must be placed in engine package.');
        }
        return substr($name, strlen(User_OAuth2_Engine::ENGINE_PREFIX));
    }
    
    abstract public function getTitle();
    
    abstract public function getDescription();
    
    abstract public function getSymbol();
    
    /**
     * (non-PHPdoc)
     *
     * @see JsonSerializable::jsonSerialize()
     */
    public function jsonSerialize()
    {
        $coded = array(
            'type' => $this->getType(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'symbol' => $this->getSymbol()
        );
        return $coded;
    }
    
    /**
     * List of the paremeters of the engine
     *
     * It returns a list of property descriptors
     */
    public function getParameters()
    {
        $param = array(
            'id' => $this->getType(),
            'name' => $this->getType(),
            'type' => 'struct',
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'editable' => true,
            'visible' => true,
            'priority' => 5,
            'symbol' => $this->getSymbol(),
            'children' => []
        );
        $general = $this->getGeneralParam();
        foreach ($general as $gp) {
            $param['children'][] = $gp;
        }
        
        $extra = $this->getExtraParam();
        foreach ($extra as $ep) {
            $param['children'][] = $ep;
        }
        return $param;
    }
    
    /**
     * List of the general parameters
     * 
     * @return
     *
     */
    public function getGeneralParam()
    {
        $params = array();
        $params[] = array(
            'name' => 'title',
            'type' => 'String',
            'unit' => 'none',
            'title' => 'title',
            'description' => 'beackend title',
            'editable' => true,
            'visible' => true,
            'priority' => 5,
            'symbol' => 'title',
            'defaultValue' => 'no title',
            'validators' => [
                'NotNull',
                'NotEmpty'
            ]
        );
        $params[] = array(
            'name' => 'description',
            'type' => 'String',
            'unit' => 'none',
            'title' => 'description',
            'description' => 'beackend description',
            'editable' => true,
            'visible' => true,
            'priority' => 5,
            'symbol' => 'title',
            'defaultValue' => 'description',
            'validators' => []
        );
        $params[] = array(
            'name' => 'symbol',
            'type' => 'String',
            'unit' => 'none',
            'title' => 'Symbol',
            'description' => 'beackend symbol',
            'editable' => true,
            'visible' => true,
            'priority' => 5,
            'symbol' => 'icon',
            'defaultValue' => '',
            'validators' => []
        );
        $params[] = array(
            'name' => self::ClientId,
            'type' => 'String',
            'unit' => 'none',
            'title' => 'client id',
            'description' => 'client id of the backend',
            'editable' => true,
            'visible' => true,
            'priority' => 5,
            'symbol' => 'client',
            'defaultValue' => '',
            'validators' => [
                'NotNull',
                'NotEmpty'
            ]
        );
        $params[] = array(
            'name' => self::ClientSecret,
            'type' => 'String',
            'unit' => 'none',
            'title' => 'client secret',
            'description' => 'client id of the backend',
            'editable' => true,
            'visible' => true,
            'priority' => 5,
            'symbol' => 'client',
            'defaultValue' => '',
            'validators' => [
                'NotNull',
                'NotEmpty'
            ]
        );
        return $params;
    }
    
    /**
     * Returns a list of property descriptors of the paremeters specific to the engine.
     * Returns an empty array if engine has not extra parameters.
     */
    abstract public function getExtraParam();
    
    /**
     * 
     * @param Pluf_HTTP_Request $request
     * @return League\OAuth2\Client\Provider\ResourceOwnerInterface
     */
    public function getOwnerDetails($request){
        $params = $request->REQUEST;
        $provider = $this->getProvider($params);
        // Try to get an access token (using the authorization code grant)
        $token = $provider->getAccessToken('authorization_code', [
            'code' => $params['code']
        ]);
        if ($token->hasExpired()) {
            $token = $token->getRefreshToken();
        }
        // We got an access token, let's now get the owner details
        $ownerDetails = $provider->getResourceOwner($token);
        return $ownerDetails;
    }
    
    abstract public function getProvider($options);
    
    abstract public function authenticate($request);
    
    abstract public function connect($request);
    
}

