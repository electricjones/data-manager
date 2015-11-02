<?php
namespace Michaels\Manager\Test\Traits;

use Michaels\Manager\Test\Bags\FileBagTestTrait;
use Michaels\Manager\Manager;
use Michaels\Manager\Decoders\CustomXmlDecoder;

class LoadsFilesTest extends \PHPUnit_Framework_TestCase
{
    use FileBagTestTrait;

    private $manager;

    private $defaultArray = array();

    public function setup()
    {
        $this->defaultArray = [
            'one' => [
                'two' => [
                    'three' => 'three-value',
                    'four' => [
                        'five' => 'five-value'
                    ],
                ],
                'six' => [
                    'seven' => 'seven-value',
                    'eight' => 'eight-value'
                ]
            ],
            'top' => 'top-value',
        ];
    }



    public function testLoadingFiles()
    {
        $goodTestFileDirectory = realpath(__DIR__ . '/../Fixtures/FilesWithGoodData');
        $goodFiles =  $this->setFilesToSplInfoObjects($goodTestFileDirectory);
        $this->manager = new Manager();
        $this->manager->loadFiles($goodFiles);

        $this->assertEquals($this->defaultArray, $this->manager->all());

    }

    public function testAddingDecoder()
    {
        $goodCustomTestFileDirectory = realpath(__DIR__ . '/../Fixtures/CustomFileWithGoodData');
        $customDecoder = new CustomXmlDecoder();
        $goodFiles =  $this->setFilesToSplInfoObjects($goodCustomTestFileDirectory);
        $this->manager = new Manager();
        $this->manager->addDecoder($customDecoder);
        $this->manager->loadFiles($goodFiles);

        $this->assertEquals($this->defaultArray, $this->manager->all());

    }
}

