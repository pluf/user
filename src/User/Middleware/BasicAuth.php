<?php

/**
 * Basic Auth middleware
 *
 * Allow basic_auth for REST API.
 * 
 */
class User_Middleware_BasicAuth
{

    /**
     * Process the request.
     *
     *
     * @param
     *            Pluf_HTTP_Request The request
     * @return bool false
     */
    function process_request (&$request)
    {
        if(isset($request->user) && !$request->user->isAnonymous()){
            return false;
        }
        if (! isset($request->SERVER['PHP_AUTH_USER'])) {
            return false;
        }
        $login = $request->SERVER['PHP_AUTH_USER'];
        $password = $request->SERVER['PHP_AUTH_PW'];
        
        $user = User_Auth_ModelBackend::getUser($login);
        if(User_Credential::checkCredential($login, $password)){
            $request->user = $user;
        }
        return false;
    }

}