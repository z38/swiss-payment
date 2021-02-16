<?php

namespace Z38\SwissPayment\Tests\PaymentInformation;

use DOMDocument;
use DOMXPath;
use Z38\SwissPayment\BIC;
use Z38\SwissPayment\FinancialInstitutionInterface;
use Z38\SwissPayment\IBAN;
use Z38\SwissPayment\Money;
use Z38\SwissPayment\PaymentInformation\CategoryPurposeCode;
use Z38\SwissPayment\PaymentInformation\PaymentInformation;
use Z38\SwissPayment\PostalAccount;
use Z38\SwissPayment\StructuredPostalAddress;
use Z38\SwissPayment\Tests\TestCase;
use Z38\SwissPayment\TransactionInformation\IS1CreditTransfer;

/**
 * @coversDefaultClass \Z38\SwissPayment\PaymentInformation\PaymentInformation
 */
class PaymentInformationTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testInvalidDebtorAgent()
    {
        $this->expectException(\InvalidArgumentException::class);
        $debtorAgent = $this->createMock(FinancialInstitutionInterface::class);

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

        self::assertFalse($payment->hasPaymentTypeInformation());
    }

    /**
     * @covers ::asDom
     */
    public function testInfersPaymentInformation()
    {
        $doc = new DOMDocument();
        $payment = new PaymentInformation(
            'id000',
            'name',
            new BIC('POFICHBEXXX'),
            new IBAN('CH31 8123 9000 0012 4568 9')
        );
        $payment->setCategoryPurpose(new CategoryPurposeCode('SALA'));
        $payment->addTransaction(new IS1CreditTransfer(
            'instr-001',
            'e2e-001',
            new Money\CHF(10000), // CHF 100.00
            'Fritz Bischof',
            new StructuredPostalAddress('Dorfstrasse', '17', '9911', 'Musterwald'),
            new PostalAccount('60-9-9')
        ));
        $payment->addTransaction(new IS1CreditTransfer(
            'instr-002',
            'e2e-002',
            new Money\CHF(30000), // CHF 300.00
            'Franziska Meier',
            new StructuredPostalAddress('Altstadt', '1a', '4998', 'Muserhausen'),
            new PostalAccount('80-151-4')
        ));

        $xml = $payment->asDom($doc);

        $xpath = new DOMXPath($doc);
        self::assertNull($payment->getServiceLevel());
        self::assertNull($payment->getLocalInstrument());
        self::assertSame('CH02', $xpath->evaluate('string(./PmtTpInf/LclInstrm/Prtry)', $xml));
        self::assertSame(0.0, $xpath->evaluate('count(./CdtTrfTxInf/PmtTpInf/LclInstrm/Prtry)', $xml));
    }
}
