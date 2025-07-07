#!/bin/env php
<?php

use Mohachi\CommandLine\IdTokenizer\LiteralIdTokenizer;
use Mohachi\CommandLine\IdTokenizer\LongIdTokenizer;
use Mohachi\CommandLine\IdTokenizer\ShortIdTokenizer;
use Mohachi\CommandLine\Lexer;
use Mohachi\CommandLine\Command;
use Mohachi\CommandLine\Option;
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
    
    $tokenizers = [
        "long" => new LongIdTokenizer,
        "short" => new ShortIdTokenizer,
        "literal" => new LiteralIdTokenizer,
    ];
    
    $lexer = new Lexer;
    $cmd["parser"] = new Command($cmd["name"]);
    
    foreach( $cmd["ids"] as $type => $id )
    {
        $cmd["parser"]->id($id);
        $tokenizers[$type]->append($id);
    }
    
    foreach( $cmd["options"] as $name => $option )
    {
        $parser = new Option($name);
        
        foreach( $option["ids"] as $type => $id )
        {
            $parser->id($id);
            $tokenizers[$type]->append($id);
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
    
    foreach( $tokenizers as $name => $tokenizer )
    {
        $lexer->append($name, $tokenizer);
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
