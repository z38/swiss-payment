<?php

namespace Z38\SwissPayment\TransactionInformation;

use Z38\SwissPayment\BIC;
use Z38\SwissPayment\IBAN;
use Z38\SwissPayment\PostalAddressInterface;
use Z38\SwissPayment\Money;

/**
 * SEPACreditTransfer contains all the information about a foreign SEPA (type 5) transaction.
 */
class SEPACreditTransfer extends CreditTransfer
{
    /**
     * @var IBAN
     */
    protected $creditorIBAN;

    /**
     * @var BIC
     */
    protected $creditorAgentBIC;

    /**
     * {@inheritdoc}
     *
     * @param IBAN $creditorIBAN     IBAN of the creditor
     * @param BIC  $creditorAgentBIC BIC of the creditor's financial institution
     */
    public function __construct($instructionId, $endToEndId, Money\EUR $amount, $creditorName, PostalAddressInterface $creditorAddress, IBAN $creditorIBAN, BIC $creditorAgentBIC)
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
        $root = $this->buildHeader($doc, null, 'SEPA');

        $creditorAgent = $doc->createElement('CdtrAgt');

        $creditorAgent->appendChild($this->creditorAgentBIC->asDom($doc));
        $root->appendChild($creditorAgent);

        $root->appendChild($this->buildCreditor($doc));

        $creditorAccount = $doc->createElement('CdtrAcct');
        $creditorAccountId = $doc->createElement('Id');
        $creditorAccountId->appendChild($doc->createElement('IBAN', $this->creditorIBAN->normalize()));
        $creditorAccount->appendChild($creditorAccountId);
        $root->appendChild($creditorAccount);

        if ($this->hasRemittanceInformation()) {
            $root->appendChild($this->buildRemittanceInformation($doc));
        }

        return $root;
    }
}
