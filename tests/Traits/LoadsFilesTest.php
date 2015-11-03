<?php
namespace Michaels\Manager\Test\Traits;

use Michaels\Manager\Decoders\CustomXmlDecoder;
use Michaels\Manager\Test\Stubs\LoadsFilesTraitStub;
use Michaels\Manager\Test\Utilities\FileBagTestTrait;

class LoadsFilesTest extends \PHPUnit_Framework_TestCase
{
    use FileBagTestTrait;

    protected $defaultArray = [];

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

    protected function createFileLoaderMock()
    {
        $stub = $this->getMockBuilder('Michaels\Manager\FileLoader')
            ->getMock();

        return $stub;
    }

    /* Unit Tests: Uses FileLoader mock */
    public function testLoadFiles()
    {
        $files = [
            new \SplFileInfo('sometest'),
            new \SplFileInfo('someothertest'),
        ];

        $fileLoader = $this->createFileLoaderMock();
        $fileLoader->expects($this->once())
            ->method('addFiles')
            ->with($this->equalTo($files));
        $fileLoader->expects($this->once())
            ->method('process');

        $manager = new LoadsFilesTraitStub();
        $manager->setFileLoader($fileLoader);

        $manager->loadFiles($files);
    }

    public function testAddingDecoder()
    {
        $decoder = $this->getMockBuilder('Michaels\Manager\Contracts\DecoderInterface')
            ->getMock();

        $fileLoader = $this->createFileLoaderMock();
        $fileLoader->expects($this->once())
            ->method('addDecoder')
            ->with($this->equalTo($decoder));

        $manager = new LoadsFilesTraitStub();
        $manager->setFileLoader($fileLoader);

        $manager->addDecoder($decoder);
    }

    /* Integration Tests: uses true FileLoader and tests output */
    public function testLoadingFiles()
    {
        $goodTestFileDirectory = realpath(__DIR__ . '/../Fixtures/FilesWithGoodData');
        $goodFiles =  $this->setFilesToSplInfoObjects($goodTestFileDirectory);
        $manager = new LoadsFilesTraitStub();
        $manager->loadFiles($goodFiles);

        $this->assertEquals($this->defaultArray, $manager->all());
    }

    public function testWithDecoder()
    {
        $goodCustomTestFileDirectory = realpath(__DIR__ . '/../Fixtures/CustomFileWithGoodData');
        $customDecoder = new CustomXmlDecoder();
        $goodFiles =  $this->setFilesToSplInfoObjects($goodCustomTestFileDirectory);
        $manager = new LoadsFilesTraitStub();
        $manager->addDecoder($customDecoder);
        $manager->loadFiles($goodFiles);

        $this->assertEquals($this->defaultArray, $manager->all());
    }
}
