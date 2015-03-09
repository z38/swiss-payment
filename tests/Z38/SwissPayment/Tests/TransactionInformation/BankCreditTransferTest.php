<?php

namespace Z38\SwissPayment\Tests;

use Z38\SwissPayment\TransactionInformation\BankCreditTransfer;
use Z38\SwissPayment\Money;
use Z38\SwissPayment\IBAN;
use Z38\SwissPayment\PostalAddress;

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
            new PostalAddress('foo', '99', '9999', 'bar'),
            new IBAN('CH31 8123 9000 0012 4568 9'),
            $creditorAgent
        );
    }
}
