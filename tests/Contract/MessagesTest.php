<?php

declare(strict_types=1);

namespace Kematjaya\PriceBundle\Tests\Contract;

use Kematjaya\PriceBundle\Contract\Messages;
use PHPUnit\Framework\TestCase;

class MessagesTest extends TestCase
{
    /**
     * @var array<int, array{int, string}>
     */
    private $triggeredErrors;

    protected function setUp(): void
    {
        $this->triggeredErrors = [];
    }

    public function testTriggerDeprecationWithPackageAndVersion(): void
    {
        $this->captureDeprecation(function () {
            Messages::triggerDeprecation('kematjaya/price-bundle', '2.0', 'field %s is removed', 'currency');
        });

        $this->assertSame(
            [[\E_USER_DEPRECATED, 'Since kematjaya/price-bundle 2.0: field currency is removed']],
            $this->triggeredErrors
        );
    }

    public function testTriggerDeprecationWithoutArgsSkipsVsprintf(): void
    {
        $this->captureDeprecation(function () {
            Messages::triggerDeprecation('kematjaya/price-bundle', '2.0', 'plain message');
        });

        $this->assertSame(
            [[\E_USER_DEPRECATED, 'Since kematjaya/price-bundle 2.0: plain message']],
            $this->triggeredErrors
        );
    }

    public function testTriggerDeprecationWithoutPackageOrVersionOmitsPrefix(): void
    {
        $this->captureDeprecation(function () {
            Messages::triggerDeprecation('', '', 'plain message');
        });

        $this->assertSame([[\E_USER_DEPRECATED, 'plain message']], $this->triggeredErrors);
    }

    private function captureDeprecation(callable $callback): void
    {
        set_error_handler(function (int $errno, string $errstr): bool {
            $this->triggeredErrors[] = [$errno, $errstr];

            return true;
        }, \E_USER_DEPRECATED);

        try {
            $callback();
        } finally {
            restore_error_handler();
        }
    }
}
