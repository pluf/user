<?php

/**
 * Notify engine for mail
 * 
 * @author maso
 *
 */
class User_Notify_Engine_Mail implements User_Notify_Engine
{

    /**
     *
     * {@inheritdoc}
     * @see User_Notify_Engine::push()
     */
    public function push($user, $template, $context)
    {
        $subject = '=?utf-8?B?' . base64_encode($context['subject']) . '?=';
        
        // Messae
        $tmpl = new Pluf_Template($template);
        
        // Send mail
        $from = 'info@pluf.ir';
        if(class_exists('Tenant_Service')){
            $from = Tenant_Service::setting('notify.mail', 'info@pluf.ir');
        }
        $email = new Pluf_Mail($from, $user->email, $subject);
        $email->addHtmlMessage($tmpl->render(new Pluf_Template_Context($context)));
        if (defined('IN_UNIT_TESTS')) {
            return;
        }
        $res = $email->sendMail();
        if (is_a($res, 'PEAR_Error')) {
            throw new \Pluf\Exception($res);
        }
    }
}