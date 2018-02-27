<?php

namespace Z38\SwissPayment\TransactionInformation;

use DOMDocument;
use InvalidArgumentException;
use LogicException;
use Z38\SwissPayment\ISRParticipant;
use Z38\SwissPayment\Money;
use Z38\SwissPayment\PaymentInformation\PaymentInformation;
use Z38\SwissPayment\PostalAddressInterface;

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

        if (self::modulo10(substr($creditorReference, 0, -1)) != (int) substr($creditorReference, -1)) {
            throw new InvalidArgumentException('Invalid ISR creditor reference.');
        }

        $this->instructionId = (string) $instructionId;
        $this->endToEndId = (string) $endToEndId;
        $this->amount = $amount;
        $this->creditorAccount = $creditorAccount;
        $this->creditorReference = (string) $creditorReference;
        $this->localInstrument = 'CH01';
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

        if (isset($this->creditorName) && isset($this->creditorAddress)) {
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
    protected function appendRemittanceInformation(\DOMDocument $doc, \DOMElement $transaction)
    {
        $remittanceInformation = $doc->createElement('RmtInf');

        $structured = $doc->createElement('Strd');
        $remittanceInformation->appendChild($structured);

        $creditorReferenceInformation = $doc->createElement('CdtrRefInf');
        $structured->appendChild($creditorReferenceInformation);

        $creditorReferenceInformation->appendChild($doc->createElement('Ref', $this->creditorReference));

        $transaction->appendChild($remittanceInformation);
    }

    /**
     * Permit to set optional creditor details
     *
     * @param string                 $creditorName
     * @param PostalAddressInterface $creditorAddress
     */
    public function setCreditorDetails($creditorName, PostalAddressInterface $creditorAddress)
    {
        $this->creditorName = $creditorName;
        $this->creditorAddress = $creditorAddress;
    }

    /**
     * Creates Modulo10 recursive check digit
     *
     * @param string $number Number to create recursive check digit off.
     *
     * @return int Recursive check digit.
     */
    private static function modulo10(string $number)
    {
        $moduloTable = [0, 9, 4, 6, 8, 2, 7, 1, 3, 5];

        $next = 0;
        for ($i = 0; $i < strlen($number); $i++) {
            $next = $moduloTable[($next + intval(substr($number, $i, 1))) % 10];
        }

        return (int) (10 - $next) % 10;
    }
}
