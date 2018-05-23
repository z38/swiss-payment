<?php

namespace Z38\SwissPayment\Tests\TransactionInformation;

use Z38\SwissPayment\BIC;
use Z38\SwissPayment\EmptyPostalAddress;
use Z38\SwissPayment\IBAN;
use Z38\SwissPayment\Money;
use Z38\SwissPayment\StructuredPostalAddress;
use Z38\SwissPayment\Tests\TestCase;
use Z38\SwissPayment\TransactionInformation\BankCreditTransfer;

/**
 * @coversDefaultClass \Z38\SwissPayment\TransactionInformation\BankCreditTransfer
 */
class BankCreditTransferTest extends TestCase
{
    /**
     * @covers ::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidCreditorAgent()
    {
        $creditorAgent = $this->getMock('\Z38\SwissPayment\FinancialInstitutionInterface');

        $transfer = new BankCreditTransfer(
            'id000',
            'name',
            new Money\CHF(100),
            'name',
            new StructuredPostalAddress('foo', '99', '9999', 'bar'),
            new IBAN('CH31 8123 9000 0012 4568 9'),
            $creditorAgent
        );
    }

    /**
     * @covers ::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidAmount()
    {
        $transfer = new BankCreditTransfer(
            'id000',
            'name',
            new Money\USD(100),
            'name',
            new StructuredPostalAddress('foo', '99', '9999', 'bar'),
            new IBAN('CH31 8123 9000 0012 4568 9'),
            new BIC('PSETPD2SZZZ')
        );
    }

    /**
     * @covers ::__construct
     * @throws \InvalidArgumentException
     */
    public function testWithoutPostalAddress()
    {
        $transfer = new BankCreditTransfer(
            'id000',
            'name',
            new Money\CHF(100),
            'name',
            new EmptyPostalAddress(),
            new IBAN('CH31 8123 9000 0012 4568 9'),
            new BIC('PSETPD2SZZZ')
        );
    }
}
