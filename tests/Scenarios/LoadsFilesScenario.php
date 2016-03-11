<?php
namespace Michaels\Manager\Test\Scenarios;

use Michaels\Manager\Decoders\CustomXmlDecoder;
use Michaels\Manager\FileLoader;
use Michaels\Manager\Test\Stubs\LoadsFilesTraitStub;
use Michaels\Manager\Test\Utilities\FileBagTestTrait;

trait LoadsFilesScenario
{
    use FileBagTestTrait;

    protected $defaultArray = [];
    protected $testData;

    public function setup()
    {
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
        $this->defaultArray['jsonConfig'] = $this->testData;
        $this->defaultArray['phpConfig'] = $this->testData;
        $this->defaultArray['yamlConfig'] = $this->testData;
    }

    protected function createFileLoaderMock()
    {
        $stub = $this->getMockBuilder('Michaels\Manager\FileLoader')
            ->getMock();

        return $stub;
    }

    /* Unit Tests: Uses FileLoader mock */
    public function test_load_files()
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

    public function test_adding_decoder()
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
    public function test_loading_files()
    {
        $goodTestFileDirectory = realpath(__DIR__ . '/../Fixtures/FilesWithGoodData');
        $goodFiles =  $this->setFilesToSplInfoObjects($goodTestFileDirectory);
        $manager = new LoadsFilesTraitStub();
        $manager->loadFiles($goodFiles);

        $this->assertEquals($this->defaultArray, $manager->all());
    }

    public function test_with_decoder()
    {
        $goodCustomTestFileDirectory = realpath(__DIR__ . '/../Fixtures/CustomFileWithGoodData');
        $customDecoder = new CustomXmlDecoder();
        $goodFiles =  $this->setFilesToSplInfoObjects($goodCustomTestFileDirectory);
        $manager = new LoadsFilesTraitStub();
        $manager->addDecoder($customDecoder);
        $manager->loadFiles($goodFiles);

        $expected['xmlConfig'] = $this->testData;

        $this->assertEquals($expected, $manager->all());
    }

    public function test_get_fileloader()
    {
        $manager = new LoadsFilesTraitStub();
        $manager->setFileLoader(new FileLoader());
        $loader = $manager->getFileLoader();

        $this->assertInstanceOf('Michaels\Manager\FileLoader', $loader, "failed to return the file loader");
    }
}
