<?php
namespace Pluf\User\Notify\Engine;

use Pluf\Template;
use Pluf\Exception;

/**
 * Notify engine for mail
 * 
 * @author maso
 *
 */
class Mail extends \Pluf\User\Notify\Engine
{

    /**
     *
     * {@inheritdoc}
     * @see \Pluf\User\Notify\Engine::push()
     */
    public function push($user, $template, $context)
    {
        $subject = '=?utf-8?B?' . base64_encode($context['subject']) . '?=';
        
        // Messae
        $tmpl = new Template($template);
        
        // Send mail
        $from = 'info@pluf.ir';
        if(class_exists('Tenant_Service')){
            $from = Tenant_Service::setting('notify.mail', 'info@pluf.ir');
        }
        $email = new \Pluf\Mail($from, $user->email, $subject);
        $email->addHtmlMessage($tmpl->render(new Template\Context($context)));
        if (defined('IN_UNIT_TESTS')) {
            return;
        }
        $res = $email->sendMail();
        if (is_a($res, 'PEAR_Error')) {
            throw new Exception($res);
        }
    }
}