<?php

namespace Z38\SwissPayment\TransactionInformation;

use Z38\SwissPayment\BIC;
use Z38\SwissPayment\FinancialInstitutionAddress;
use Z38\SwissPayment\FinancialInstitutionInterface;
use Z38\SwissPayment\IBAN;
use Z38\SwissPayment\IntermediarySwift;
use Z38\SwissPayment\Money\Money;
use Z38\SwissPayment\PaymentInformation\PaymentInformation;
use Z38\SwissPayment\PostalAddressInterface;

/**
 * ForeignCreditTransfer contains all the information about a foreign (type 6) transaction.
 */
class ForeignCreditTransfer extends CreditTransfer
{
    /**
     * @var IBAN
     */
    protected $creditorIBAN;

    /**
     * @var BIC|FinancialInstitutionAddress
     */
    protected $creditorAgent;

    /**
     * {@inheritdoc}
     *
     * @param IBAN                              $creditorIBAN  IBAN of the creditor
     * @param BIC|FinancialInstitutionAddress   $creditorAgent BIC or address of the creditor's financial institution
     * @param IntermediarySwift|null            $intermediarySwift IntermediarySwift
     */
    public function __construct($instructionId, $endToEndId, Money $amount, $creditorName, PostalAddressInterface $creditorAddress, IBAN $creditorIBAN, FinancialInstitutionInterface $creditorAgent, $intermediarySwift = null)
    {
        parent::__construct($instructionId, $endToEndId, $amount, $creditorName, $creditorAddress, $intermediarySwift);

        if (!$creditorAgent instanceof BIC && !$creditorAgent instanceof FinancialInstitutionAddress) {
            throw new \InvalidArgumentException('The creditor agent must be an instance of BIC or FinancialInstitutionAddress.');
        }

        $this->creditorIBAN = $creditorIBAN;
        $this->creditorAgent = $creditorAgent;
    }

    /**
     * {@inheritdoc}
     */
    public function asDom(\DOMDocument $doc, PaymentInformation $paymentInformation)
    {
        $root = $this->buildHeader($doc, $paymentInformation);

        $creditorAgent = $doc->createElement('CdtrAgt');
        $creditorAgent->appendChild($this->creditorAgent->asDom($doc));
        $root->appendChild($creditorAgent);

        if ($this->intermediarySwift) {
            $intermediary = $doc->createElement('IntrmyAgt1');
            $intermediary->appendChild($this->intermediarySwift->asDom($doc));
            $root->appendChild($intermediary);
        }

        $root->appendChild($this->buildCreditor($doc));

        $creditorAccount = $doc->createElement('CdtrAcct');
        $creditorAccountId = $doc->createElement('Id');
        $creditorAccountId->appendChild($this->creditorIBAN->asDom($doc));
        $creditorAccount->appendChild($creditorAccountId);
        $root->appendChild($creditorAccount);

        if ($this->hasRemittanceInformation()) {
            $root->appendChild($this->buildRemittanceInformation($doc));
        }

        return $root;
    }
}
