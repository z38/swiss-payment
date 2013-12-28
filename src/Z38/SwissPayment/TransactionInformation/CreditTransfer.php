<?php

namespace Z38\SwissPayment\TransactionInformation;

use Z38\SwissPayment\BIC;
use Z38\SwissPayment\IBAN;
use Z38\SwissPayment\PostalAddress;
use Z38\SwissPayment\Money;

/**
 * CreditTransfer contains all the information about the beneficiary and further information about the transaction.
 */
class CreditTransfer
{
    protected $instructionId;
    protected $endToEndId;
    protected $creditorName;
    protected $creditorAddress;
    protected $creditorIBAN;
    protected $creditorAgentBIC;
    protected $amount;
    protected $remittanceInformation;

    /**
     * Constructor
     *
     * @param string        $instructionId    Identifier of the instruction (should be unique within the message)
     * @param string        $endToEndId       End-To-End Identifier of the instruction (passed unchanged along the complete processing chain)
     * @param string        $creditorName     Name of the creditor
     * @param PostalAddress $creditorAddress  Address of the creditor
     * @param IBAN          $creditorIBAN     IBAN of the creditor
     * @param BIC           $creditorAgentBIC BIC of the creditor's financial institution
     * @param Money\CHF     $amount           Amount of money to be transferred
     */
    public function __construct($instructionId, $endToEndId, $creditorName, PostalAddress $creditorAddress, IBAN $creditorIBAN, BIC $creditorAgentBIC, Money\CHF $amount)
    {
        $this->instructionId = $instructionId;
        $this->endToEndId = $endToEndId;
        $this->creditorName = $creditorName;
        $this->creditorAddress = $creditorAddress;
        $this->creditorIBAN = $creditorIBAN;
        $this->creditorAgentBIC = $creditorAgentBIC;
        $this->amount = $amount;
        $this->remittanceInformation = null;
    }

    /**
     * Sets the unstructured remittance information
     *
     * @param string|null $remittanceInformation
     *
     * @return CreditTransfer This credit transfer
     */
    public function setRemittanceInformation($remittanceInformation)
    {
        $this->remittanceInformation = $remittanceInformation;

        return $this;
    }

    /**
     * Gets the instructed amount of this transaction
     *
     * @return Money\CHF The instructed amount
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Builds a DOM tree of this transaction
     *
     * @param \DOMDocument $doc
     *
     * @return \DOMElement The built DOM tree
     */
    public function asDom(\DOMDocument $doc)
    {
        $root = $doc->createElement('CdtTrfTxInf');

        $id = $doc->createElement('PmtId');
        $id->appendChild($doc->createElement('InstrId', $this->instructionId));
        $id->appendChild($doc->createElement('EndToEndId', $this->endToEndId));
        $root->appendChild($id);

        $amount = $doc->createElement('Amt');
        $instdAmount = $doc->createElement('InstdAmt', $this->amount->format());
        $instdAmount->setAttribute('Ccy', $this->amount->getCurrency());
        $amount->appendChild($instdAmount);
        $root->appendChild($amount);

        $creditorAgent = $doc->createElement('CdtrAgt');
        $creditorAgentId = $doc->createElement('FinInstnId');
        $creditorAgentId->appendChild($doc->createElement('BIC', $this->creditorAgentBIC->format()));
        $creditorAgent->appendChild($creditorAgentId);
        $root->appendChild($creditorAgent);

        $creditor = $doc->createElement('Cdtr');
        $creditor->appendChild($doc->createElement('Nm', $this->creditorName));
        $creditor->appendChild($this->creditorAddress->asDom($doc));
        $root->appendChild($creditor);

        $creditorAccount = $doc->createElement('CdtrAcct');
        $creditorAccountId = $doc->createElement('Id');
        $creditorAccountId->appendChild($doc->createElement('IBAN', $this->creditorIBAN->format(false)));
        $creditorAccount->appendChild($creditorAccountId);
        $root->appendChild($creditorAccount);

        if (!empty($this->remittanceInformation)) {
            $remittance = $doc->createElement('RmtInf');
            $remittance->appendChild($doc->createElement('Ustrd', $this->remittanceInformation));
            $root->appendChild($remittance);
        }

        return $root;
    }
}
