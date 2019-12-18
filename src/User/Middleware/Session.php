<?php

/**
 * User session
 *
 * 
 * Allow a session object in the request and the automatic
 * login/logout of a user if a standard authentication against the
 * User model is performed.
 */
class User_Middleware_Session
{

    /**
     * کلد جلسه کاربر را تعیین می‌کند.
     */
    public $session_key = '_PX_User_auth';

    /**
     * Process the request.
     *
     * When processing the request, if a session is found with
     * User creditentials the corresponding user is loaded into
     * $request->user.
     *
     * FIXME: We should logout everybody when the session table is emptied.
     *
     * @param
     *            Pluf_HTTP_Request The request
     * @return bool false
     */
    function process_request(&$request)
    {
        if(isset($request->user) && !$request->user->isAnonymous()){
            return false;
        }
        if ($request->session->containsKey($this->session_key)) {
            // We can get the corresponding user
            $id = $request->session->getData($this->session_key);
            $found_user = new User_Account($id);
            if ($found_user->id == $id) {
                // User_Account found!
                $request->user = $found_user;
                // If the last login is from 12h or more, set it to
                // now.
                Pluf::loadFunction('Pluf_Date_Compare');
                if (43200 < Pluf_Date_Compare($request->user->last_login)) {
                    $request->user->last_login = gmdate('Y-m-d H:i:s');
                    $request->user->update();
                }
                return false;
            }
        }
        $request->user = new User_Account();
        $request->user->id = 0;
        return false;
    }

    /**
     * Process the response of a view.
     *
     * If the session has been modified save it into the database.
     * Add the session cookie to the response.
     *
     * @param
     *            Pluf_HTTP_Request The request
     * @param
     *            Pluf_HTTP_Response The response
     * @return Pluf_HTTP_Response The response
     */
    function process_response($request, $response)
    {
        if (! $request->user->isAnonymous()) {
            $request->session->setData($this->session_key, $request->user->id);
        }
        return $response;
    }
}


