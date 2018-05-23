<?php

namespace Z38\SwissPayment\TransactionInformation;

use Z38\SwissPayment\Money\Money;
use Z38\SwissPayment\PaymentInformation\PaymentInformation;
use Z38\SwissPayment\PostalAddressInterface;
use Z38\SwissPayment\Text;

/**
 * CreditTransfer contains all the information about the beneficiary and further information about the transaction.
 */
abstract class CreditTransfer
{
    /**
     * @var string
     */
    protected $instructionId;

    /**
     * @var string
     */
    protected $endToEndId;

    /**
     * @var string
     */
    protected $creditorName;

    /**
     * @var PostalAddressInterface
     */
    protected $creditorAddress;

    /**
     * @var Money
     */
    protected $amount;

    /**
     * @var string|null
     */
    protected $localInstrument;

    /**
     * @var string|null
     */
    protected $serviceLevel;

    /**
     * @var PurposeCode|null
     */
    protected $purpose;

    /**
     * @var string|null
     */
    protected $remittanceInformation;

    /**
     * Constructor
     *
     * @param string                 $instructionId   Identifier of the instruction (should be unique within the message)
     * @param string                 $endToEndId      End-To-End Identifier of the instruction (passed unchanged along the complete processing chain)
     * @param Money                  $amount          Amount of money to be transferred
     * @param string                 $creditorName    Name of the creditor
     * @param PostalAddressInterface $creditorAddress Address of the creditor
     *
     * @throws \InvalidArgumentException When any of the inputs contain invalid characters or are too long.
     */
    public function __construct($instructionId, $endToEndId, Money $amount, $creditorName, PostalAddressInterface $creditorAddress)
    {
        $this->instructionId = Text::assertIdentifier($instructionId);
        $this->endToEndId = Text::assertIdentifier($endToEndId);
        $this->amount = $amount;
        $this->creditorName = Text::assert($creditorName, 70);
        $this->creditorAddress = $creditorAddress;
    }

    /**
     * Gets the local instrument
     *
     * @return string|null The local instrument
     */
    public function getLocalInstrument()
    {
        return $this->localInstrument;
    }

    /**
     * Gets the service level
     *
     * @return string|null The service level
     */
    public function getServiceLevel()
    {
        return $this->serviceLevel;
    }

    /**
     * Sets the purpose of the payment
     *
     * @param PurposeCode $purpose The purpose
     *
     * @return CreditTransfer This credit transfer
     */
    public function setPurpose(PurposeCode $purpose)
    {
        $this->purpose = $purpose;

        return $this;
    }

    /**
     * Sets the unstructured remittance information
     *
     * @param string|null $remittanceInformation
     *
     * @return CreditTransfer This credit transfer
     *
     * @throws \InvalidArgumentException When the information contains invalid characters or is too long.
     */
    public function setRemittanceInformation($remittanceInformation)
    {
        $this->remittanceInformation = Text::assertOptional($remittanceInformation, 140);

        return $this;
    }

    /**
     * Gets the instructed amount of this transaction
     *
     * @return Money The instructed amount
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Builds a DOM tree of this transaction
     *
     * @param \DOMDocument       $doc
     * @param PaymentInformation $paymentInformation Information on B-level
     *
     * @return \DOMElement The built DOM tree
     */
    abstract public function asDom(\DOMDocument $doc, PaymentInformation $paymentInformation);

    /**
     * Builds a DOM tree of this transaction and adds header nodes
     *
     * @param \DOMDocument       $doc
     * @param PaymentInformation $paymentInformation The corresponding B-level element
     *
     * @return \DOMNode The built DOM node
     */
    protected function buildHeader(\DOMDocument $doc, PaymentInformation $paymentInformation)
    {
        $root = $doc->createElement('CdtTrfTxInf');

        $id = $doc->createElement('PmtId');
        $id->appendChild(Text::xml($doc, 'InstrId', $this->instructionId));
        $id->appendChild(Text::xml($doc, 'EndToEndId', $this->endToEndId));
        $root->appendChild($id);

        if (!$paymentInformation->hasPaymentTypeInformation() && ($this->localInstrument !== null || $this->serviceLevel !== null)) {
            $paymentType = $doc->createElement('PmtTpInf');
            if ($this->localInstrument !== null) {
                $localInstrumentNode = $doc->createElement('LclInstrm');
                $localInstrumentNode->appendChild($doc->createElement('Prtry', $this->localInstrument));
                $paymentType->appendChild($localInstrumentNode);
            }
            if ($this->serviceLevel !== null) {
                $serviceLevelNode = $doc->createElement('SvcLvl');
                $serviceLevelNode->appendChild($doc->createElement('Cd', $this->serviceLevel));
                $paymentType->appendChild($serviceLevelNode);
            }
            $root->appendChild($paymentType);
        }

        $amount = $doc->createElement('Amt');
        $instdAmount = $doc->createElement('InstdAmt', $this->amount->format());
        $instdAmount->setAttribute('Ccy', $this->amount->getCurrency());
        $amount->appendChild($instdAmount);
        $root->appendChild($amount);

        return $root;
    }

    /**
     * Builds a DOM node of the Creditor field
     *
     * @param \DOMDocument $doc
     *
     * @return \DOMNode The built DOM node
     */
    protected function buildCreditor(\DOMDocument $doc)
    {
        $creditor = $doc->createElement('Cdtr');
        $creditor->appendChild(Text::xml($doc, 'Nm', $this->creditorName));
        if (!is_null($this->creditorAddress->asDom($doc))) {
            $creditor->appendChild($this->creditorAddress->asDom($doc));
        }

        return $creditor;
    }

    /**
     * Appends the purpose to the transaction
     *
     * @param \DOMDocument $doc
     * @param \DOMElement  $transaction
     */
    protected function appendPurpose(\DOMDocument $doc, \DOMElement $transaction)
    {
        if ($this->purpose !== null) {
            $purposeNode = $doc->createElement('Purp');
            $purposeNode->appendChild($this->purpose->asDom($doc));
            $transaction->appendChild($purposeNode);
        }
    }

    /**
     * Appends the remittance information to the transaction
     *
     * @param \DOMDocument $doc
     * @param \DOMElement  $transaction
     */
    protected function appendRemittanceInformation(\DOMDocument $doc, \DOMElement $transaction)
    {
        if (!empty($this->remittanceInformation)) {
            $remittanceNode = $doc->createElement('RmtInf');
            $remittanceNode->appendChild(Text::xml($doc, 'Ustrd', $this->remittanceInformation));
            $transaction->appendChild($remittanceNode);
        }
    }
}
