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
    protected $testLoadFileData = [
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

    public function setupDefaultArray()
    {
        $this->defaultArray = [];
        $this->defaultArray['jsonConfig'] = $this->testLoadFileData;
        $this->defaultArray['phpConfig'] = $this->testLoadFileData;
        $this->defaultArray['yamlConfig'] = $this->testLoadFileData;
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

        $manager = $this->getManager();
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

        $manager = $this->getManager();
        $manager->setFileLoader($fileLoader);

        $manager->addDecoder($decoder);
    }

    /* Integration Tests: uses true FileLoader and tests output */
    public function test_loading_files()
    {
        $this->setupDefaultArray();
        $goodTestFileDirectory = realpath(__DIR__ . '/../Fixtures/FilesWithGoodData');
        $goodFiles =  $this->setFilesToSplInfoObjects($goodTestFileDirectory);
        $manager = $this->getManager();
        $manager->loadFiles($goodFiles);

        $this->assertEquals($this->defaultArray, $manager->all());
    }

    public function test_load_single_file()
    {
        $this->setupDefaultArray();
        $manager = $this->getManager();
        $manager->loadFile(new \SplFileInfo(realpath(__DIR__ . '/../Fixtures/FilesWithGoodData/jsonConfig.json')));

        $expected = $this->defaultArray;
        unset($expected['phpConfig']);
        unset($expected['yamlConfig']);

        $this->assertEquals($expected, $manager->all());
    }

    public function test_with_decoder()
    {
        $goodCustomTestFileDirectory = realpath(__DIR__ . '/../Fixtures/CustomFileWithGoodData');
        $customDecoder = new CustomXmlDecoder();
        $goodFiles =  $this->setFilesToSplInfoObjects($goodCustomTestFileDirectory);
        $manager = $this->getManager();
        $manager->addDecoder($customDecoder);
        $manager->loadFiles($goodFiles);

        $expected['xmlConfig'] = $this->testLoadFileData;

        $this->assertEquals($expected, $manager->all());
    }

    public function test_get_fileloader()
    {
        $manager = $this->getManager();
        $manager->setFileLoader(new FileLoader());
        $loader = $manager->getFileLoader();

        $this->assertInstanceOf('Michaels\Manager\FileLoader', $loader, "failed to return the file loader");
    }
}
