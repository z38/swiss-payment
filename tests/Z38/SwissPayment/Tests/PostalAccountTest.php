<?php

namespace Z38\SwissPayment\Tests;

use Z38\SwissPayment\PostalAccount;

class PostalAccountTest extends TestCase
{
    /**
     * @dataProvider validSamples
     * @covers \Z38\SwissPayment\PostalAccount::__construct
     */
    public function testValid($postalAccount)
    {
        $this->check($postalAccount, true);
    }

    /**
     * @covers \Z38\SwissPayment\PostalAccount::__construct
     */
    public function testInvalidFormat()
    {
        $this->check('4032138', false);
        $this->check('40.3213.8', false);
        $this->check('40-003213-8', false);
        $this->check('40-3213-28', false);
    }

    /**
     * @covers \Z38\SwissPayment\PostalAccount::__construct
     */
    public function testInvalidPrefix()
    {
        $this->check('21-423332-2', false);
        $this->check('99-4332-2', false);
    }

    /**
     * @dataProvider validSamples
     * @covers \Z38\SwissPayment\PostalAccount::format
     */
    public function testFormat($postalAccount)
    {
        $instance = new PostalAccount($postalAccount);
        $this->assertEquals($postalAccount, $instance->format());
    }

    public function validSamples()
    {
        return array(
            array('80-2-2'),
            array('80-470-3'),
            array('40-3213-8'),
            array('87-344666-2')
        );
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
