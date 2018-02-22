<?php

namespace Z38\SwissPayment\Tests;

use Z38\SwissPayment\QRCode;

class QRCodeTest extends TestCase
{
    const SAMPLE_FULL = "SPC\r\n0100\r\n1\r\nCH4431999123000889012\r\nRobert Schneider AG\r\nRue du Lac\r\n1268/2/22\r\n2501\r\nBiel\r\nCH\r\nRobert Schneider Services Switzerland AG\r\nRue du Lac\r\n1268/3/1\r\n2501\r\nBiel\r\nCH\r\n123,949.75\r\nCHF\r\n2019-10-31\r\nPia-Maria Rutschmann-Schnyder\r\nGrosse Marktgasse\r\n28\r\n9400\r\nRorschach\r\nCH\r\nQRR\r\n210000000003139471430009017\r\nInstruction of15.09.2019##S1/01/20170309/11/10201409/20/14000000/22/36958/30/CH106017086/40/1020/41/3010\r\nUV1;1.1;1278564;1A-2F-43-AC-9B-33-21-B0-CC-D4-28-56;TCXVMKC22;2019-02-10T15: 12:39; 2019-02-10T15:18:16\r\nXY2;2a-2.2r;_R1-CH2_ConradCH-2074-1_3350_2019-03-13T10:23:47_16,99_0,00_0,00_0,00_0,00_+8FADt/DQ=_1==";
    const SAMPLE_DONATION = "SPC\r\n0100\r\n1\r\nCH3709000000304442225\r\nSalvation Army Foundation Switzerland\r\n\r\n\r\n3000\r\nBerne\r\nCH\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\nCHF\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\nNON\r\n\r\n";
    const SAMPLE_REFERENCE = "SPC\r\n0100\r\n1\r\nCH4431999123000889012\r\nRobert Schneider AG\r\nRue du Lac\r\n1268/2/22\r\n2501\r\nBiel\r\nCH\r\nRobert Schneider Services Switzerland AG\r\nRue du Lac\r\n1268/3/1\r\n2501\r\nBiel\r\nCH\r\n199.95\r\nCHF\r\n2019-10-31\r\nPia-Maria Rutschmann-Schnyder\r\nGrosse Marktgasse\r\n28\r\n9400\r\nRorschach\r\nCH\r\nSCOR\r\nRF18539007547034\r\n";

    /**
     * @dataProvider samplesValid
     * @covers \Z38\SwissPayment\QRCode::__construct
     */
    public function testValid($code)
    {
        $this->assertInstanceOf(QRCode::class, new QRCode($code));
    }

    /**
     * @covers \Z38\SwissPayment\QRCode::getAlternativeScheme
     */
    public function testGetAlternativeScheme()
    {
        $code = new QRCode(self::SAMPLE_FULL);

        $scheme = $code->getAlternativeScheme('XY2');

        $this->assertSame([
            '2a-2.2r',
            '_R1-CH2_ConradCH-2074-1_3350_2019-03-13T10:23:47_16,99_0,00_0,00_0,00_0,00_+8FADt/DQ=_1==',
        ], $scheme);
    }

    /**
     * @covers \Z38\SwissPayment\QRCode::getAlternativeSchemes
     */
    public function testGetAlternativeSchemes()
    {
        $code = new QRCode(self::SAMPLE_FULL);

        $schemes = $code->getAlternativeSchemes();

        $this->assertCount(2, $schemes);
        $this->assertSame('UV1;1.1;1278564;1A-2F-43-AC-9B-33-21-B0-CC-D4-28-56;TCXVMKC22;2019-02-10T15: 12:39; 2019-02-10T15:18:16', $schemes[0]);
    }

    /**
     * @covers \Z38\SwissPayment\QRCode::getAlternativeSchemes
     */
    public function testGetAlternativeSchemesEmpty()
    {
        $code = new QRCode(self::SAMPLE_DONATION);

        $schemes = $code->getAlternativeSchemes();

        $this->assertCount(0, $schemes);
    }

    public function samplesValid()
    {
        return [
            [self::SAMPLE_FULL],
            [self::SAMPLE_DONATION],
            [self::SAMPLE_REFERENCE],
        ];
    }
}
