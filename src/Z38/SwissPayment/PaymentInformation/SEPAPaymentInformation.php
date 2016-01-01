<?php

namespace Z38\SwissPayment\PaymentInformation;

use Z38\SwissPayment\FinancialInstitutionInterface;
use Z38\SwissPayment\IBAN;

/**
 * SEPAPaymentInformation contains a group of SEPA transactions
 */
class SEPAPaymentInformation extends PaymentInformation
{
    /**
     * {@inheritdoc}
     */
    public function __construct($id, $debtorName, FinancialInstitutionInterface $debtorAgent, IBAN $debtorIBAN)
    {
        parent::__construct($id, $debtorName, $debtorAgent, $debtorIBAN);
        $this->serviceLevel = 'SEPA';
    }
}
