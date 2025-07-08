<?php

namespace Mohachi\CliParser;

use Mohachi\CliParser\Exception\InvalidArgumentException;
use Mohachi\CliParser\TokenQueue;
use stdClass;

class Command extends Context
{
    
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
