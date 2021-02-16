<?php

namespace Z38\SwissPayment\Tests\TransactionInformation;

use Z38\SwissPayment\FinancialInstitutionInterface;
use Z38\SwissPayment\IBAN;
use Z38\SwissPayment\Money;
use Z38\SwissPayment\StructuredPostalAddress;
use Z38\SwissPayment\Tests\TestCase;
use Z38\SwissPayment\TransactionInformation\ForeignCreditTransfer;

/**
 * @coversDefaultClass \Z38\SwissPayment\TransactionInformation\ForeignCreditTransfer
 */
class ForeignCreditTransferTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testInvalidCreditorAgent()
    {
        $this->expectException(\InvalidArgumentException::class);
        $creditorAgent = $this->createMock(FinancialInstitutionInterface::class);

        $transfer = new ForeignCreditTransfer(
            'id000',
            'name',
            new Money\CHF(100),
            'name',
            new StructuredPostalAddress('foo', '99', '9999', 'bar'),
            new IBAN('CH31 8123 9000 0012 4568 9'),
            $creditorAgent
        );
    }
}
