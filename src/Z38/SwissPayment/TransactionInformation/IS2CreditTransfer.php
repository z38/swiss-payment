<?php

namespace Z38\SwissPayment\TransactionInformation;

use Money\Money;
use Z38\SwissPayment\IBAN;
use Z38\SwissPayment\PostalAddress;
use Z38\SwissPayment\PostalAccount;

/**
 * IS2CreditTransfer contains all the information about a IS 2-stage (type 2.2) transaction.
 */
class IS2CreditTransfer extends CreditTransfer
{
    protected $creditorIBAN;
    protected $creditorAgentName;
    protected $creditorAgentPostal;

    /**
     * {@inheritdoc}
     * @param IBAN          $creditorIBAN        IBAN of the creditor
     * @param string        $creditorAgentName   Name of the creditor's financial institution
     * @param PostalAccount $creditorAgentPostal Postal account of the creditor's financial institution
     *
     * @throws \InvalidArgumentException When the instructed amount is not in Swiss Francs.
     */
    public function __construct($instructionId, $endToEndId, Money $amount, $creditorName, PostalAddress $creditorAddress, IBAN $creditorIBAN, $creditorAgentName, PostalAccount $creditorAgentPostal)
    {
        parent::__construct($instructionId, $endToEndId, $amount, $creditorName, $creditorAddress);

        $this->assertCurrency('CHF');

        $this->creditorIBAN = $creditorIBAN;
        $this->creditorAgentName = $creditorAgentName;
        $this->creditorAgentPostal = $creditorAgentPostal;
    }

    /**
     * {@inheritdoc}
     */
    public function asDom(\DOMDocument $doc)
    {
        $root = $this->buildHeader($doc, 'CH03');

        $creditorAgent = $doc->createElement('CdtrAgt');
        $creditorAgentId = $doc->createElement('FinInstnId');
        $creditorAgentId->appendChild($doc->createElement('Nm', $this->creditorAgentName));
        $creditorAgentIdOther = $doc->createElement('Othr');
        $creditorAgentIdOther->appendChild($doc->createElement('Id', $this->creditorAgentPostal->format()));
        $creditorAgentId->appendChild($creditorAgentIdOther);
        $creditorAgent->appendChild($creditorAgentId);
        $root->appendChild($creditorAgent);

        $root->appendChild($this->buildCreditor($doc));

        $creditorAccount = $doc->createElement('CdtrAcct');
        $creditorAccountId = $doc->createElement('Id');
        $creditorAccountId->appendChild($doc->createElement('IBAN', $this->creditorIBAN->format(false)));
        $creditorAccount->appendChild($creditorAccountId);
        $root->appendChild($creditorAccount);

        if ($this->hasRemittanceInformation()) {
            $root->appendChild($this->buildRemittanceInformation($doc));
        }

        return $root;
    }
}
