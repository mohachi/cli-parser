<?php

namespace Mohachi\CliParser;

use Mohachi\CliParser\Exception\InvalidArgumentException;
use Mohachi\CliParser\IdTokenizer\LiteralIdTokenizer;
use Mohachi\CliParser\IdTokenizer\LongIdTokenizer;
use Mohachi\CliParser\IdTokenizer\ShortIdTokenizer;
use stdClass;

class Command extends Context
{
    
    public function __construct(private string $name, ?Lexer $lexer = null)
    {
        if( "" == $name )
        {
            throw new InvalidArgumentException();
        }
        
        if( null === $lexer )
        {
            $lexer = new Lexer;
            $lexer->register(new LongIdTokenizer, "long");
            $lexer->register(new ShortIdTokenizer, "short");
            $lexer->register(new LiteralIdTokenizer, "literal");
        }
        
        $this->lexer = $lexer;
    }
    
    public function parse(array &$argv): stdClass
    {
        $this->lexer->consume($argv);
        
        $command = new stdClass;
        $command->name = $this->name;
        $command->id = $this->parseId();
        $command->options = $this->parseOptions();
        $command->arguments = $this->parseArguments();
        return $command;
    }
    
}
