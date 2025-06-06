<?php

use Mohachi\CommandLine\Parser\CommandParser;
use Mohachi\CommandLine\Parser\OptionParser;
use Mohachi\CommandLine\SyntaxTree\LiteralIdentifierNode;
use Mohachi\CommandLine\SyntaxTree\LongIdentifierNode;
use Mohachi\CommandLine\Tokenizer;

require_once __DIR__ . "/../../vendor/autoload.php";

$idNodes = [
    "binary" => new LongIdentifierNode("binary"),
    "check" => new LongIdentifierNode("check"),
    "help" => new LongIdentifierNode("help"),
    "ignore-missing" => new LongIdentifierNode("ignore-missing"),
    "quiet" => new LongIdentifierNode("quiet"),
    "sha256sum" => new LiteralIdentifierNode("sha256sum"),
    "status" => new LongIdentifierNode("status"),
    "strict" => new LongIdentifierNode("strict"),
    "tag" => new LongIdentifierNode("tag"),
    "text" => new LongIdentifierNode("text"),
    "version" => new LongIdentifierNode("version"),
    "warn" => new LongIdentifierNode("warn"),
    "zero" => new LongIdentifierNode("zero"),
];

$tokenizer = new Tokenizer();
$parser = new CommandParser("sha256sum", $idNodes["sha256sum"]);
$parser->arguments->append("FILE");

foreach( $idNodes as $name => $id )
{
    $tokenizer->appendIdentifier($id);
    $parser->options->append(new OptionParser($name, $id));
    
}

$line = [
    "sha256sum",
    "--ignore-missing",
    "--check",
    "--quiet",
    "path/to/file.sha256"
];

$tokens = $tokenizer->tokenize($line);
$node = $parser->parse($tokens);

dump($node);
