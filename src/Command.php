<?php

namespace Mohachi\CommandLine;

use Mohachi\CommandLine\Exception\InvalidArgumentException;
use Mohachi\CommandLine\TokenQueue;
use stdClass;

class Command
{
    use IdParserTrait, ArgumentsParserTrait, OptionsParserTrait;
    
    public function __construct(private string $name)
    {
        if( "" == $name )
        {
            throw new InvalidArgumentException();
        }
    }
    
    public function parse(TokenQueue $queue): stdClass
    {
        $command = new stdClass;
        $command->name = $this->name;
        $command->id = $this->parseId($queue);
        $command->options = $this->parseOptions($queue);
        $command->arguments = $this->parseArguments($queue);
        return $command;
    }
    
}
