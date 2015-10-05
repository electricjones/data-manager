<?php
namespace Michaels\Manager\Test;

use Michaels\Manager\Manager;
use Michaels\Manager\FileLoader;
use Michaels\Manager\Bags\FileBag;
use Michaels\Manager\Test\Bags\FileBagTestTrait;

class FileLoaderTest extends \PHPUnit_Framework_TestCase
{
    use FileBagTestTrait;

    private $fileLoader;

    private $manager;

    private $defaultArray;

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
        $goodFileData =  $this->setFilesToSplInfoObjects($goodTestFileDirectory);
        $this->fileLoader->addFiles($goodFileData);
        $this->fileLoader->hydrateManager();
        $this->assertEquals($this->defaultArray, $this->manager->getAll());


    }

    public function testAddingFileBag()
    {
        $goodTestFileDirectory = realpath(__DIR__ . '/Fixtures/FilesWithGoodData');
        $goodFileData =  $this->setFilesToSplInfoObjects($goodTestFileDirectory);
        $fileBag = new FileBag($goodFileData);
        $this->fileLoader->addFiles($fileBag);
        $this->fileLoader->hydrateManager();

        $this->assertEquals($this->defaultArray, $this->manager->getAll());


    }

    public function testResettingFileLoader()
    {

    }

    public function testAddingADecoder()
    {

    }


}