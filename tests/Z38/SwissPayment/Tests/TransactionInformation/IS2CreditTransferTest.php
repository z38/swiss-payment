<?php

namespace Z38\SwissPayment\Tests\TransactionInformation;

use Z38\SwissPayment\IBAN;
use Z38\SwissPayment\Money;
use Z38\SwissPayment\PostalAccount;
use Z38\SwissPayment\StructuredPostalAddress;
use Z38\SwissPayment\Tests\TestCase;
use Z38\SwissPayment\TransactionInformation\IS2CreditTransfer;

/**
 * @coversDefaultClass \Z38\SwissPayment\TransactionInformation\IS2CreditTransfer
 */
class IS2CreditTransferTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testInvalidAmount()
    {
        $this->expectException(\InvalidArgumentException::class);
        $transfer = new IS2CreditTransfer(
            'id000',
            'name',
            new Money\USD(100),
            'creditor name',
            new StructuredPostalAddress('foo', '99', '9999', 'bar'),
            new Iban('AZ21 NABZ 0000 0000 1370 1000 1944'),
            'name',
            new PostalAccount('10-2424-4')
        );
    }
}
