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

    protected $testData;
    private $fileLoader;

    private $defaultArray = [];

    public function setup()
    {
        $this->fileLoader = new FileLoader();
        $this->testData = [
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

        $this->defaultArray = [];
        $this->defaultArray['json'] = $this->testData;
        $this->defaultArray['php'] = $this->testData;
        $this->defaultArray['yaml'] = $this->testData;
    }

    public function testAddingFilesAsArray()
    {
        $goodTestFileDirectory = realpath(__DIR__ . '/Fixtures/FilesWithGoodData');
        $goodFiles =  $this->setFilesToSplInfoObjects($goodTestFileDirectory);
        $this->fileLoader->addFiles($goodFiles);
        $this->assertEquals($this->defaultArray, $this->fileLoader->process());
    }

    public function testAddingFileBag()
    {
        $goodTestFileDirectory = realpath(__DIR__ . '/Fixtures/FilesWithGoodData');
        $goodFiles =  $this->setFilesToSplInfoObjects($goodTestFileDirectory);
        $fileBag = new FileBag($goodFiles);
        $this->fileLoader->addFiles($fileBag);

        $this->assertEquals($this->defaultArray, $this->fileLoader->process());
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

        $expected = $this->defaultArray;
        $expected['xml'] = $this->testData;

        $this->assertEquals($expected, $this->fileLoader->process());
    }

    public function testAddDecoder()
    {
        $customDecoder = new CustomXmlDecoder();
        $this->fileLoader->addDecoder($customDecoder);
        $actual = $this->fileLoader->getDecoders();

        $this->assertEquals(['xml' => $customDecoder], $actual, "failed to add the decoder");
        $this->assertCount(1, $actual, "failed to add a single decoder");

        $this->assertEquals(['xml'], $this->fileLoader->getMimeTypes(), "failed to add to supported mime types list");
        $this->assertCount(1, $actual, "failed to add a single mime type");
    }

    public function testAddDecoderTwice()
    {
        $customDecoder = new CustomXmlDecoder();
        $this->fileLoader->addDecoder($customDecoder);
        $this->fileLoader->addDecoder($customDecoder);
        $actual = $this->fileLoader->getDecoders();

        $this->assertEquals(['xml' => $customDecoder], $actual, "failed to add the decoder");
        $this->assertCount(1, $actual, "failed to add a single decoder");

        $this->assertEquals(['xml'], $this->fileLoader->getMimeTypes(), "failed to add to supported mime types list");
        $this->assertCount(1, $actual, "failed to add a single mime type");
    }

    /**
     * @expectedException \Exception
     */
    public function testErrorWhenDecodingEmptyFileBag()
    {
        $fileLoader = new FileLoader();
        $fileLoader->decodeFileBagData(new FileBag());
    }

    /**
     * @expectedException \Michaels\Manager\Exceptions\UnsupportedFilesException
     */
    public function testErrorWhenDecodingInvalidFileTypes()
    {
        $this->fileLoader->addFiles([
            new \SplFileInfo(realpath('/Fixtures/FilesWithBadNames/config.ini')),
            new \SplFileInfo(realpath('/Fixtures/FilesWithBadNames/config.txt')),
        ]);

        $this->fileLoader->process();
    }

    /**
     * @expectedException \Michaels\Manager\Exceptions\BadFileDataException
     */
    public function testErrorWhenAddingInvalidFileBag()
    {
        $fileLoader = new FileLoader();
        $fileLoader->addFiles('string');
    }
}
