<?php

namespace Z38\SwissPayment\TransactionInformation;

use DOMDocument;
use InvalidArgumentException;
use LogicException;
use Z38\SwissPayment\ISRParticipant;
use Z38\SwissPayment\Money;
use Z38\SwissPayment\PaymentInformation\PaymentInformation;
use Z38\SwissPayment\RemittanceInformation\ISRReferenceInformation;
use Z38\SwissPayment\RemittanceInformation\RemittanceInformation;

/**
 * ISRCreditTransfer contains all the information about a ISR (type 1) transaction.
 */
class ISRCreditTransfer extends CreditTransfer
{
    /**
     * @var ISRParticipant
     */
    protected $creditorAccount;

    /**
     * {@inheritdoc}
     *
     * @param ISRParticipant $creditorAccount   ISR participation number of the creditor
     * @param string         $creditorReference ISR reference number
     *
     * @throws InvalidArgumentException When the amount is not in EUR or CHF.
     */
    public function __construct($instructionId, $endToEndId, Money\Money $amount, ISRParticipant $creditorAccount, $creditorReference)
    {
        if (!$amount instanceof Money\EUR && !$amount instanceof Money\CHF) {
            throw new InvalidArgumentException(sprintf(
                'The amount must be an instance of Z38\SwissPayment\Money\EUR or Z38\SwissPayment\Money\CHF (instance of %s given).',
                get_class($amount)
            ));
        }

        $this->instructionId = (string) $instructionId;
        $this->endToEndId = (string) $endToEndId;
        $this->amount = $amount;
        $this->creditorAccount = $creditorAccount;
        $this->localInstrument = 'CH01';

        parent::setRemittanceInformation(new ISRReferenceInformation($creditorReference));
    }

    /**
     * {@inheritdoc}
     */
    public function setRemittanceInformation(RemittanceInformation $remittanceInformation)
    {
        throw new LogicException('ISR payments are not able to store additional remittance information.');
    }

    /**
     * {@inheritdoc}
     */
    public function asDom(DOMDocument $doc, PaymentInformation $paymentInformation)
    {
        $root = $this->buildHeader($doc, $paymentInformation);

        $creditorAccount = $doc->createElement('CdtrAcct');
        $creditorAccount->appendChild($this->creditorAccount->asDom($doc));
        $root->appendChild($creditorAccount);

        $this->appendPurpose($doc, $root);

        $this->appendRemittanceInformation($doc, $root);

        return $root;
    }
}
