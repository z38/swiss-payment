<?php

namespace Z38\SwissPayment\Tests\TransactionInformation;

use Z38\SwissPayment\ISRParticipant;
use Z38\SwissPayment\Money;
use Z38\SwissPayment\StructuredPostalAddress;
use Z38\SwissPayment\Tests\TestCase;
use Z38\SwissPayment\TransactionInformation\ISRCreditTransfer;

/**
 * @coversDefaultClass \Z38\SwissPayment\TransactionInformation\ISRCreditTransfer
 */
class ISRCreditTransferTest extends TestCase
{
    /**
     * @covers ::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidAmount()
    {
        $transfer = new ISRCreditTransfer(
            'id000',
            'name',
            new Money\USD(100),
            new ISRParticipant('10-2424-4'),
            '120000000000234478943216899'
        );
    }

    /**
     * @covers ::setRemittanceInformation
     * @expectedException \LogicException
     */
    public function testSetRemittanceInformation()
    {
        $transfer = new ISRCreditTransfer(
            'id000',
            'name',
            new Money\CHF(100),
            new ISRParticipant('10-2424-4'),
            '120000000000234478943216899'
        );

        $transfer->setRemittanceInformation('not allowed');
    }

    /**
     * @covers ::setCreditorDetails
     */
    public function testCreditorDetails()
    {
        $transfer = new ISRCreditTransfer(
            'id000',
            'name',
            new Money\CHF(100),
            new ISRParticipant('01-25083-7'),
            '120000000000234478943216899'
        );

        $creditorName = 'name';
        $creditorAddress = new StructuredPostalAddress('foo', '99', '9999', 'bar');
        $transfer->setCreditorDetails($creditorName, $creditorAddress);
    }
}
