<?php

namespace Mohachi\CommandLine\Parser;

use Mohachi\CommandLine\Exception\InvalidArgumentException;
use Mohachi\CommandLine\SyntaxTree\Identifier\AbstractIdentifierNode;
use Mohachi\CommandLine\SyntaxTree\OptionNode;
use Mohachi\CommandLine\TokenQueue;

class OptionParser implements ParserInterface
{
    
    readonly IdentifierParser $id;
    readonly ArgumentsParser $arguments;
    
    public function __construct(private string $name, AbstractIdentifierNode $id)
    {
        if( "" == $name )
        {
            throw new InvalidArgumentException();
        }
        
        $this->id = new IdentifierParser;
        $this->arguments = new ArgumentsParser;
        $this->id->append($id);
    }
    
    public function parse(TokenQueue $tokens): OptionNode
    {
        return new OptionNode(
            $this->name,
            $this->id->parse($tokens),
            $this->arguments->parse($tokens)
        );
    }
    
}
