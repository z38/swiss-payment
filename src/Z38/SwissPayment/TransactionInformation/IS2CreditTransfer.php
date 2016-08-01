<?php

namespace Z38\SwissPayment\TransactionInformation;

use DOMDocument;
use Z38\SwissPayment\IBAN;
use Z38\SwissPayment\Money;
use Z38\SwissPayment\PaymentInformation\PaymentInformation;
use Z38\SwissPayment\PostalAccount;
use Z38\SwissPayment\PostalAddressInterface;

/**
 * IS2CreditTransfer contains all the information about a IS 2-stage (type 2.2) transaction.
 */
class IS2CreditTransfer extends CreditTransfer
{
    /**
     * @var IBAN
     */
    protected $creditorIBAN;

    /**
     * @var string
     */
    protected $creditorAgentName;

    /**
     * @var PostalAccount
     */
    protected $creditorAgentPostal;

    /**
     * {@inheritdoc}
     *
     * @param IBAN          $creditorIBAN        IBAN of the creditor
     * @param string        $creditorAgentName   Name of the creditor's financial institution
     * @param PostalAccount $creditorAgentPostal Postal account of the creditor's financial institution
     *
     * @throws \InvalidArgumentException. An InvalidArgumentException is thrown if amount is not EUR or CHF
     */
    public function __construct($instructionId, $endToEndId, Money\Money $amount, $creditorName, PostalAddressInterface $creditorAddress, IBAN $creditorIBAN, $creditorAgentName, PostalAccount $creditorAgentPostal)
    {
        if (false === $amount instanceof Money\EUR && false === $amount instanceof Money\CHF) {
            throw new \InvalidArgumentException(sprintf(
                'Amount must be an instance of Z38\SwissPayment\Money\EUR or Z38\SwissPayment\Money\CHF. Instance of %s given.',
                get_class($amount)
            ));
        }

        parent::__construct($instructionId, $endToEndId, $amount, $creditorName, $creditorAddress);

        $this->creditorIBAN = $creditorIBAN;
        $this->creditorAgentName = (string) $creditorAgentName;
        $this->creditorAgentPostal = $creditorAgentPostal;
    }

    /**
     * {@inheritdoc}
     */
    public function asDom(DOMDocument $doc, PaymentInformation $paymentInformation)
    {
        $root = $this->buildHeader($doc, $paymentInformation, 'CH03');

        $creditorAgent = $doc->createElement('CdtrAgt');
        $creditorAgentId = $doc->createElement('FinInstnId');
        $creditorAgentId->appendChild($doc->createElement('Nm', $this->creditorAgentName));
        $creditorAgentIdOther = $doc->createElement('Othr');
        $creditorAgentIdOther->appendChild($doc->createElement('Id', $this->creditorAgentPostal->format()));
        $creditorAgentId->appendChild($creditorAgentIdOther);
        $creditorAgent->appendChild($creditorAgentId);
        $root->appendChild($creditorAgent);

        $root->appendChild($this->buildCreditor($doc));

        $creditorAccount = $doc->createElement('CdtrAcct');
        $creditorAccount->appendChild($this->creditorIBAN->asDom($doc));
        $root->appendChild($creditorAccount);

        if ($this->hasRemittanceInformation()) {
            $root->appendChild($this->buildRemittanceInformation($doc));
        }

        return $root;
    }
}
