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
 * @author hadi <mohammad.hadi.mansouri@dpq.co.ir>
 *        
 */
class User_Views_OAuth2Server extends Pluf_Views
{

    /**
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     */
    public function create($request, $match)
    {
        $type = 'not set';
        if (array_key_exists('type', $request->REQUEST)) {
            $type = $request->REQUEST['type'];
        }
        Pluf::loadFunction('User_OAuth2_Shortcuts_GetEngineOr404');
        $engine = User_OAuth2_Shortcuts_GetEngineOr404($type);
        $params = array(
            'engine' => $engine
        );
        $form = new User_OAuth2_Form_ServerNew($request->REQUEST, $params);
        $server = $form->save();
        return $server;
    }

    /**
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     */
    public function get($request, $match)
    {
        Pluf::loadFunction('User_OAuth2_Shortcuts_GetServerOr404');
        $server = User_OAuth2_Shortcuts_GetServerOr404($match['id']);
        if(isset($request->REQUEST['redirect_uri'])){
            $redirectUriTmpl = $request->REQUEST['redirect_uri'];
            $authUrl = $this->generateAuthorizationUrl($server, $redirectUriTmpl);
            $server->auth_url = $authUrl;
        }
        return $server;
    }

    public function find($request, $match)
    {
        $builder = new Pluf_Paginator_Builder(new User_OAuth2Server());
        $result = $builder->setRequest($request)
            ->setWhereClause(new Pluf_SQL('deleted=false'))
            ->build()
            ->render_object();
        
        $redirectUriTmpl = '';
        $data = $request->REQUEST;
        if(isset($data['redirect_uri'])){
            $redirectUriTmpl = $data['redirect_uri'];
            for ($i = 0; $i < count($result['items']); $i ++) {
                $authServer = $result['items'][$i];
                $authUrl = $this->generateAuthorizationUrl($authServer, $redirectUriTmpl);
                $result['items'][$i]->auth_url = $authUrl;
            }
        }
        return $result;
    }

    private function generateAuthorizationUrl($authServer, $redirectUriTmpl){
        $m = new Mustache_Engine();
        $engine = $authServer->get_engine();
        $redirectUri = $m->render($redirectUriTmpl, $authServer->getData());
        $options = array(
            'redirect_uri' => $redirectUri,
            'server_id' => $authServer->id
        );
        $authUrl = $engine->getProvider($options)->getAuthorizationUrl();
        return $authUrl;
    }
    
    /**
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     */
    public function update($request, $match)
    {
        Pluf::loadFunction('User_OAuth2_Shortcuts_GetServerOr404');
        $server = User_OAuth2_Shortcuts_GetServerOr404($match['id']);
        $params = array(
            'backend' => $server
        );
        $form = new User_OAuth2_Form_ServerUpdate($request->REQUEST, $params);
        $server = $form->update();
        return $server;
    }
}
