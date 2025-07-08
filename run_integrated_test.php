#!/bin/env php
<?php

use Mohachi\CliParser\IdTokenizer\LiteralIdTokenizer;
use Mohachi\CliParser\IdTokenizer\LongIdTokenizer;
use Mohachi\CliParser\IdTokenizer\ShortIdTokenizer;
use Mohachi\CliParser\Lexer;
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
    
    /* Automation */
    
    $lexer = new Lexer;
    $command = new Command($cmd["name"]);
    $lexer->register(new LongIdTokenizer);
    $lexer->register(new ShortIdTokenizer);
    $lexer->register(new LiteralIdTokenizer);
    
    foreach( $cmd["ids"] as $type => $id )
    {
        $command->id($lexer->get($type)->create($id));
    }
    
    foreach( $cmd["options"] as $name => $opt )
    {
        $options = $command->opt($name);
        
        foreach( $opt["ids"] as $type => $id )
        {
            $options->id($lexer->get($type)->create($id));
        }
        
        if( isset($opt["arguments"]) )
        {
            foreach( $opt["arguments"] as $name => $criterion )
            {
                $options->arg($name, $criterion);
            }
        }
    }
    
    foreach( $cmd["arguments"] as $name => $criterion )
    {
        $command->arg($name, $criterion);
    }
    
    /* Run tests */
    
    foreach( $examples as $i => $example )
    {
        $queue = $lexer->lex($example["line"]);
        TestCase::assertEquals($example["expected"]["queue"], $queue);
        
        $syntax = $command->parse($queue);
        TestCase::assertEquals($example["expected"]["syntax"], $syntax);
    }
    
    echo "[\e[32mSuccess\e[0m] \e[33m$file\e[0m", PHP_EOL;
}
