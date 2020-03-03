<?php
namespace Pluf\User\Notify\Engine;

use Pluf\Template;

/**
 * Notify engine for message
 *
 * @author maso
 *        
 */
class Message extends \Pluf\User\Notify\Engine
{

    /**
     *
     * {@inheritdoc}
     * @see \Pluf\User\Notify\Engine::push()
     */
    public function push($user, $template, $context)
    {
        // Message Template
        $tmpl = new Template($template);
        $msg = $tmpl->render(new Template\Context($context));
        if (defined('IN_UNIT_TESTS')) {
            return;
        }
        $user->setMessage($msg);
    }
}