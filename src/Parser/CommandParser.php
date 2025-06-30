<?php

namespace Mohachi\CommandLine\Parser;

use Mohachi\CommandLine\Exception\InvalidArgumentException;
use Mohachi\CommandLine\TokenQueue;
use stdClass;

class CommandParser implements ParserInterface
{
    
    
    public function __construct(
        private string $name,
        readonly IdParser $id = new IdParser,
        readonly OptionsParser $options = new OptionsParser,
        readonly ArgumentsParser $arguments = new ArgumentsParser,
    )
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
        $command->id = $this->id->parse($queue);
        $command->options = $this->options->parse($queue);
        $command->arguments = $this->arguments->parse($queue);
        return $command;
    }
    
}
