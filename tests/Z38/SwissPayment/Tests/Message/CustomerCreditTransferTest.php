<?php

namespace Z38\SwissPayment\Tests\Message;

use Z38\SwissPayment\BC;
use Z38\SwissPayment\BIC;
use Z38\SwissPayment\FinancialInstitutionAddress;
use Z38\SwissPayment\IBAN;
use Z38\SwissPayment\Message\CustomerCreditTransfer;
use Z38\SwissPayment\Money;
use Z38\SwissPayment\PaymentInformation\PaymentInformation;
use Z38\SwissPayment\PostalAccount;
use Z38\SwissPayment\StructuredPostalAddress;
use Z38\SwissPayment\Tests\TestCase;
use Z38\SwissPayment\TransactionInformation\BankCreditTransfer;
use Z38\SwissPayment\TransactionInformation\ForeignCreditTransfer;
use Z38\SwissPayment\TransactionInformation\IS1CreditTransfer;
use Z38\SwissPayment\TransactionInformation\IS2CreditTransfer;
use Z38\SwissPayment\TransactionInformation\SEPACreditTransfer;
use Z38\SwissPayment\UnstructuredPostalAddress;

class CustomerCreditTransferTest extends TestCase
{
    const SCHEMA = 'pain.001.001.03.ch.02.xsd';
    const NS_URI_ROOT = 'http://www.six-interbank-clearing.com/de/';

    protected function buildMessage()
    {
        $transaction = new BankCreditTransfer(
            'instr-001',
            'e2e-001',
            new Money\CHF(130000), // CHF 1300.00
            'Muster Transport AG',
            new StructuredPostalAddress('Wiesenweg', '14b', '8058', 'Zürich-Flughafen'),
            new IBAN('CH51 0022 5225 9529 1301 C'),
            new BIC('UBSWCHZH80A')
        );

        $transaction2 = new IS1CreditTransfer(
            'instr-002',
            'e2e-002',
            new Money\CHF(30000), // CHF 300.00
            'Finanzverwaltung Stadt Musterhausen',
            new StructuredPostalAddress('Altstadt', '1a', '4998', 'Muserhausen'),
            new PostalAccount('80-151-4')
        );

        $transaction3 = new IS2CreditTransfer(
            'instr-003',
            'e2e-003',
            new Money\CHF(20000), // CHF 200.00
            'Druckerei Muster GmbH',
            new StructuredPostalAddress('Gartenstrasse', '61', '3000', 'Bern'),
            new IBAN('CH03 0900 0000 3054 1118 8'),
            'Musterbank AG',
            new PostalAccount('80-5928-4')
        );

        $iban4 = new IBAN('CH51 0022 5225 9529 1301 C');
        $transaction4 = new BankCreditTransfer(
            'instr-004',
            'e2e-004',
            new Money\CHF(30000), // CHF 300.00
            'Muster Transport AG',
            new StructuredPostalAddress('Wiesenweg', '14b', '8058', 'Zürich-Flughafen'),
            $iban4,
            BC::fromIBAN($iban4)
        );

        $iban5 = new IBAN('DE89 3704 0044 0532 0130 00');
        $transaction5 = new SEPACreditTransfer(
            'instr-005',
            'e2e-005',
            new Money\EUR(70000), // EUR 700.00
            'Muster Immo AG',
            new UnstructuredPostalAddress('Musterstraße 35', '80333 München', 'DE'),
            $iban5,
            new BIC('COBADEFFXXX')
        );

        $iban6 = new IBAN('GB29 NWBK 6016 1331 9268 19');
        $transaction6 = new ForeignCreditTransfer(
            'instr-006',
            'e2e-006',
            new Money\GBP(6500), // GBP 65.00
            'United Development Ltd',
            new UnstructuredPostalAddress('George Street', 'BA1 2FJ Bath', 'UK'),
            $iban6,
            new BIC('NWBKGB2L')
        );

        $iban7 = new IBAN('BR97 0036 0305 0000 1000 9795 493P 1');
        $transaction7 = new ForeignCreditTransfer(
            'instr-007',
            'e2e-007',
            new Money\GBP(30000), // GBP 300.00
            'United Development Brazil Ltda.',
            new UnstructuredPostalAddress('Rua do Castelino, 1650', '41610-480 Salvador-BA', 'BR'),
            $iban6,
            new FinancialInstitutionAddress('Caixa Economica Federal', new UnstructuredPostalAddress('Rua Sao Valentim, 620', '03446-040 Sao Paulo-SP', 'BR'))
        );

        $payment = new PaymentInformation('payment-001', 'InnoMuster AG', new BIC('ZKBKCHZZ80A'), new IBAN('CH6600700110000204481'));
        $payment->addTransaction($transaction);
        $payment->addTransaction($transaction2);
        $payment->addTransaction($transaction3);
        $payment->addTransaction($transaction4);

        $payment2 = new PaymentInformation('payment-002', 'InnoMuster AG', new BIC('POFICHBEXXX'), new IBAN('CH6309000000250097798'));
        $payment2->addTransaction($transaction5);
        $payment2->addTransaction($transaction6);
        $payment2->addTransaction($transaction7);

        $message = new CustomerCreditTransfer('message-001', 'InnoMuster AG');
        $message->addPayment($payment);
        $message->addPayment($payment2);

        return $message;
    }

    public function testGroupHeader()
    {
        $xml = $this->buildMessage()->asXml();

        $doc = new \DOMDocument();
        $doc->loadXML($xml);
        $xpath = new \DOMXPath($doc);
        $xpath->registerNamespace('pain001', self::NS_URI_ROOT.self::SCHEMA);

        $nbOfTxs = $xpath->query('//pain001:GrpHdr/pain001:NbOfTxs');
        $this->assertEquals('7', $nbOfTxs->item(0)->textContent);

        $ctrlSum = $xpath->query('//pain001:GrpHdr/pain001:CtrlSum');
        $this->assertEquals('3165.00', $ctrlSum->item(0)->textContent);
    }

    public function testSchemaValidation()
    {
        $xml = $this->buildMessage()->asXml();
        $schemaPath = __DIR__.'/../../../../'.self::SCHEMA;

        $doc = new \DOMDocument();
        $doc->loadXML($xml);

        libxml_use_internal_errors(true);
        $valid = $doc->schemaValidate($schemaPath);
        foreach (libxml_get_errors() as $error) {
            $this->fail($error->message);
        }
        $this->assertTrue($valid);
        libxml_clear_errors();
        libxml_use_internal_errors(false);
    }
}
