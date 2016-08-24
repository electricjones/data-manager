<?php
namespace Michaels\Manager\Test\Unit;

use Michaels\Manager\Manager;
use Michaels\Manager\FileLoader;
use Michaels\Manager\Bags\FileBag;
use Michaels\Manager\Test\Utilities\FileBagTestTrait;
use Michaels\Manager\Decoders\CustomXmlDecoder;

class FileLoaderTest extends \PHPUnit_Framework_TestCase
{
    use FileBagTestTrait;

    protected $testData;

    /** @var  FileLoader */
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
        $this->defaultArray['jsonConfig'] = $this->testData;
        $this->defaultArray['phpConfig'] = $this->testData;
        $this->defaultArray['yamlConfig'] = $this->testData;
    }

    public function test_adding_files_as_array()
    {
        $goodTestFileDirectory = realpath($GLOBALS['test_config']['test_dir'] . '/Fixtures/FilesWithGoodData');
        $goodFiles =  $this->setFilesToSplInfoObjects($goodTestFileDirectory);
        $this->fileLoader->addFiles($goodFiles);
        $this->assertEquals($this->defaultArray, $this->fileLoader->process());
    }

    public function test_adding_file_bag()
    {
        $goodTestFileDirectory = realpath($GLOBALS['test_config']['test_dir'] . '/Fixtures/FilesWithGoodData');
        $goodFiles =  $this->setFilesToSplInfoObjects($goodTestFileDirectory);
        $fileBag = new FileBag($goodFiles);
        $this->fileLoader->addFiles($fileBag);

        $this->assertEquals($this->defaultArray, $this->fileLoader->process());
    }

    public function test_adding_a_custom_decoder_and_processing()
    {
        $goodTestFileDirectory = realpath($GLOBALS['test_config']['test_dir'] . '/Fixtures/FilesWithGoodData');
        $goodCustomFileDirectory = realpath($GLOBALS['test_config']['test_dir'] . '/Fixtures/CustomFileWithGoodData');
        $goodFiles =  $this->setFilesToSplInfoObjects($goodTestFileDirectory);
        $goodCustomFile = $this->setFilesToSplInfoObjects($goodCustomFileDirectory);
        $goodFiles = array_merge($goodFiles, $goodCustomFile);
        $customDecoder = new CustomXmlDecoder();

        $fileBag = new FileBag($goodFiles);
        $this->fileLoader->addDecoder($customDecoder);
        $this->fileLoader->addFiles($fileBag);

        $expected = $this->defaultArray;
        $expected['xmlConfig'] = $this->testData;

        $this->assertEquals($expected, $this->fileLoader->process());
    }

    public function test_add_decoder()
    {
        $customDecoder = new CustomXmlDecoder();
        $this->fileLoader->addDecoder($customDecoder);
        $actual = $this->fileLoader->getDecoders();

        $this->assertEquals(['xml' => $customDecoder], $actual, "failed to add the decoder");
        $this->assertCount(1, $actual, "failed to add a single decoder");

        $this->assertEquals(['xml'], $this->fileLoader->getMimeTypes(), "failed to add to supported mime types list");
        $this->assertCount(1, $actual, "failed to add a single mime type");
    }

    public function test_add_decoder_twice()
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

    public function test_error_when_decoding_empty_file_bag()
    {
        $this->setExpectedException('\Exception');

        $fileLoader = new FileLoader();
        $fileLoader->decodeFileBagData(new FileBag());
    }

    public function test_error_when_decoding_invalid_file_types()
    {
        $this->setExpectedException('\Michaels\Manager\Exceptions\UnsupportedFilesException');

        $this->fileLoader->addFiles([
            new \SplFileInfo(realpath('/Fixtures/FilesWithBadNames/config.ini')),
            new \SplFileInfo(realpath('/Fixtures/FilesWithBadNames/config.txt')),
        ]);

        $this->fileLoader->process();
    }

    public function test_error_when_adding_invalid_file_bag()
    {
        $this->setExpectedException('\Michaels\Manager\Exceptions\BadFileDataException');

        $fileLoader = new FileLoader();
        $fileLoader->addFiles('string');
    }

    public function test_sanitizing_filename_for_namespacing()
    {
        $badName = "This.is-a bad name&@$3";
        $correctedName = "This_is_a_bad_name3";

        $fileLoader = new FileLoader();

        $this->assertEquals($correctedName, $fileLoader->sanitizeNamespace($badName), "failed to sanitize name");
    }

    public function test_using_explicit_namespaces()
    {
        $fileLoader = new FileLoader();
        $fileLoader->addFiles([
            new \SplFileInfo($GLOBALS['test_config']['test_dir'] . '/Fixtures/FilesWithGoodData/jsonConfig.json'),
            [new \SplFileInfo($GLOBALS['test_config']['test_dir'] . '/Fixtures/FilesWithGoodData/phpConfig.php'), 'customNs']
        ]);

        $expected['jsonConfig'] = $this->testData;
        $expected['customNs'] = $this->testData;

        $actual = $fileLoader->process();

        $this->assertEquals($expected, $actual, "failed to custom namespace the second file");
    }

    public function test_using_no_namespaces()
    {
        $fileLoader = new FileLoader();
        $fileLoader->addFiles([
            new \SplFileInfo($GLOBALS['test_config']['test_dir'] . '/Fixtures/FilesWithGoodData/jsonConfig.json')
        ]);

        $expected = $this->testData;

        $actual = $fileLoader->process(false);

        $this->assertEquals($expected, $actual, "failed to disable namespacing");
    }

    public function test_loading_files_as_a_path()
    {
        $fileLoader = new FileLoader();
        $fileLoader->addFiles([
            $GLOBALS['test_config']['test_dir'] . '/Fixtures/FilesWithGoodData/yamlConfig.yaml',
            [$GLOBALS['test_config']['test_dir'] . '/Fixtures/FilesWithGoodData/phpConfig.php', 'customNs'],
            new \SplFileInfo($GLOBALS['test_config']['test_dir'] . '/Fixtures/FilesWithGoodData/jsonConfig.json'),
        ]);

        $expected['yamlConfig'] = $this->testData;
        $expected['customNs'] = $this->testData;
        $expected['jsonConfig'] = $this->testData;

        $actual = $fileLoader->process();

        $this->assertEquals($expected, $actual, "failed to custom namespace the second file");
    }

    public function test_throws_exception_for_invalid_file_load()
    {
        $this->setExpectedException('\RuntimeException');

        $fileLoader = new FileLoader();
        $file = new \SplFileInfo('nogo.nope');

        $fileLoader->getFileContents($file);
    }
}
