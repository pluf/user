<?php

/**
 * Notify engine for message
 * 
 * @author maso
 *
 */
class User_Notify_Engine_Message implements User_Notify_Engine
{

    /**
     *
     * {@inheritdoc}
     * @see User_Notify_Engine::push()
     */
    public function push($user, $template, $context)
    {
        // Message Template
        $tmpl = new Pluf_Template($template);
        $msg = $tmpl->render(new Pluf_Template_Context($context));
        if (defined('IN_UNIT_TESTS')) {
            return;
        }
        $user->setMessage($msg);
    }
}