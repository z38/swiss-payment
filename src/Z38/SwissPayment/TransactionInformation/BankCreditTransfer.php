<?php

namespace Z38\SwissPayment\TransactionInformation;

use Z38\SwissPayment\BIC;
use Z38\SwissPayment\IBAN;
use Z38\SwissPayment\PostalAddress;
use Z38\SwissPayment\Money;

/**
 * BankCreditTransfer contains all the information about a type 3 transaction.
 */
class BankCreditTransfer extends CreditTransfer
{
    protected $creditorIBAN;
    protected $creditorAgentBIC;

    /**
     * {@inheritdoc}
     * @param IBAN $creditorIBAN     IBAN of the creditor
     * @param BIC  $creditorAgentBIC BIC of the creditor's financial institution
     */
    public function __construct($instructionId, $endToEndId, Money\CHF $amount, $creditorName, PostalAddress $creditorAddress, IBAN $creditorIBAN, BIC $creditorAgentBIC)
    {
        parent::__construct($instructionId, $endToEndId, $amount, $creditorName, $creditorAddress);

        $this->creditorIBAN = $creditorIBAN;
        $this->creditorAgentBIC = $creditorAgentBIC;
    }

    /**
     * {@inheritdoc}
     */
    public function asDom(\DOMDocument $doc)
    {
        $root = $this->buildHeader($doc, null);

        $creditorAgent = $doc->createElement('CdtrAgt');
        $creditorAgentId = $doc->createElement('FinInstnId');
        $creditorAgentId->appendChild($doc->createElement('BIC', $this->creditorAgentBIC->format()));
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
