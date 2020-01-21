<?php

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/ApiTest.php';

$test = new ApiTest;

$test->testMethodIsAvailable();
$test->testResponseTime(10);
$test->testLoad(20);