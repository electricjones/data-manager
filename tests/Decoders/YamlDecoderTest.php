<?php
namespace Michaels\Manager\Test;

use Michaels\Manager\Decoders\YamlDecoder;
use Symfony\Component\Yaml\Yaml;

class YamlDecoderTest extends \PHPUnit_Framework_TestCase
{
    private $yamlData;
    private $testArrayData;
    private $yamlDecoder;

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



        $this->yamlDecoder = new YamlDecoder();
        $this->yamlData = Yaml::dump($this->testArrayData);

    }

    public function testGettingMimeType()
    {
        $expected = ['yaml','yml'];
        $this->assertEquals($expected, $this->yamlDecoder->getMimeType());

    }

    public function testYamlDecoding()
    {
        $this->assertEquals($this->testArrayData, $this->yamlDecoder->decode($this->yamlData));
    }
}

