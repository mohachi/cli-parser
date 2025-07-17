<?php

use Mohachi\CliParser\IdTokenizer\LongIdTokenizer;
use Mohachi\CliParser\IdTokenizer\ShortIdTokenizer;
use Mohachi\CliParser\Lexer;

require_once __DIR__ . "/vendor/autoload.php";

$lexer = new Lexer;
$lexer->register(new LongIdTokenizer);
$lexer->register(new ShortIdTokenizer);
$lexer->get(ShortIdTokenizer::class)->create("-l");
$lexer->get(ShortIdTokenizer::class)->create("-a");
$lexer->get(LongIdTokenizer::class)->create("--color");

$line = ["ls", "-al", "--color=none", "/var/log"];
$lexer->consume($line);

foreach( $lexer as $token ) 
{
    dump($token);
}
