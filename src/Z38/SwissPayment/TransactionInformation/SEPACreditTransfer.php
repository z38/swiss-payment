<?php

namespace Z38\SwissPayment\TransactionInformation;

use Z38\SwissPayment\BIC;
use Z38\SwissPayment\IBAN;
use Z38\SwissPayment\Money;
use Z38\SwissPayment\PaymentInformation\PaymentInformation;
use Z38\SwissPayment\PostalAddressInterface;

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
     * @param IntermediarySwift $intermediarySwift
     */
    public function __construct($instructionId, $endToEndId, Money\EUR $amount, $creditorName, PostalAddressInterface $creditorAddress, IBAN $creditorIBAN, BIC $creditorAgentBIC, $intermediarySwift = null)
    {
        parent::__construct($instructionId, $endToEndId, $amount, $creditorName, $creditorAddress, $intermediarySwift);

        $this->creditorIBAN = $creditorIBAN;
        $this->creditorAgentBIC = $creditorAgentBIC;
    }

    /**
     * {@inheritdoc}
     */
    public function asDom(\DOMDocument $doc, PaymentInformation $paymentInformation)
    {
        $root = $this->buildHeader($doc, $paymentInformation, null, 'SEPA');

        $creditorAgent = $doc->createElement('CdtrAgt');
        $creditorAgent->appendChild($this->creditorAgentBIC->asDom($doc));
        $root->appendChild($creditorAgent);
        $root->appendChild($this->buildCreditor($doc));

        if ($this->intermediarySwift) {
            $intermediary = $doc->createElement('IntrmyAgt1');
            $intermediary->appendChild($this->intermediarySwift->asDom($doc));
            $root->appendChild($intermediary);
        }

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
