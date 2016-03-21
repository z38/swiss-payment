<?php
namespace Z38\SwissPayment;

class IntermediarySwift implements FinancialInstitutionInterface
{
    /**
     * @var BIC
     */
    private $bic;

    /**
     * IntermediarySwift constructor.
     * @param BIC $bic
     */
    public function __construct(BIC $bic)
    {
        $this->bic = $bic;
    }

    public function asDom(\DOMDocument $doc)
    {
        return $this->bic->asDom($doc);
    }
}
