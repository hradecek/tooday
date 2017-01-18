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
     * Default sample cities used in test data.
     */
    const TEST_CITIES = [
        'from' => 'CityOne',
        'to'   => 'CityTwo'
    ];
    
    /**
     * Path to dummy data file
     */
    const TEST_DATA = [
        'where' => 'tests/resources/whereTestCaseData.test'
    ];
    
    /**
     * @var Tooday\Parser\RideParser parser
     */
    private $parser;

    /**
     * @var array testing data
     */
    private static $posts;

    /**
     * Set up test class
     */
    public static function setUpBeforeClass()
    {
        self::$posts = self::readDummyDataFile(self::TEST_DATA['where']);
    }
    
    // Read test data file
    // See tests/resources folder for format example
    private static function readDummyDataFile($filePath)
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
     * Set up test
     */
    public function setUp()
    {
        parent::setUp();
        $this->parser = new RideParserImpl;
    }

    /**
     * @group where
     */
    public function testWhereDefinedWithArrow()
    {
        foreach (self::$posts as $post) {
            $where = $this->parser->where($post);
            $this->assertWhere(self::TEST_CITIES, $where, $post);
        }
    }
    
    public function testWhenBasic()
    {
        $this->assertTrue(true);
    }

    private function assertWhere($expected, $actual, $post)
    {
        $this->assertArrayHasKey('from', $actual, "<from> should be parsed for:\n$post");
        $this->assertArrayHasKey('to', $actual, "<to> should be parsed for:\n$post");
        $this->assertEquals($expected['from'], $actual['from'], "<from> was should be same for:\n$post");
        $this->assertEquals($expected['to'], $actual['to'], "<to> was should be same for:\n$post");
    }
}

