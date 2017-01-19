<?php

use PHPUnit\Framework\TestCase;
use Tooday\Parser\RideParserImpl;

/**
 * Class RideParserTestCase
 * 
 * @author Ivo Hradek <ivohradek@gmail.com>
 */
class RideParserTestCase extends TestCase
{
    /**
     * Name of 'where' group test cases
     */
    const GROUP_WHERE = 'where';
    /**
     * Name of 'when' group test cases
     */
    const GROUP_WHEN = 'when';
    
    /**
     * Default sample cities used in test data.
     */
    const TEST_CITIES = [
        'from'    => 'CityFrom',
        'through' => ['CityThroughOne', 'CityThroughTwo'],
        'to'      => 'CityTo'
    ];

    /**
     * Default 'when' sample used in test data.
     */
    const TEST_SAMPLE_WHEN = [
        'date' => '2017-01-12',
        'time' => '9:00'
    ];
    
    /**
     * Path to dummy data file
     */
    const TEST_DATA = [
        self::GROUP_WHERE => 'tests/resources/whereTestCaseData.test',
        self::GROUP_WHEN  => 'tests/resources/whenTestCaseData.test'
    ];
    
    /**
     * @var Tooday\Parser\RideParser parser
     */
    private $parser;

    /**
     * @var array testing data
     */
    private $posts;

    /**
     * Set up test
     * 
     * @before
     */
    public function setUp()
    {
        parent::setUp();
        $this->parser = new RideParserImpl;
        
        $groups = $this->getGroups();
        if (array_search(self::GROUP_WHERE, $groups)) {
            $path = self::TEST_DATA[self::GROUP_WHERE];
            $this->posts = $this->readDummyDataFile($path);
        } else if (array_search(self::GROUP_WHEN, $groups)) {
            $path = self::TEST_DATA[self::GROUP_WHEN];
            $this->posts = $this->readDummyDataFile($path);
        }
    }
    
    // Read test data file
    // See tests/resources folder for format example
    private function readDummyDataFile($filePath)
    {
        $fileHandle = fopen($filePath, 'r');
        if (!$fileHandle) {
            throw new Exception('File ' . $filePath. ' was not found');
        }

        $data = [];
        $post = '';
        while (false !== ($line = fgets($fileHandle))) {
            // Filter comments
            if ('#' === substr($line, 0, 1)) {
                continue;
            }
            // End of test-case
            if ('' === trim($line)) {
                array_push($data, $post);
                $post = '';
                continue;
            }
            
            $post .= $line;
        }

        fclose($fileHandle);
        return $data;
    }

    /**
     * @group where
     */
    public function testWhereBasicFuncionality()
    {
        foreach ($this->posts as $post) {
            $where = $this->parser->where($post);
            $this->assertWhere(self::TEST_CITIES, $where, $post);
        }
    }

    private function assertWhere($expected, $actual, $post)
    {
        $this->assertArrayHasKey('from', $actual, "<from> should be parsed for:\n$post");
        $this->assertArrayHasKey('to', $actual, "<to> should be parsed for:\n$post");
        
        $this->assertEquals($expected['from'], $actual['from'], "<from> should be same for:\n$post");
        $this->assertEquals($expected['to'], $actual['to'], "<to> should be same for:\n$post");
        
        if (array_key_exists('through', $actual)) {
            $this->assertEquals($expected['through'][0], $actual['through'][0], "<through> should be same for:\n$post");
            $this->assertEquals($expected['through'][1], $actual['through'][1], "<through> should be same for:\n$post");
        }
    }
    
    /**
     * @group when
     */
    public function testWhereBasicFunctionality()
    {
        foreach ($this->posts as $post) {
            $when = $this->parser->when($post);
            $this->assertWhen(self::TEST_SAMPLE_WHEN, $when, $post);
        }
    }
    
    private function assertWhen($expected, $actual, $post)
    {
        if (array_key_exists('time', $actual)) {
            $this->assertEquals($expected['time'], $actual['time'], "<time> should be same for: $post");
        }
        if (array_key_exists('date', $actual)) {
            $this->assertEquals($expected['date'], $actual['date'], "<date> should be same for: $post");
        }
    }
}

