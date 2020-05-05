<?php

namespace Z38\SwissPayment\TransactionInformation;

use DOMDocument;
use DOMElement;
use InvalidArgumentException;
use Z38\SwissPayment\FinancialInstitutionInterface;
use Z38\SwissPayment\IBAN;
use Z38\SwissPayment\Money;
use Z38\SwissPayment\PostalAccount;

/**
 * BankCreditTransfer contains all the information about a type 3 transaction
 * for a QR-Bill with Creditor Reference (SCOR)
 */
class BankCreditTransferWithCreditorReference extends BankCreditTransfer
{
    /**
     * @var string
     */
    protected $creditorReference;

    /**
     * BankCreditTransferWithQRR constructor.
     * @param $instructionId
     * @param $endToEndId
     * @param Money\Money $amount
     * @param $creditorName
     * @param $creditorAddress
     * @param IBAN $creditorIBAN  IBAN of the creditor
     * @param FinancialInstitutionInterface $creditorAgent BIC or IID of the creditor's financial institution
     * @param string $creditorReference QR reference number (QRR)
     */
    public function __construct(
        $instructionId,
        $endToEndId,
        Money\Money $amount,
        $creditorName,
        $creditorAddress,
        IBAN $creditorIBAN,
        FinancialInstitutionInterface $creditorAgent,
        $creditorReference
    ) {
        $cleanedCreditorReference = str_replace(' ', '', strtoupper($creditorReference));
        if (!preg_match('/^RF/', $cleanedCreditorReference)) {
            throw new InvalidArgumentException('The creditor reference (SCOR) must starts with RF : ISO-11649');
        }
        $this->creditorReference = $cleanedCreditorReference;

        if (preg_match('/^CH[0-9]{2}3/', $creditorIBAN->normalize())) {
            throw new InvalidArgumentException('The IBAN must not be a QR-IBAN');
        }

        parent::__construct($instructionId, $endToEndId, $amount, $creditorName, $creditorAddress, $creditorIBAN, $creditorAgent);
    }

    /**
     * @param DOMDocument $doc
     * @param DOMElement $transaction
     */
    protected function appendRemittanceInformation(DOMDocument $doc, DOMElement $transaction)
    {
        $remittanceInformation = $doc->createElement('RmtInf');

        $structured = $doc->createElement('Strd');
        $remittanceInformation->appendChild($structured);

        $creditorReferenceInformation = $doc->createElement('CdtrRefInf');
        $structured->appendChild($creditorReferenceInformation);

        $codeOrProperty = $doc->createElement('CdOrPrtry');
        $codeOrProperty->appendChild($doc->createElement('Cd', 'SCOR'));
        $type = $doc->createElement('Tp');
        $type->appendChild($codeOrProperty);

        $creditorReferenceInformation->appendChild($type);
        $creditorReferenceInformation->appendChild($doc->createElement('Ref', $this->creditorReference));

        if (!empty($this->remittanceInformation)) {
            $structured->appendChild($doc->createElement('AddtlRmtInf', $this->remittanceInformation));
        }

        $transaction->appendChild($remittanceInformation);
    }
}
