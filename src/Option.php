<?php

namespace Mohachi\CliParser;

use Mohachi\CliParser\Exception\InvalidArgumentException;
use stdClass;

class Option extends Component
{
    
    public function __construct(readonly string $name, Lexer $lexer)
    {
        if( "" == $name )
        {
            throw new InvalidArgumentException();
        }
        
        $this->lexer = $lexer;
    }
    
    public function parse(): stdClass
    {
        $option = new stdClass;
        $option->name = $this->name;
        $option->id = $this->parseId();
        $option->arguments = $this->parseArguments();
        return $option;
    }
    
}
