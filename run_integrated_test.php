#!/bin/env php
<?php

use Mohachi\CliParser\Command;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . "/vendor/autoload.php";

/* Filteration */

$files = [];
$filters = array_slice($argv, 1);
$dir = __DIR__ . "/tests/integration";

if( empty($filters) )
{
    $filters[] = "*";
}

foreach( $filters as $filter )
{
    $files = array_merge($files, glob($dir ."/". trim($filter, "/")));
}

array_unique($files);

foreach( $files as $file )
{
    include_once $file;
    
    /* Run tests */
    
    /**
     * @var Command $cmd
     */
    foreach( $examples as $i => $example )
    {
        $syntax = $cmd->parse($example["line"]);
        TestCase::assertEquals($example["syntax"], $syntax);
    }
    
    echo "[\e[32mSuccess\e[0m] \e[33m$file\e[0m", PHP_EOL;
}
