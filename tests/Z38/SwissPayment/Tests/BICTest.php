<?php

namespace Z38\SwissPayment\Tests;

use Z38\SwissPayment\BIC;

class BICTest extends TestCase
{
    public function testValid()
    {
        $this->check('AABAFI22', true);
        $this->check('HANDFIHH', true);
        $this->check('DEUTDEFF500', true);
    }

    public function testInvalidLength()
    {
        $this->check('AABAFI22F', false);
        $this->check('HANDFIHH00', false);
    }

    public function testInvalidChars()
    {
        $this->check('HAND-FIHH', false);
        $this->check('HAND FIHH', false);
    }

    protected function check($iban, $valid)
    {
        $exception = false;
        try {
            $temp = new BIC($iban);
        } catch (\InvalidArgumentException $e) {
            $exception = true;
        }
        $this->assertTrue($exception != $valid);
    }
}
