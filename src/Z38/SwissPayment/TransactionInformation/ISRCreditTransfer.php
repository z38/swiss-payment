<?php

namespace Z38\SwissPayment\TransactionInformation;

use DOMDocument;
use DOMElement;
use InvalidArgumentException;
use LogicException;
use Z38\SwissPayment\ISRParticipant;
use Z38\SwissPayment\Money;
use Z38\SwissPayment\PaymentInformation\PaymentInformation;
use Z38\SwissPayment\PostalAccount;
use Z38\SwissPayment\PostalAddressInterface;
use Z38\SwissPayment\Text;

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
     * @var string
     */
    protected $creditorReference;

    /**
     * {@inheritdoc}
     *
     * @param ISRParticipant $creditorAccount   ISR participation number of the creditor
     * @param string         $creditorReference ISR reference number
     *
     * @throws InvalidArgumentException When the amount or the creditor reference is invalid.
     */
    public function __construct($instructionId, $endToEndId, Money\Money $amount, ISRParticipant $creditorAccount, $creditorReference)
    {
        if (!$amount instanceof Money\EUR && !$amount instanceof Money\CHF) {
            throw new InvalidArgumentException(sprintf(
                'The amount must be an instance of Z38\SwissPayment\Money\EUR or Z38\SwissPayment\Money\CHF (instance of %s given).',
                get_class($amount)
            ));
        }

        if (!preg_match('/^[0-9]{1,27}$/', $creditorReference) || !PostalAccount::validateCheckDigit($creditorReference)) {
            throw new InvalidArgumentException('ISR creditor reference is invalid.');
        }

        $this->instructionId = Text::assertIdentifier($instructionId);
        $this->endToEndId = Text::assertIdentifier($endToEndId);
        $this->amount = $amount;
        $this->creditorAccount = $creditorAccount;
        $this->creditorReference = $creditorReference;
        $this->localInstrument = 'CH01';
    }

    /**
     * Sets creditor details
     *
     * @param string                      $creditorName
     * @param PostalAddressInterface|null $creditorAddress
     */
    public function setCreditorDetails($creditorName, PostalAddressInterface $creditorAddress = null)
    {
        $this->creditorName = Text::assert($creditorName, 70);
        $this->creditorAddress = $creditorAddress;
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
        $root = $this->buildHeader($doc, $paymentInformation);

        if (strlen($this->creditorName) && isset($this->creditorAddress)) {
            $root->appendChild($this->buildCreditor($doc));
        }

        $creditorAccount = $doc->createElement('CdtrAcct');
        $creditorAccount->appendChild($this->creditorAccount->asDom($doc));
        $root->appendChild($creditorAccount);

        $this->appendPurpose($doc, $root);

        $this->appendRemittanceInformation($doc, $root);

        return $root;
    }

    /**
     * {@inheritdoc}
     */
    protected function appendRemittanceInformation(DOMDocument $doc, DOMElement $transaction)
    {
        $remittanceInformation = $doc->createElement('RmtInf');

        $structured = $doc->createElement('Strd');
        $remittanceInformation->appendChild($structured);

        $creditorReferenceInformation = $doc->createElement('CdtrRefInf');
        $structured->appendChild($creditorReferenceInformation);

        $creditorReferenceInformation->appendChild($doc->createElement('Ref', $this->creditorReference));

        $transaction->appendChild($remittanceInformation);
    }
}
