<?php

namespace Mohachi\CommandLine;

use Mohachi\CommandLine\Exception\InvalidArgumentException;
use Mohachi\CommandLine\TokenQueue;
use stdClass;

class Option
{
    use IdParserTrait, ArgumentsParserTrait;
    
    public function __construct(readonly string $name)
    {
        if( "" == $name )
        {
            throw new InvalidArgumentException();
        }
    }
    
    public function parse(TokenQueue $queue): stdClass
    {
        $option = new stdClass;
        $option->name = $this->name;
        $option->id = $this->parseId($queue);
        $option->arguments = $this->parseArguments($queue);
        return $option;
    }
    
}
