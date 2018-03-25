<?php

namespace Z38\SwissPayment\Tests\Message;

use Z38\SwissPayment\BIC;
use Z38\SwissPayment\FinancialInstitutionAddress;
use Z38\SwissPayment\GeneralAccount;
use Z38\SwissPayment\IBAN;
use Z38\SwissPayment\IID;
use Z38\SwissPayment\ISRParticipant;
use Z38\SwissPayment\Message\CustomerCreditTransfer;
use Z38\SwissPayment\Money;
use Z38\SwissPayment\PaymentInformation\CategoryPurposeCode;
use Z38\SwissPayment\PaymentInformation\PaymentInformation;
use Z38\SwissPayment\PaymentInformation\SEPAPaymentInformation;
use Z38\SwissPayment\PostalAccount;
use Z38\SwissPayment\StructuredPostalAddress;
use Z38\SwissPayment\Tests\TestCase;
use Z38\SwissPayment\TransactionInformation\BankCreditTransfer;
use Z38\SwissPayment\TransactionInformation\ForeignCreditTransfer;
use Z38\SwissPayment\TransactionInformation\IS1CreditTransfer;
use Z38\SwissPayment\TransactionInformation\IS2CreditTransfer;
use Z38\SwissPayment\TransactionInformation\ISRCreditTransfer;
use Z38\SwissPayment\TransactionInformation\PurposeCode;
use Z38\SwissPayment\TransactionInformation\SEPACreditTransfer;
use Z38\SwissPayment\UnstructuredPostalAddress;

class CustomerCreditTransferTest extends TestCase
{
    const SCHEMA = 'http://www.six-interbank-clearing.com/de/pain.001.001.03.ch.02.xsd';
    const SCHEMA_PATH = 'pain.001.001.03.ch.02.xsd';

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
            IID::fromIBAN($iban4)
        );
        $transaction4->setPurpose(new PurposeCode('AIRB'));

        $transaction5 = new SEPACreditTransfer(
            'instr-005',
            'e2e-005',
            new Money\EUR(70000), // EUR 700.00
            'Muster Immo AG',
            new UnstructuredPostalAddress('Musterstraße 35', '80333 München', 'DE'),
            new IBAN('DE89 3704 0044 0532 0130 00'),
            new BIC('COBADEFFXXX')
        );

        $transaction6 = new ForeignCreditTransfer(
            'instr-006',
            'e2e-006',
            new Money\GBP(6500), // GBP 65.00
            'United Development Ltd',
            new UnstructuredPostalAddress('George Street', 'BA1 2FJ Bath', 'GB'),
            new IBAN('GB29 NWBK 6016 1331 9268 19'),
            new BIC('NWBKGB2L')
        );

        $transaction7 = new ForeignCreditTransfer(
            'instr-007',
            'e2e-007',
            new Money\GBP(30000), // GBP 300.00
            'United Development Brazil Ltda.',
            new UnstructuredPostalAddress('Rua do Castelino, 1650', '41610-480 Salvador-BA', 'BR'),
            new IBAN('BR97 0036 0305 0000 1000 9795 493P 1'),
            new FinancialInstitutionAddress('Caixa Economica Federal', new UnstructuredPostalAddress('Rua Sao Valentim, 620', '03446-040 Sao Paulo-SP', 'BR'))
        );

        $transaction8 = new ForeignCreditTransfer(
            'instr-008',
            'e2e-008',
            new Money\GBP(4500), // GBP 45.00
            'United Development Belgium SA/NV',
            new UnstructuredPostalAddress('Oostjachtpark 187', '6743 Buzenol', 'BE'),
            new GeneralAccount('123-4567890-78'),
            new FinancialInstitutionAddress('Belfius Bank', new UnstructuredPostalAddress('Pachecolaan 44', '1000 Brussel', 'BE'))
        );
        $transaction8->setIntermediaryAgent(new BIC('SWHQBEBB'));

        $transaction9 = new SEPACreditTransfer(
            'instr-009',
            'e2e-009',
            new Money\EUR(10000), // EUR 100.00
            'Bau Muster AG',
            new UnstructuredPostalAddress('Musterallee 11', '10115 Berlin', 'DE'),
            new IBAN('DE22 2665 0001 9311 6826 12'),
            new BIC('NOLADE21EMS')
        );

        $transaction10 = new ISRCreditTransfer(
            'instr-010',
            'e2e-010',
            new Money\CHF(20000), // CHF 200.00
            new ISRParticipant('01-1439-8'),
            '210000000003139471430009017'
        );

        $transaction11 = new ISRCreditTransfer(
            'instr-011',
            'e2e-011',
            new Money\CHF(20000), // CHF 200.00
            new ISRParticipant('01-95106-8'),
            '6019701803969733825'
        );
        $transaction11->setCreditorDetails(
            'Fritz Bischof',
            new StructuredPostalAddress('Dorfstrasse', '17', '9911', 'Musterwald')
        );

        $transaction12 = new IS1CreditTransfer(
            'instr-012',
            'e2e-012',
            new Money\CHF(50000), // CHF 500.00
            'Meier & Söhne AG',
            new StructuredPostalAddress('Dorfstrasse', '17', '9911', 'Musterwald'),
            new PostalAccount('60-9-9')
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
        $payment2->addTransaction($transaction8);

        $payment3 = new SEPAPaymentInformation('payment-003', 'InnoMuster AG', new BIC('POFICHBEXXX'), new IBAN('CH6309000000250097798'));
        $payment3->addTransaction($transaction9);

        $payment4 = new PaymentInformation('payment-004', 'InnoMuster AG', new BIC('POFICHBEXXX'), new IBAN('CH6309000000250097798'));
        $payment4->addTransaction($transaction10);
        $payment4->addTransaction($transaction11);

        $payment5 = new PaymentInformation('payment-005', 'InnoMuster AG', new BIC('POFICHBEXXX'), new IBAN('CH6309000000250097798'));
        $payment5->setCategoryPurpose(new CategoryPurposeCode('SALA'));
        $payment5->addTransaction($transaction12);

        $message = new CustomerCreditTransfer('message-001', 'InnoMuster AG');
        $message->addPayment($payment);
        $message->addPayment($payment2);
        $message->addPayment($payment3);
        $message->addPayment($payment4);
        $message->addPayment($payment5);

        return $message;
    }

    public function testGroupHeader()
    {
        $xml = $this->buildMessage()->asXml();

        $doc = new \DOMDocument();
        $doc->loadXML($xml);
        $xpath = new \DOMXPath($doc);
        $xpath->registerNamespace('pain001', self::SCHEMA);

        $nbOfTxs = $xpath->evaluate('string(//pain001:GrpHdr/pain001:NbOfTxs)');
        $this->assertEquals('12', $nbOfTxs);

        $ctrlSum = $xpath->evaluate('string(//pain001:GrpHdr/pain001:CtrlSum)');
        $this->assertEquals('4210.00', $ctrlSum);
    }

    public function testSchemaValidation()
    {
        $xml = $this->buildMessage()->asXml();
        $schemaPath = __DIR__.'/../../../../'.self::SCHEMA_PATH;

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

    public function testGetPaymentCount()
    {
        $message = $this->buildMessage();

        $this->assertSame(5, $message->getPaymentCount());
    }
}
