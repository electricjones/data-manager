<?php
namespace Michaels\Manager\Test;

use Michaels\Manager\Decoders\PhpDecoder;

class PhpDecoderTest extends \PHPUnit_Framework_TestCase
{
    private $phpData;
    private $testArrayData;
    private $phpDecoder;

    public function setup()
    {
        $this->testArrayData = [
            'one' => [
                'two' => [
                    'three' => [
                        'true' => true,
                    ]
                ],
                'four' => [
                    'six' => false,
                ]
            ],
            'five' => 5,
            'six' => [
                'a' => 'A',
                'b' => 'B',
                'c' => 'C',
            ]
        ];



        $this->phpDecoder = new PhpDecoder();
        // This decoder is just a pass through of array data from a config file, which returns [].
        $this->phpData = $this->testArrayData;
    }

    public function testGettingMimeType()
    {
        $expected = ['php'];
        $this->assertEquals($expected, $this->phpDecoder->getMimeType());

    }

    public function testPhpDecoding()
    {
        $this->assertEquals($this->testArrayData, $this->phpDecoder->decode($this->phpData));
    }
}