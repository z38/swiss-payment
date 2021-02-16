<?php

namespace Z38\SwissPayment\Tests;

use InvalidArgumentException;
use Z38\SwissPayment\GeneralAccount;

class GeneralAccountTest extends TestCase
{
    /**
     * @covers \Z38\SwissPayment\GeneralAccount::__construct
     */
    public function testValid()
    {
        $instance = new GeneralAccount('A-123-4567890-78');
        self::assertInstanceOf(GeneralAccount::class, $instance);
    }

    /**
     * @covers \Z38\SwissPayment\GeneralAccount::__construct
     */
    public function testInvalid()
    {
        $this->expectException(\InvalidArgumentException::class);
        $instance = new GeneralAccount('0123456789012345678901234567890123456789');
    }

    /**
     * @covers \Z38\SwissPayment\GeneralAccount::format
     */
    public function testFormat()
    {
        $instance = new GeneralAccount('  123-4567890-78 AA ');
        self::assertSame('  123-4567890-78 AA ', $instance->format());
    }
}
