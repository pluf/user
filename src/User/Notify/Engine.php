<?php

/**
 * Notification engine
 * 
 * @author maso
 *
 */
interface User_Notify_Engine
{

    /**
     * Push a messag fo the user
     *
     * @param User_Account $user
     * @param string $template
     * @param array $context
     */
    public function push($user, $template, $context);
}