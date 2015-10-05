<?php
namespace Michaels\Manager\Test;

use Michaels\Manager\Decoders\JsonDecoder;

class JsonDecoderTest extends \PHPUnit_Framework_TestCase
{
    private $jsonData;
    private $testArrayData;
    private $jsonDecoder;

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

        $this->jsonData = json_encode($this->testArrayData);

        $this->jsonDecoder = new JsonDecoder();
    }

    public function testGettingMimeType()
    {
        $expected = ['json'];
        $this->assertEquals($expected, $this->jsonDecoder->getMimeType());

    }

    public function testJsonDecoding()
    {
        $this->assertEquals($this->testArrayData, $this->jsonDecoder->decode($this->jsonData));
    }


    /**
     * @expectedException \Michaels\Manager\Exceptions\JsonDecodingFailedException
     */
    public function testInvalidJsonDecoding()
    {
        $this->jsonDecoder->decode($this->testArrayData);
    }

}