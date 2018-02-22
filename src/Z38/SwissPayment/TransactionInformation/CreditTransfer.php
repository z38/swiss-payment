<?php

namespace Z38\SwissPayment\TransactionInformation;

use Z38\SwissPayment\Money\Money;
use Z38\SwissPayment\PaymentInformation\PaymentInformation;
use Z38\SwissPayment\PostalAddressInterface;
use Z38\SwissPayment\RemittanceInformation\RemittanceInformation;

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
    protected $ultimateDebtorName;

    /**
     * @var PostalAddressInterface|null
     */
    protected $ultimateDebtorAddress;

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
     * @var RemittanceInformation|null
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
     */
    public function __construct($instructionId, $endToEndId, Money $amount, $creditorName, PostalAddressInterface $creditorAddress)
    {
        $this->instructionId = (string) $instructionId;
        $this->endToEndId = (string) $endToEndId;
        $this->amount = $amount;
        $this->creditorName = (string) $creditorName;
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
     * Gets the charge bearer
     *
     * @return string|null The charge bearer
     */
    public function getChargeBearer()
    {
        return null;
    }

    /**
     * Sets the ultimate debtor's name
     *
     * @param string|null $name
     *
     * @return self
     */
    public function setUltimateDebtorName($name)
    {
        $this->ultimateDebtorName = $name;

        return $this;
    }

    /**
     * Sets the ultimate debtor's address
     *
     * @param PostalAddressInterface|null $address
     *
     * @return self
     */
    public function setUltimateDebtorAddress(PostalAddressInterface $address)
    {
        $this->ultimateDebtorAddress = $address;

        return $this;
    }

    /**
     * Sets the remittance information
     *
     * @param RemittanceInformation|null $remittanceInformation
     *
     * @return self
     */
    public function setRemittanceInformation(RemittanceInformation $remittanceInformation)
    {
        $this->remittanceInformation = $remittanceInformation;

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
    protected function buildHeader(\DOMDocument $doc, PaymentInformation $paymentInformation, $chargeBearer = null)
    {
        $root = $doc->createElement('CdtTrfTxInf');

        $id = $doc->createElement('PmtId');
        $id->appendChild($doc->createElement('InstrId', $this->instructionId));
        $id->appendChild($doc->createElement('EndToEndId', $this->endToEndId));
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

        if ($chargeBearer = $this->getChargeBearer()) {
            $root->appendChild($doc->createElement('ChrgBr', $chargeBearer));
        }

        if (strlen($this->ultimateDebtorName) || $this->ultimateDebtorAddress !== null) {
            $ultimateDebtor = $doc->createElement('UltmtDbtr');
            if (strlen($this->ultimateDebtorName)) {
                $ultimateDebtor->appendChild($doc->createElement('Nm', $this->ultimateDebtorName));
            }
            if ($this->ultimateDebtorAddress !== null) {
                $ultimateDebtor->appendChild($this->ultimateDebtorAddress->asDom($doc));
            }
            $root->appendChild($ultimateDebtor);
        }

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
        $creditor->appendChild($doc->createElement('Nm', $this->creditorName));
        $creditor->appendChild($this->creditorAddress->asDom($doc));

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
        if ($this->remittanceInformation !== null) {
            $remittanceInformation = $doc->createElement('RmtInf');
            $remittanceInformation->appendChild($this->remittanceInformation->asDom($doc));
            $transaction->appendChild($remittanceInformation);
        }
    }
}
