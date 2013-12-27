<?php

namespace Z38\SwissPayment\Tests\Message;

use Z38\SwissPayment\Message\CustomerCreditTransfer;
use Z38\SwissPayment\TransactionInformation\CreditTransfer;
use Z38\SwissPayment\PaymentInformation\PaymentInformation;
use Z38\SwissPayment\BIC;
use Z38\SwissPayment\IBAN;
use Z38\SwissPayment\Money;
use Z38\SwissPayment\PostalAddress;
use Z38\SwissPayment\Tests\TestCase;

class CustomerCreditTransferTest extends TestCase
{
    const SCHEMA = 'pain.001.001.03.ch.02.xsd';
    const NS_URI_ROOT = 'http://www.six-interbank-clearing.com/de/';

    public function testTransaction()
    {
        $transaction = new CreditTransfer(
            'instr-001',
            'e2e-001',
            'Muster Transport AG',
            new PostalAddress('Wiesenweg', '14b', '8058', 'Zürich-Flughafen'),
            new IBAN('CH51 0022 5225 9529 1301 C'),
            new BIC('UBSWCHZH80A'),
            new Money\CHF(130000) // CHF 1300.00
        );

        $transaction2 = new CreditTransfer(
            'instr-002',
            'e2e-002',
            'Druckerei Muster GmbH',
            new PostalAddress('Gartenstrasse', '61', '3000', 'Bern'),
            new IBAN('CH03 0900 0000 3054 1118 8'),
            new BIC('POFICHBEXXX'),
            new Money\CHF(50000) // CHF 500.00
        );

        $payment = new PaymentInformation('payment-001', 'InnoMuster AG', new BIC('ZKBKCHZZ80A'), new IBAN('CH6600700110000204481'));
        $payment->addTransaction($transaction);
        $payment->addTransaction($transaction2);

        $message = new CustomerCreditTransfer('message-001', 'InnoMuster AG');
        $message->addPayment($payment);

        $xml = $message->asXml();

        $this->validateXml($xml, self::SCHEMA);

        $doc = new \DOMDocument();
        $doc->loadXML($xml);
        $xpath = new \DOMXPath($doc);
        $xpath->registerNamespace('pain001', self::NS_URI_ROOT.self::SCHEMA);

        $nbOfTxs = $xpath->query('//pain001:GrpHdr/pain001:NbOfTxs');
        $this->assertEquals('2', $nbOfTxs->item(0)->textContent);

        $ctrlSum = $xpath->query('//pain001:GrpHdr/pain001:CtrlSum');
        $this->assertEquals('1800.00', $ctrlSum->item(0)->textContent);
    }

    protected function validateXml($xml, $schema)
    {
        $schemaPath = __DIR__.'/../../../../'.$schema;

        $doc = new \DOMDocument();
        $doc->loadXML($xml);

        libxml_use_internal_errors(true);
        $doc->schemaValidate($schemaPath);
        $valid = $doc->schemaValidate($schemaPath);
        foreach (libxml_get_errors() as $error) {
            $this->fail($error->message);
        }
        $this->assertTrue($valid);
        libxml_clear_errors();
        libxml_use_internal_errors(false);
    }
}
