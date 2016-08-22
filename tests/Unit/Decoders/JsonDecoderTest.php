<?php
namespace Michaels\Manager\Test\Unit\Decoders;

use Michaels\Manager\Decoders\JsonDecoder;

class JsonDecoderTest extends \PHPUnit_Framework_TestCase
{
    private $jsonData;
    private $testArrayData;

    /** @var JsonDecoder */
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

    public function test_getting_mime_type()
    {
        $expected = ['json'];
        $this->assertEquals($expected, $this->jsonDecoder->getMimeType());

    }

    public function test_json_decoding()
    {
        $this->assertEquals($this->testArrayData, $this->jsonDecoder->decode($this->jsonData));
    }


    /**
     * @expectedException \Michaels\Manager\Exceptions\JsonDecodingFailedException
     */
    public function test_invalid_json_decoding()
    {
        $this->jsonDecoder->decode($this->testArrayData);
    }

}

