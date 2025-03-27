<?php
function testDebug($message) {
    $result = "Debug test: " . $message;
    return $result;
}

// This line will be our breakpoint
$message = testDebug("Hello World!");
echo $message; 