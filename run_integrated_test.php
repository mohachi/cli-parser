#!/bin/env php
<?php

use Mohachi\CliParser\IdTokenizer\LiteralIdTokenizer;
use Mohachi\CliParser\IdTokenizer\LongIdTokenizer;
use Mohachi\CliParser\IdTokenizer\ShortIdTokenizer;
use Mohachi\CliParser\Lexer;
use Mohachi\CliParser\Command;
use Mohachi\CliParser\Option;
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
    $lexer->register(new LongIdTokenizer);
    $lexer->register(new ShortIdTokenizer);
    $lexer->register(new LiteralIdTokenizer);
    $cmd["parser"] = new Command($cmd["name"]);
    
    foreach( $cmd["ids"] as $type => $id )
    {
        $cmd["parser"]->id($lexer->get($type)->create($id));
    }
    
    foreach( $cmd["options"] as $name => $option )
    {
        $parser = new Option($name);
        
        foreach( $option["ids"] as $type => $id )
        {
            $parser->id($lexer->get($type)->create($id));
        }
        
        if( isset($option["arguments"]) )
        {
            foreach( $option["arguments"] as $name => $criterion )
            {
                $parser->arg($name, $criterion);
            }
        }
        
        $cmd["parser"]->opt($parser);
    }
    
    foreach( $cmd["arguments"] as $name => $criterion )
    {
        $cmd["parser"]->arg($name, $criterion);
    }
    
    /* Run tests */
    
    foreach( $examples as $i => $example )
    {
        $queue = $lexer->lex($example["line"]);
        TestCase::assertEquals($example["expected"]["queue"], $queue);
        
        $syntax = $cmd["parser"]->parse($queue);
        TestCase::assertEquals($example["expected"]["syntax"], $syntax);
    }
    
    echo "[\e[32mSuccess\e[0m] \e[33m$file\e[0m", PHP_EOL;
}
