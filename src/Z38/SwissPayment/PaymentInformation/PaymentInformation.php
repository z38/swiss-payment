<?php

namespace Z38\SwissPayment\PaymentInformation;

use Z38\SwissPayment\BIC;
use Z38\SwissPayment\FinancialInstitutionInterface;
use Z38\SwissPayment\IBAN;
use Z38\SwissPayment\IID;
use Z38\SwissPayment\Money;
use Z38\SwissPayment\TransactionInformation\CreditTransfer;

/**
 * PaymentInformation contains a group of transactions as well as details about the debtor
 */
class PaymentInformation
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var array
     */
    protected $transactions;

    /**
     * @var bool
     */
    protected $batchBooking;

    /**
     * @var string|null
     */
    protected $serviceLevel;

    /**
     * @var string|null
     */
    protected $localInstrument;

    /**
     * @var \DateTime
     */
    protected $executionDate;

    /**
     * @var string
     */
    protected $debtorName;

    /**
     * @var FinancialInstitutionInterface
     */
    protected $debtorAgent;

    /**
     * @var IBAN
     */
    protected $debtorIBAN;

    /**
     * Constructor
     *
     * @param string  $id          Identifier of this group (should be unique within a message)
     * @param string  $debtorName  Name of the debtor
     * @param BIC|IID $debtorAgent BIC or IID of the debtor's financial institution
     * @param IBAN    $debtorIBAN  IBAN of the debtor's account
     */
    public function __construct($id, $debtorName, FinancialInstitutionInterface $debtorAgent, IBAN $debtorIBAN)
    {
        if (!$debtorAgent instanceof BIC && !$debtorAgent instanceof IID) {
            throw new \InvalidArgumentException('The debtor agent must be an instance of BIC or IID.');
        }

        $this->id = (string) $id;
        $this->transactions = array();
        $this->batchBooking = true;
        $this->executionDate = new \DateTime();
        $this->debtorName = (string) $debtorName;
        $this->debtorAgent = $debtorAgent;
        $this->debtorIBAN = $debtorIBAN;
    }

    /**
     * Adds a single transaction to this payment
     *
     * @param CreditTransfer $transaction The transaction to be added
     *
     * @return PaymentInformation This payment instruction
     */
    public function addTransaction(CreditTransfer $transaction)
    {
        $this->transactions[] = $transaction;

        return $this;
    }

    /**
     * Gets the number of transactions
     *
     * @return int Number of transactions
     */
    public function getTransactionCount()
    {
        return count($this->transactions);
    }

    /**
     * Gets the sum of transactions
     *
     * @return Money\Mixed Sum of transactions
     */
    public function getTransactionSum()
    {
        $sum = new Money\Mixed(0);

        foreach ($this->transactions as $transaction) {
            $sum = $sum->plus($transaction->getAmount());
        }

        return $sum;
    }

    /**
     * Sets the required execution date.
     * Where appropriate, the value data is automatically modified to the next possible banking/Post Office working day.
     *
     * @param \DateTime $executionDate
     *
     * @return PaymentInformation This payment instruction
     */
    public function setExecutionDate(\DateTime $executionDate)
    {
        $this->executionDate = $executionDate;

        return $this;
    }

    /**
     * Sets the batch booking option.
     * It is recommended that one payment instruction is created for each currency transferred.
     *
     * @param bool $batchBooking
     *
     * @return PaymentInformation This payment instruction
     */
    public function setBatchBooking($batchBooking)
    {
        $this->batchBooking = boolval($batchBooking);

        return $this;
    }

    /**
     * Checks whether the payment type information is included on B- or C-level
     *
     * @return bool true if it is included on B-level
     */
    public function hasPaymentTypeInformation()
    {
        return ($this->localInstrument !== null || $this->serviceLevel !== null);
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
     * Builds a DOM tree of this payment instruction
     *
     * @param \DOMDocument $doc
     *
     * @return \DOMElement The built DOM tree
     */
    public function asDom(\DOMDocument $doc)
    {
        $root = $doc->createElement('PmtInf');

        $root->appendChild($doc->createElement('PmtInfId', $this->id));
        $root->appendChild($doc->createElement('PmtMtd', 'TRF'));
        $root->appendChild($doc->createElement('BtchBookg', ($this->batchBooking ? 'true' : 'false')));

        if ($this->hasPaymentTypeInformation()) {
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

        $root->appendChild($doc->createElement('ReqdExctnDt', $this->executionDate->format('Y-m-d')));

        $debtor = $doc->createElement('Dbtr');
        $debtor->appendChild($doc->createElement('Nm', $this->debtorName));
        $root->appendChild($debtor);

        $debtorAccount = $doc->createElement('DbtrAcct');
        $debtorAccountId = $doc->createElement('Id');
        $debtorAccountId->appendChild($doc->createElement('IBAN', $this->debtorIBAN->normalize()));
        $debtorAccount->appendChild($debtorAccountId);
        $root->appendChild($debtorAccount);

        $debtorAgent = $doc->createElement('DbtrAgt');
        $debtorAgent->appendChild($this->debtorAgent->asDom($doc));
        $root->appendChild($debtorAgent);

        foreach ($this->transactions as $transaction) {
            $root->appendChild($transaction->asDom($doc, $this));
        }

        return $root;
    }
}
