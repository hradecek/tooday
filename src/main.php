<?php

// Note: This simple utility is for testing purposes only!
require_once 'Tooday/Parser/RideParserImpl.php';

$handle = fopen('php://stdin', 'r');
$parser = new Tooday\Parser\RideParserImpl;

echo 'Test case: ';
while ($test = trim(fgets($handle))) {
    if (!$test) break;
    $when = $parser->when($test);
    $destination = $parser->where($test);
    echo "Date: ${when['date']}\n";
    echo "From: ${destination['from']}\n";
    echo "To: ${destination['to']}\n";
    echo "\n";
    echo 'Test case: ';
}

