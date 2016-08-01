<?php

namespace Z38\SwissPayment\Tests;

use Z38\SwissPayment\Money;
use Z38\SwissPayment\StructuredPostalAddress;
use Z38\SwissPayment\PostalAccount;
use Z38\SwissPayment\TransactionInformation\IS1CreditTransfer;

/**
 * @coversDefaultClass \Z38\SwissPayment\TransactionInformation\IS1CreditTransfer
 */
class IS1CreditTransferTest extends TestCase
{
    /**
     * @covers ::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidAmount()
    {
        $transfer = new IS1CreditTransfer(
            'id000',
            'name',
            new Money\USD(100),
            'name',
            new StructuredPostalAddress('foo', '99', '9999', 'bar'),
            new PostalAccount('10-2424-4')
        );
    }
}
