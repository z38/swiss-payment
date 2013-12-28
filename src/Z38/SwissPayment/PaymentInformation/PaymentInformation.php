<?php

namespace Z38\SwissPayment\PaymentInformation;

use Z38\SwissPayment\BIC;
use Z38\SwissPayment\IBAN;
use Z38\SwissPayment\Money;
use Z38\SwissPayment\TransactionInformation\CreditTransfer;

/**
 * PaymentInformation contains a group of transactions as well as details about the debtor
 */
class PaymentInformation
{
    protected $id;
    protected $transactions;
    protected $batchBooking;
    protected $executionDate;
    protected $debtorName;
    protected $debtorBIC;
    protected $debtorIBAN;

    /**
     * Constructor
     *
     * @param string $id         Identifier of this group (should be unique within a message)
     * @param string $debtorName Name of the debtor
     * @param BIC    $debtorBIC  BIC of the debtor's financial institution
     * @param IBAN   $debtorIBAN IBAN of the debtor's account
     */
    public function __construct($id, $debtorName, BIC $debtorBIC, IBAN $debtorIBAN)
    {
        $this->id = $id;
        $this->transactions = array();
        $this->batchBooking = true;
        $this->executionDate = new \DateTime();
        $this->debtorName = $debtorName;
        $this->debtorBIC = $debtorBIC;
        $this->debtorIBAN = $debtorIBAN;
    }

    /**
     * Adds a single transaction to this payment
     *
     * @param CreditTranfer $transaction The transaction to be added
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
     * @return Money\CHF Sum of transactions
     */
    public function getTransactionSum()
    {
        $sum = new Money\CHF(0);

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
        $root->appendChild($doc->createElement('ReqdExctnDt', $this->executionDate->format('Y-m-d')));

        $debtor = $doc->createElement('Dbtr');
        $debtor->appendChild($doc->createElement('Nm', $this->debtorName));
        $root->appendChild($debtor);

        $debtorAccount = $doc->createElement('DbtrAcct');
        $debtorAccountId = $doc->createElement('Id');
        $debtorAccountId->appendChild($doc->createElement('IBAN', $this->debtorIBAN->format(false)));
        $debtorAccount->appendChild($debtorAccountId);
        $root->appendChild($debtorAccount);

        $debtorAgent = $doc->createElement('DbtrAgt');
        $debtorAgentId = $doc->createElement('FinInstnId');
        $debtorAgentId->appendChild($doc->createElement('BIC', $this->debtorBIC->format()));
        $debtorAgent->appendChild($debtorAgentId);
        $root->appendChild($debtorAgent);

        foreach ($this->transactions as $transaction) {
            $root->appendChild($transaction->asDom($doc));
        }

        return $root;
    }
}
