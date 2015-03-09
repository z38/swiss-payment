<?php

namespace Z38\SwissPayment;

/**
 * General interface for financial institutions
 */
interface FinancialInstitutionInterface
{
    /**
     * Returns a XML representation to identify the financial institution
     *
     * @param \DOMDocument $doc
     *
     * @return \DOMElement The built DOM element
     */
    public function asDom(\DOMDocument $doc);
}
