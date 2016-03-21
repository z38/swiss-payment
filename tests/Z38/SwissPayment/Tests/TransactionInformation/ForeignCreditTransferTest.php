<?php

namespace Z38\SwissPayment\Tests;

use Z38\SwissPayment\BIC;
use Z38\SwissPayment\IBAN;
use Z38\SwissPayment\Money;
use Z38\SwissPayment\StructuredPostalAddress;
use Z38\SwissPayment\TransactionInformation\ForeignCreditTransfer;

/**
 * @coversDefaultClass \Z38\SwissPayment\TransactionInformation\ForeignCreditTransfer
 */
class ForeignCreditTransferTest extends TestCase
{
    /**
     * @covers ::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidCreditorAgent()
    {
        $creditorAgent = $this->getMock('\Z38\SwissPayment\FinancialInstitutionInterface');

        $iban = new IBAN('CH31 8123 9000 0012 4568 9');
        $transfer = new ForeignCreditTransfer(
            'id000',
            'name',
            new Money\CHF(100),
            'name',
            new StructuredPostalAddress('foo', '99', '9999', 'bar'),
            $iban,
            $creditorAgent
        );
        $this->assertTrue($iban->isValidIban());
    }
    /**
     * Test a valid ForeignCreditTransfer with Other bank account
     * @covers ::__construct
     */
    public function testValidOther()
    {
        $iban = new IBAN('208381234');
        $transfer = new ForeignCreditTransfer(
            'id000',
            'name',
            new Money\CHF(100),
            'name',
            new StructuredPostalAddress('foo', '99', '9999', 'bar'),
            $iban,
            new BIC('HAKRSRPA')
        );
        $this->assertFalse($iban->isValidIban());
    }
}
