<?php

namespace Mohachi\CommandLine\Parser;

use Mohachi\CommandLine\Exception\ParserException;
use Mohachi\CommandLine\SyntaxTree\Identifier\AbstractIdentifierNode;
use Mohachi\CommandLine\TokenQueue;

class IdentifierParser implements ParserInterface
{
    
    /**
     * @var AbstractIdentifierNode[] $nodes
     */
    private array $nodes = [];
    
    public function append(AbstractIdentifierNode $node)
    {
        $this->nodes[] = $node;
    }
    
    public function parse(TokenQueue $tokens): AbstractIdentifierNode
    {
        foreach( $this->nodes as $node )
        {
            if( $node === $tokens->getHead() )
            {
                return $tokens->pull();
            }
        }
        
        throw new ParserException();
    }
    
}
