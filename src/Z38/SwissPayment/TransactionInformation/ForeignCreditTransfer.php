<?php

namespace Z38\SwissPayment\TransactionInformation;

use DOMDocument;
use Z38\SwissPayment\AccountInterface;
use Z38\SwissPayment\BIC;
use Z38\SwissPayment\FinancialInstitutionAddress;
use Z38\SwissPayment\FinancialInstitutionInterface;
use Z38\SwissPayment\Money\Money;
use Z38\SwissPayment\PaymentInformation\PaymentInformation;
use Z38\SwissPayment\PostalAddressInterface;

/**
 * ForeignCreditTransfer contains all the information about a foreign (type 6) transaction.
 */
class ForeignCreditTransfer extends CreditTransfer
{
    /**
     * @var AccountInterface
     */
    protected $creditorAccount;

    /**
     * @var BIC|FinancialInstitutionAddress
     */
    protected $creditorAgent;

    /**
     * {@inheritdoc}
     *
     * @param AccountInterface                $creditorAccount Account of the creditor
     * @param BIC|FinancialInstitutionAddress $creditorAgent   BIC or address of the creditor's financial institution
     */
    public function __construct($instructionId, $endToEndId, Money $amount, $creditorName, PostalAddressInterface $creditorAddress, AccountInterface $creditorAccount, FinancialInstitutionInterface $creditorAgent)
    {
        parent::__construct($instructionId, $endToEndId, $amount, $creditorName, $creditorAddress);

        if (!$creditorAgent instanceof BIC && !$creditorAgent instanceof FinancialInstitutionAddress) {
            throw new \InvalidArgumentException('The creditor agent must be an instance of BIC or FinancialInstitutionAddress.');
        }

        $this->creditorAccount = $creditorAccount;
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
        $creditorAccount->appendChild($this->creditorAccount->asDom($doc));
        $root->appendChild($creditorAccount);

        if ($this->hasRemittanceInformation()) {
            $root->appendChild($this->buildRemittanceInformation($doc));
        }

        return $root;
    }
}
