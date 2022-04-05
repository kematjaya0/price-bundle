<?php

namespace Kematjaya\PriceBundle\Contract;
/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class Messages 
{
    public static function triggerDeprecation(string $package, string $version, string $message, ...$args):void
    {
        trigger_error(($package || $version ? "Since $package $version: " : '').($args ? vsprintf($message, $args) : $message), \E_USER_DEPRECATED);
    }
}
