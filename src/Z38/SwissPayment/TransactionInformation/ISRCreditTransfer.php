<?php

namespace Z38\SwissPayment\TransactionInformation;

use DOMDocument;
use LogicException;
use Z38\SwissPayment\Money;
use Z38\SwissPayment\PaymentInformation\PaymentInformation;
use Z38\SwissPayment\PostalAccount;

/**
 * ISRCreditTransfer contains all the information about a ISR (type 1) transaction.
 */
class ISRCreditTransfer extends CreditTransfer
{
    /**
     * @var PostalAccount
     */
    protected $creditorAccount;

    /**
     * @var string
     */
    protected $creditorReference;

    /**
     * {@inheritdoc}
     *
     * @param PostalAccount $creditorAccount   Postal account of the creditor
     * @param string        $creditorReference Creditor reference information from remittance information
     *
     * @throws \InvalidArgumentException. An InvalidArgumentException is thrown if amount is not EUR or CHF
     */
    public function __construct($instructionId, $endToEndId, Money\Money $amount, PostalAccount $creditorAccount, $creditorReference)
    {
        if (false === $amount instanceof Money\EUR && false === $amount instanceof Money\CHF) {
            throw new \InvalidArgumentException(sprintf(
                'Amount must be an instance of Z38\SwissPayment\Money\EUR or Z38\SwissPayment\Money\CHF. Instance of %s given.',
                get_class($amount)
            ));
        }

        $this->instructionId = (string) $instructionId;
        $this->endToEndId = (string) $endToEndId;
        $this->amount = $amount;
        $this->creditorAccount = $creditorAccount;
        $this->creditorReference = (string) $creditorReference;
    }

    /**
     * {@inheritdoc}
     */
    public function setRemittanceInformation($remittanceInformation)
    {
        throw new LogicException('ISR payments are not able to store unstructured remittance information.');
    }

    /**
     * {@inheritdoc}
     */
    public function asDom(DOMDocument $doc, PaymentInformation $paymentInformation)
    {
        $root = $this->buildHeader($doc, $paymentInformation, 'CH01');

        $creditorAccount = $doc->createElement('CdtrAcct');
        $creditorAccount->appendChild($this->creditorAccount->asDom($doc));
        $root->appendChild($creditorAccount);

        $root->appendChild($this->buildRemittanceInformation($doc));

        return $root;
    }

    /**
     * {@inheritdoc}
     */
    protected function buildRemittanceInformation(\DOMDocument $doc)
    {
        $remittanceInformation = $doc->createElement('RmtInf');

        $structured = $doc->createElement('Strd');
        $remittanceInformation->appendChild($structured);

        $creditorReferenceInformation = $doc->createElement('CdtrRefInf');
        $structured->appendChild($creditorReferenceInformation);

        $creditorReferenceInformation->appendChild($doc->createElement('Ref', $this->creditorReference));

        return $remittanceInformation;
    }

    /**
     * {@inheritdoc}
     */
    protected function hasRemittanceInformation()
    {
        return true;
    }
}
