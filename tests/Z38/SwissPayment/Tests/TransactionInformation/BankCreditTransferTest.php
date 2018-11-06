<?php

namespace Z38\SwissPayment\Tests\TransactionInformation;

use Z38\SwissPayment\BIC;
use Z38\SwissPayment\IBAN;
use Z38\SwissPayment\BBAN;
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
     * @expectedException \TypeError
     */
    public function testInvalidAmount()
    {
        $transfer = new BankCreditTransfer(
            'id000',
            'name',
            100,
            'name',
            new StructuredPostalAddress('foo', '99', '9999', 'bar'),
            new IBAN('CH31 8123 9000 0012 4568 9'),
            new BIC('PSETPD2SZZZ')
        );
    }

    /**
     * @covers ::__construct
     */
    public function testBankCreditCanBeDoneWithSEKAndBBAN()
    {
        $transfer = new BankCreditTransfer(
            'id000',
            'name',
            new Money\SEK(100),
            'name',
            new StructuredPostalAddress('foo', '99', '9999', 'bar'),
            new BBAN('1234', '123456789'),
            new BIC('PSETPD2SZZZ')
        );
    }
}
