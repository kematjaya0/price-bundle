<?php

declare(strict_types=1);

namespace Kematjaya\PriceBundle\Contract;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class Messages
{
    public static function triggerDeprecation(string $package, string $version, string $message, mixed ...$args): void
    {
        $prefix = ('' !== $package || '' !== $version) ? sprintf('Since %s %s: ', $package, $version) : '';
        $formattedMessage = $args ? vsprintf($message, $args) : $message;

        trigger_error($prefix . $formattedMessage, \E_USER_DEPRECATED);
    }
}
