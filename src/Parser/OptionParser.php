<?php

namespace Mohachi\CommandLine\Parser;

use Mohachi\CommandLine\Exception\InvalidArgumentException;
use Mohachi\CommandLine\TokenQueue;
use stdClass;

class OptionParser implements ParserInterface
{
    
    
    public function __construct(
        private string $name,
        readonly IdParser $id = new IdParser,
        readonly ArgumentsParser $arguments = new ArgumentsParser
    )
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
        $option->id = $this->id->parse($queue);
        $option->arguments = $this->arguments->parse($queue);
        return $option;
    }
    
}
