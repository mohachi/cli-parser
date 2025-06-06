<?php

namespace Mohachi\CommandLine\Parser;

use Mohachi\CommandLine\Exception\InvalidArgumentException;
use Mohachi\CommandLine\SyntaxTree\CommandNode;
use Mohachi\CommandLine\SyntaxTree\Identifier\AbstractIdentifierNode;
use Mohachi\CommandLine\TokenQueue;

class CommandParser implements ParserInterface
{
    
    readonly IdentifierParser $id;
    readonly OptionsParser $options;
    readonly ArgumentsParser $arguments;
    
    public function __construct(private string $name, AbstractIdentifierNode $id)
    {
        if( "" == $name )
        {
            throw new InvalidArgumentException();
        }
        
        $this->id = new IdentifierParser;
        $this->options = new OptionsParser;
        $this->arguments = new ArgumentsParser;
        $this->id->append($id);
    }
    
    public function parse(TokenQueue $tokens): CommandNode
    {
        return new CommandNode(
            $this->name,
            $this->id->parse($tokens),
            $this->options->parse($tokens),
            $this->arguments->parse($tokens)
        );
    }
    
}
