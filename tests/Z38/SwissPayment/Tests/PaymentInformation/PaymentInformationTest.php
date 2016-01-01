<?php

namespace Z38\SwissPayment\Tests;

use Z38\SwissPayment\BIC;
use Z38\SwissPayment\IBAN;
use Z38\SwissPayment\PaymentInformation\PaymentInformation;

/**
 * @coversDefaultClass \Z38\SwissPayment\PaymentInformation\PaymentInformation
 */
class PaymentInformationTest extends TestCase
{
    /**
     * @covers ::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidDebtorAgent()
    {
        $debtorAgent = $this->getMock('\Z38\SwissPayment\FinancialInstitutionInterface');

        $payment = new PaymentInformation(
            'id000',
            'name',
            $debtorAgent,
            new IBAN('CH31 8123 9000 0012 4568 9')
        );
    }

    /**
     * @covers ::hasPaymentTypeInformation
     */
    public function testHasPaymentTypeInformation()
    {
        $payment = new PaymentInformation(
            'id000',
            'name',
            new BIC('POFICHBEXXX'),
            new IBAN('CH31 8123 9000 0012 4568 9')
        );

        $this->assertFalse($payment->hasPaymentTypeInformation());
    }
}
