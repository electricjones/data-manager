<?php
namespace Michaels\Manager\Test\Traits;

use Michaels\Manager\Test\Bags\FileBagTestTrait;
use Michaels\Manager\Manager;

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
        $goodFileData =  $this->setFilesToSplInfoObjects($goodTestFileDirectory);
        $this->manager = new Manager();
        $this->manager->loadFiles($goodFileData);

        $this->assertEquals($this->defaultArray, $this->manager->all());

    }
}