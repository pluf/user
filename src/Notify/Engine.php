<?php
namespace Pluf\User\Notify;

use Pluf\User\Account;

/**
 * Notification engine
 *
 * @author maso
 *        
 */
abstract class Engine
{

    /**
     * Push a messag fo the user
     *
     * @param Account $user
     * @param string $template
     * @param array $context
     */
    public abstract function push(Account $user, $template, $context);
}