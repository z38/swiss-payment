<?php

namespace Z38\SwissPayment\TransactionInformation;

use Money\Money;
use Z38\SwissPayment\PostalAddress;
use Z38\SwissPayment\PostalAccount;

/**
 * IS1CreditTransfer contains all the information about a IS 1-stage (type 2.1) transaction.
 */
class IS1CreditTransfer extends CreditTransfer
{
    protected $creditorAccount;

    /**
     * {@inheritdoc}
     * @param PostalAccount $creditorAccount Postal account of the creditor
     *
     * @throws \InvalidArgumentException When the instructed amount is not in Swiss Francs.
     */
    public function __construct($instructionId, $endToEndId, Money $amount, $creditorName, PostalAddress $creditorAddress, PostalAccount $creditorAccount)
    {
        parent::__construct($instructionId, $endToEndId, $amount, $creditorName, $creditorAddress);

        $this->assertCurrency('CHF');

        $this->creditorAccount = $creditorAccount;
    }

    /**
     * {@inheritdoc}
     */
    public function asDom(\DOMDocument $doc)
    {
        $root = $this->buildHeader($doc, 'CH02');

        $root->appendChild($this->buildCreditor($doc));

        $creditorAccount = $doc->createElement('CdtrAcct');
        $creditorAccountId = $doc->createElement('Id');
        $creditorAccountIdOther = $doc->createElement('Othr');
        $creditorAccountIdOther->appendChild($doc->createElement('Id', $this->creditorAccount->format()));
        $creditorAccountId->appendChild($creditorAccountIdOther);
        $creditorAccount->appendChild($creditorAccountId);
        $root->appendChild($creditorAccount);

        if ($this->hasRemittanceInformation()) {
            $root->appendChild($this->buildRemittanceInformation($doc));
        }

        return $root;
    }
}
