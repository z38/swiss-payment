<?php

namespace Z38\SwissPayment\Tests;

use Z38\SwissPayment\PostalAccount;

class PostalAccountTest extends TestCase
{
    public function testValid()
    {
        $this->check('80-2-2', true);
        $this->check('80-470-3', true);
        $this->check('40-3213-8', true);
        $this->check('87-344666-2', true);
    }

    public function testInvalidFormat()
    {
        $this->check('4032138', false);
        $this->check('40.3213.8', false);
        $this->check('40-003213-8', false);
        $this->check('40-3213-28', false);
    }

    public function testInvalidPrefix()
    {
        $this->check('21-423332-2', false);
        $this->check('99-4332-2', false);
    }

    protected function check($postalAccount, $valid)
    {
        $exception = false;
        try {
            $temp = new PostalAccount($postalAccount);
        } catch (\InvalidArgumentException $e) {
            $exception = true;
        }
        $this->assertTrue($exception != $valid);
    }
}
