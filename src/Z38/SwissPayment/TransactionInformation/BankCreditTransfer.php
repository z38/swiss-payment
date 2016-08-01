<?php

namespace Z38\SwissPayment\TransactionInformation;

use DOMDocument;
use Z38\SwissPayment\BC;
use Z38\SwissPayment\BIC;
use Z38\SwissPayment\FinancialInstitutionInterface;
use Z38\SwissPayment\IBAN;
use Z38\SwissPayment\Money;
use Z38\SwissPayment\PaymentInformation\PaymentInformation;
use Z38\SwissPayment\PostalAddressInterface;

/**
 * BankCreditTransfer contains all the information about a type 3 transaction.
 */
class BankCreditTransfer extends CreditTransfer
{
    /**
     * @var IBAN
     */
    protected $creditorIBAN;

    /**
     * @var FinancialInstitutionInterface
     */
    protected $creditorAgent;

    /**
     * {@inheritdoc}
     *
     * @param IBAN   $creditorIBAN  IBAN of the creditor
     * @param BC|BIC $creditorAgent BC or BIC of the creditor's financial institution
     *
     * @throws \InvalidArgumentException.
     *     An InvalidArgumentException is thrown if amount is not EUR or CHF
     *     or if creditorAgent is not BC or BIC
     */
    public function __construct($instructionId, $endToEndId, Money\Money $amount, $creditorName, PostalAddressInterface $creditorAddress, IBAN $creditorIBAN, FinancialInstitutionInterface $creditorAgent)
    {
        if (false === $amount instanceof Money\EUR && false === $amount instanceof Money\CHF) {
            throw new \InvalidArgumentException(sprintf(
                'Amount must be an instance of Z38\SwissPayment\Money\EUR or Z38\SwissPayment\Money\CHF. Instance of %s given.',
                get_class($amount)
            ));
        }

        parent::__construct($instructionId, $endToEndId, $amount, $creditorName, $creditorAddress);

        if (!$creditorAgent instanceof BC && !$creditorAgent instanceof BIC) {
            throw new \InvalidArgumentException('The creditor agent must be an instance of BC or BIC.');
        }

        $this->creditorIBAN = $creditorIBAN;
        $this->creditorAgent = $creditorAgent;
    }

    /**
     * {@inheritdoc}
     */
    public function asDom(DOMDocument $doc, PaymentInformation $paymentInformation)
    {
        $root = $this->buildHeader($doc, $paymentInformation);

        $creditorAgent = $doc->createElement('CdtrAgt');
        $creditorAgent->appendChild($this->creditorAgent->asDom($doc));
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
