<?php

namespace Z38\SwissPayment\Message;

use Z38\SwissPayment\Money;
use Z38\SwissPayment\PaymentInformation\PaymentInformation;
use Z38\SwissPayment\Text;

/**
 * CustomerCreditTransfer represents a Customer Credit Transfer Initiation (pain.001) message
 */
class CustomerCreditTransfer extends AbstractMessage
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $initiatingParty;

    /**
     * @var array
     */
    protected $payments;

    /**
     * @var \DateTime
     */
    protected $creationTime;

    /**
     * Constructor
     *
     * @param string $id              Identifier of the message (should usually be unique over a period of at least 90 days)
     * @param string $initiatingParty Name of the initiating party
     *
     * @throws \InvalidArgumentException When any of the inputs contain invalid characters or are too long.
     */
    public function __construct($id, $initiatingParty)
    {
        $this->id = Text::assertIdentifier($id);
        $this->initiatingParty = Text::assert($initiatingParty, 70);
        $this->payments = [];
        $this->creationTime = new \DateTime();
    }

    /**
     * Manually sets the creation time
     *
     * @param \DateTime $creationTime The desired creation time
     *
     * @return CustomerCreditTransfer This message
     */
    public function setCreationTime(\DateTime $creationTime)
    {
        $this->creationTime = $creationTime;

        return $this;
    }

    /**
     * Adds a payment instruction
     *
     * @param PaymentInformation $payment The payment to be added
     *
     * @return CustomerCreditTransfer This message
     */
    public function addPayment(PaymentInformation $payment)
    {
        $this->payments[] = $payment;

        return $this;
    }

    /**
     * Gets the number of payments
     *
     * @return int Number of payments
     */
    public function getPaymentCount()
    {
        return count($this->payments);
    }

    /**
     * {@inheritdoc}
     */
    protected function getSchemaName()
    {
        return 'http://www.six-interbank-clearing.com/de/pain.001.001.03.ch.02.xsd';
    }

    /**
     * {@inheritdoc}
     */
    protected function getSchemaLocation()
    {
        return 'pain.001.001.03.ch.02.xsd';
    }

    /**
     * {@inheritdoc}
     */
    protected function buildDom(\DOMDocument $doc)
    {
        $transactionCount = 0;
        $transactionSum = new Money\MixedMoney(0);
        foreach ($this->payments as $payment) {
            $transactionCount += $payment->getTransactionCount();
            $transactionSum = $transactionSum->plus($payment->getTransactionSum());
        }

        $root = $doc->createElement('CstmrCdtTrfInitn');
        $header = $doc->createElement('GrpHdr');
        $header->appendChild(Text::xml($doc, 'MsgId', $this->id));
        $header->appendChild(Text::xml($doc, 'CreDtTm', $this->creationTime->format('Y-m-d\TH:i:sP')));
        $header->appendChild(Text::xml($doc, 'NbOfTxs', $transactionCount));
        $header->appendChild(Text::xml($doc, 'CtrlSum', $transactionSum->format()));
        $initgParty = $doc->createElement('InitgPty');
        $initgParty->appendChild(Text::xml($doc, 'Nm', $this->initiatingParty));
        $initgParty->appendChild($this->buildContactDetails($doc));
        $header->appendChild($initgParty);
        $root->appendChild($header);

        foreach ($this->payments as $payment) {
            $root->appendChild($payment->asDom($doc));
        }

        return $root;
    }
}
