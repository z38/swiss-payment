<?php

namespace Z38\SwissPayment\Tests;

use Z38\SwissPayment\BIC;
use Z38\SwissPayment\IBAN;
use Z38\SwissPayment\IntermediarySwift;
use Z38\SwissPayment\Money;
use Z38\SwissPayment\StructuredPostalAddress;
use Z38\SwissPayment\TransactionInformation\SEPACreditTransfer;

/**
 * @coversDefaultClass \Z38\SwissPayment\TransactionInformation\BankCreditTransfer
 */
class SEPACreditTransferTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testValid()
    {
        $transfer = new SEPACreditTransfer(
            'id01',
            '',
            new Money\EUR(1000),
            'Im Theowner',
            new StructuredPostalAddress(
                'mystreet',
                '123',
                '',
                'thiscity',
                'BE'
            ),
            new IBAN('BE17377022128021'),
            new BIC('BBRUBEBB')
        );
    }

    public function testIntermediarySwift()
    {
        $intermediarySwift = new IntermediarySwift(new BIC('RABONL2U'));
        $transfer = new SEPACreditTransfer(
            'id01',
            '',
            new Money\EUR(1000),
            'Im Theowner',
            new StructuredPostalAddress(
                'mystreet',
                '123',
                '',
                'thiscity',
                'BE'
            ),
            new IBAN('BE17377022128021'),
            new BIC('BBRUBEBB'),
            $intermediarySwift
        );
        $this->assertEquals($intermediarySwift, $transfer->getIntermediarySwift());
    }
}
