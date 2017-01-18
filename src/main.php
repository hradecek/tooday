<?php

// Note: This simple utility is for testing purposes only!
require_once 'Tooday/Parser/RideParser.php';
require_once 'Tooday/Parser/RideParserImpl.php';
require_once 'Tooday/Exceptions/ParserException.php';

$handle = fopen('php://stdin', 'r');
$parser = new Tooday\Parser\RideParserImpl;

echo 'Test case: ';
while ($test = trim(fgets($handle))) {
    if (!$test) break;
    $where = $parser->where($test);
    print_r($where);

    $when = $parser->when($test);
    $when = $parser->when($test);
    print_r($when);

    echo "\nTest case: ";
}

