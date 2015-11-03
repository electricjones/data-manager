<?php
namespace Michaels\Manager\Test;

use Michaels\Manager\Manager;
use Michaels\Manager\FileLoader;
use Michaels\Manager\Bags\FileBag;
use Michaels\Manager\Test\Utilities\FileBagTestTrait;
use Michaels\Manager\Decoders\CustomXmlDecoder;

class FileLoaderTest extends \PHPUnit_Framework_TestCase
{
    use FileBagTestTrait;

    private $fileLoader;

    private $manager;

    private $defaultArray = [];

    /**
     *
     */
    protected function setUp()
    {
        $this->manager = new Manager();
        $this->fileLoader = new FileLoader($this->manager);


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

    public function testAddingFileArray()
    {
        $goodTestFileDirectory = realpath(__DIR__ . '/Fixtures/FilesWithGoodData');
        $goodFiles =  $this->setFilesToSplInfoObjects($goodTestFileDirectory);
        $this->fileLoader->addFiles($goodFiles);
        $this->fileLoader->hydrateManager();
        $this->assertEquals($this->defaultArray, $this->manager->getAll());


    }

    public function testAddingFileBag()
    {
        $goodTestFileDirectory = realpath(__DIR__ . '/Fixtures/FilesWithGoodData');
        $goodFiles =  $this->setFilesToSplInfoObjects($goodTestFileDirectory);
        $fileBag = new FileBag($goodFiles);
        $this->fileLoader->addFiles($fileBag);
        $this->fileLoader->hydrateManager();

        $this->assertEquals($this->defaultArray, $this->manager->getAll());


    }

    public function testResettingFileLoader()
    {

    }

    public function testAddingADecoder()
    {
        $goodTestFileDirectory = realpath(__DIR__ . '/Fixtures/FilesWithGoodData');
        $goodCustomFileDirectory = realpath(__DIR__ . '/Fixtures/CustomFileWithGoodData');
        $goodFiles =  $this->setFilesToSplInfoObjects($goodTestFileDirectory);
        $goodCustomFile = $this->setFilesToSplInfoObjects($goodCustomFileDirectory);
        $goodFiles = array_merge($goodFiles, $goodCustomFile);
        $customDecoder = new CustomXmlDecoder();
        $fileBag = new FileBag($goodFiles);
        $this->fileLoader->addDecoder($customDecoder);
        $this->fileLoader->addFiles($fileBag);
        $this->fileLoader->hydrateManager();

        $this->assertEquals($this->defaultArray, $this->manager->getAll());

    }


}

