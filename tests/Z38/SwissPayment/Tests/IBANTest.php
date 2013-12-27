<?php

namespace Z38\SwissPayment\Tests;

use Z38\SwissPayment\IBAN;

class IBANTest extends TestCase
{
    public function testValid()
    {
        $this->check('AZ21 NABZ 0000 0000 1370 1000 1944', true);
        $this->check('FR14 2004 1010 0505 0001 3M02 606', true);
        $this->check('CH9300762011623852957', true);
    }

    public function testCountry()
    {
        $iban = new IBAN('AZ21 NABZ 0000 0000 1370 1000 1944');
        $this->assertEquals('AZ', $iban->getCountry());
    }

    public function testInvalidChars()
    {
        $this->check('CZ28-0300-0080-1005-6650-1963', false);
        $this->check('CZ28:0300:0080:1005:6650:1963', false);
    }

    public function testWrongChecksum()
    {
        $this->check('FR13 2004 1010 0505 0001 3M02 606', false);
        $this->check('CH9200762011623852957', false);
    }

    protected function check($iban, $valid)
    {
        $exception = false;
        try {
            $temp = new IBAN($iban);
        } catch (\InvalidArgumentException $e) {
            $exception = true;
        }
        $this->assertTrue($exception != $valid);
    }
}
