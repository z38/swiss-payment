<?php

namespace Z38\SwissPayment\PaymentInformation;

use Z38\SwissPayment\BIC;
use Z38\SwissPayment\IBAN;
use Z38\SwissPayment\Money;
use Z38\SwissPayment\TransactionInformation\CreditTransfer;

class PaymentInformation
{
    protected $id;
    protected $transactions;
    protected $batchBooking;
    protected $executionDate;
    protected $debtorName;
    protected $debtorBIC;
    protected $debtorIBAN;

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

    public function addTransaction(CreditTransfer $transaction)
    {
        $this->transactions[] = $transaction;
    }

    public function getTransactionCount()
    {
        return count($this->transactions);
    }

    public function getTransactionSum()
    {
        $sum = new Money\CHF(0);

        foreach ($this->transactions as $transaction) {
            $sum = $sum->plus($transaction->getAmount());
        }

        return $sum;
    }

    public function setExecutionDate(\DateTime $executionDate)
    {
        $this->executionDate = $executionDate;

        return $this;
    }

    public function setBatchBooking($batchBooking)
    {
        $this->batchBooking = boolval($batchBooking);

        return $this;
    }

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
