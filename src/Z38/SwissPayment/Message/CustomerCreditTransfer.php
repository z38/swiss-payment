<?php

namespace Z38\SwissPayment\Message;

use Z38\SwissPayment\Money;
use Z38\SwissPayment\PaymentInformation\PaymentInformation;

class CustomerCreditTransfer extends AbstractMessage
{
    protected $id;
    protected $initiatingParty;
    protected $payments;
    protected $creationTime;

    public function __construct($id, $initiatingParty)
    {
        $this->id = $id;
        $this->initiatingParty = $initiatingParty;
        $this->transactions = array();
        $this->creationTime = new \DateTime();
    }

    public function setCreationTime(\DateTime $creationTime)
    {
        $this->creationTime = $creationTime;
    }

    public function addPayment(PaymentInformation $payment)
    {
        $this->payments[] = $payment;
    }

    protected function getSchemaName()
    {
        return 'pain.001.001.03.ch.02.xsd';
    }

    protected function buildDom(\DOMDocument $doc)
    {
        $transactionCount = 0;
        $transactionSum = new Money\CHF(0);
        foreach ($this->payments as $payment) {
            $transactionCount += $payment->getTransactionCount();
            $transactionSum = $transactionSum->plus($payment->getTransactionSum());
        }

        $root = $doc->createElement('CstmrCdtTrfInitn');
        $header = $doc->createElement('GrpHdr');
        $header->appendChild($doc->createElement('MsgId', $this->id));
        $header->appendChild($doc->createElement('CreDtTm', $this->creationTime->format('Y-m-d\TH:i:sP')));
        $header->appendChild($doc->createElement('NbOfTxs', $transactionCount));
        $header->appendChild($doc->createElement('CtrlSum', $transactionSum->format()));
        $initgParty = $doc->createElement('InitgPty');
        $initgParty->appendChild($doc->createElement('Nm', $this->initiatingParty));
        $initgParty->appendChild($this->buildContactDetails($doc));
        $header->appendChild($initgParty);
        $root->appendChild($header);

        foreach ($this->payments as $payment) {
            $root->appendChild($payment->asDom($doc));
        }

        return $root;
    }
}
