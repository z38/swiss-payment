<?php

namespace Z38\SwissPayment;

class IBAN
{
    const PATTERN = '/^[A-Z]{2,2}[0-9]{2,2}[A-Z0-9]{1,30}$/';

    protected $iban;

    public function __construct($iban)
    {
        $cleanedIban = str_replace(' ', '', strtoupper($iban));
        if (!preg_match(self::PATTERN, $cleanedIban)) {
            throw new \InvalidArgumentException('IBAN is not properly formatted.');
        }
        if (!self::check($cleanedIban)) {
            throw new \InvalidArgumentException('IBAN has an invalid checksum.');
        }

        $this->iban = $cleanedIban;
    }

    public function format($human = true)
    {
        if ($human) {
            $parts = str_split($iban, 4);

            return implode(' ', $parts);
        } else {
            return $this->iban;
        }
    }

    public function getCountry()
    {
        return substr($this->iban, 0, 2);
    }

    protected static function check($iban)
    {
        $prepared = str_split(substr($iban, 4).substr($iban, 0, 4));
        foreach ($prepared as &$c) {
            if (ord($c) >= ord('A') && ord($c) <= ord('Z')) {
                $c = ord($c) - ord('A') + 10;
            }
        }
        unset($c);
        $prepared = implode($prepared);

        $r = '';
        $i = 0;
        while ($i < strlen($prepared)) {
            $d = $r.substr($prepared, $i, 9 - strlen($r));
            $i += 9 - strlen($r);
            $r = $d % 97;
        }

        return ($r == 1);
    }
}
