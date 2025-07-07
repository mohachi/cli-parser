<?php

namespace Mohachi\CliParser\Parser;

use Mohachi\CliParser\Exception\InvalidArgumentException;
use Mohachi\CliParser\TokenQueue;
use stdClass;

class OptionParser implements ParserInterface
{
    
    public function __construct(
        readonly string $name,
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
