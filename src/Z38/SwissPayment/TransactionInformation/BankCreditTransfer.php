<?php

namespace Z38\SwissPayment\TransactionInformation;

use DOMDocument;
use InvalidArgumentException;
use Z38\SwissPayment\BIC;
use Z38\SwissPayment\FinancialInstitutionInterface;
use Z38\SwissPayment\IBAN;
use Z38\SwissPayment\IID;
use Z38\SwissPayment\Money;
use Z38\SwissPayment\PaymentInformation\PaymentInformation;
use Z38\SwissPayment\PostalAddressInterface;
use Z38\SwissPayment\QRCode;
use Z38\SwissPayment\QRReference;
use Z38\SwissPayment\RemittanceInformation\CreditorReferenceInformation;
use Z38\SwissPayment\RemittanceInformation\QRReferenceInformation;
use Z38\SwissPayment\RemittanceInformation\UnstructuredInformation;

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
     * @var string|null
     */
    protected $ultimateCreditorName;

    /**
     * @var PostalAddressInterface|null
     */
    protected $ultimateCreditorAddress;

    /**
     * {@inheritdoc}
     *
     * @param IBAN         $creditorIBAN  IBAN of the creditor
     * @param BIC|IID|null $creditorAgent BIC or IID of the creditor's financial institution
     *
     * @throws \InvalidArgumentException When the amount is not in EUR or CHF or when the creditor agent is not BIC or IID.
     */
    public function __construct($instructionId, $endToEndId, Money\Money $amount, $creditorName, PostalAddressInterface $creditorAddress, IBAN $creditorIBAN, FinancialInstitutionInterface $creditorAgent = null)
    {
        if (!$amount instanceof Money\EUR && !$amount instanceof Money\CHF) {
            throw new InvalidArgumentException(sprintf(
                'The amount must be an instance of Z38\SwissPayment\Money\EUR or Z38\SwissPayment\Money\CHF (instance of %s given).',
                get_class($amount)
            ));
        }

        if ($creditorAgent !== null && !$creditorAgent instanceof BIC && !$creditorAgent instanceof IID) {
            throw new InvalidArgumentException('The creditor agent must be an instance of BIC or IID.');
        }

        parent::__construct($instructionId, $endToEndId, $amount, $creditorName, $creditorAddress);

        $this->creditorIBAN = $creditorIBAN;
        $this->creditorAgent = $creditorAgent;
    }

    /**
     * Constructs an instruction from a QR code.
     *
     * @param string $instructionId Identifier of the instruction (should be unique within the message)
     * @param string $endToEndId    End-To-End Identifier of the instruction (passed unchanged along the complete processing chain)
     * @param QRCode $code          QR Code
     * @params Money $amount Amount if it is not part of the code, null otherwise.
     *
     * @returns PaymentInformation
     *
     * @throws InvalidArgumentException When the amount is invalid
     */
    public static function fromQRCode($instructionId, $endToEndId, QRCode $code, Money\Money $amount = null)
    {
        if (is_null($code->getAmount()) === is_null($amount)) {
            throw new InvalidArgumentException('Amount needs to be passed when it is not part of the code.');
        }
        $amount = $amount ?: $code->getAmount();
        if ($amount->getCurrency() !== $code->getCurrency()) {
            throw new InvalidArgumentException(sprintf('Amount needs to be in %s.', $code->getCurrency()));
        }

        $transfer = new self($instructionId, $endToEndId, $amount, $code->getCreditorName(), $code->getCreditorAddress(), $code->getCreditorAccount());

        if ($code->getUltimateCreditorName()) {
            $transfer->setUltimateCreditorName($code->getUltimateCreditorName());
            $transfer->setUltimateCreditorAddress($code->getUltimateCreditorAddress());
        }

        if ($code->getUltimateDebtorName()) {
            $transfer->setUltimateDebtorName($code->getUltimateDebtorName());
            $transfer->setUltimateDebtorAddress($code->getUltimateDebtorAddress());
        }

        $reference = $code->getReference();
        if ($reference instanceof QRReference) {
            $transfer->setRemittanceInformation(new QRReferenceInformation($reference, $code->getUnstructuredMessage()));
        } elseif ($reference instanceof CreditorReference) {
            $transfer->setRemittanceInformation(new CreditorReferenceInformation($reference));
        } elseif ($code->getUnstructuredMessage() !== null) {
            $transfer->setRemittanceInformation(new UnstructuredInformation($code->getUnstructuredMessage()));
        }

        return $transfer;
    }

    /**
     * Sets the name of the ultimate creditor
     *
     * @param string|null $name
     *
     * @return self
     */
    public function setUltimateCreditorName($name)
    {
        $this->ultimateCreditorName = $name;

        return $this;
    }

    /**
     * Sets the address of the ultimate creditor
     *
     * @param PostalAddressInterface|null $address
     *
     * @return self
     */
    public function setUltimateCreditorAddress(PostalAddressInterface $address)
    {
        $this->ultimateCreditorAddress = $address;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function asDom(DOMDocument $doc, PaymentInformation $paymentInformation)
    {
        $root = $this->buildHeader($doc, $paymentInformation);

        if ($this->creditorAgent !== null) {
            $creditorAgent = $doc->createElement('CdtrAgt');
            $creditorAgent->appendChild($this->creditorAgent->asDom($doc));
            $root->appendChild($creditorAgent);
        }

        $root->appendChild($this->buildCreditor($doc));

        $creditorAccount = $doc->createElement('CdtrAcct');
        $creditorAccount->appendChild($this->creditorIBAN->asDom($doc));
        $root->appendChild($creditorAccount);

        if (strlen($this->ultimateCreditorName) || $this->ultimateCreditorAddress !== null) {
            $ultimateCreditor = $doc->createElement('UltmtCdtr');
            if (strlen($this->ultimateCreditorName)) {
                $ultimateCreditor->appendChild($doc->createElement('Nm', $this->ultimateCreditorName));
            }
            if ($this->ultimateCreditorAddress !== null) {
                $ultimateCreditor->appendChild($this->ultimateCreditorAddress->asDom($doc));
            }
            $root->appendChild($ultimateCreditor);
        }

        $this->appendPurpose($doc, $root);

        $this->appendRemittanceInformation($doc, $root);

        return $root;
    }
}
