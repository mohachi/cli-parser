<?php

namespace Mohachi\CommandLine\Parser;

use Mohachi\CommandLine\Exception\ParserException;
use Mohachi\CommandLine\SyntaxTree\IdentifierNodeInterface;
use Mohachi\CommandLine\TokenQueue;

class IdentifierParser implements ParserInterface
{
    
    /**
     * @var IdentifierNodeInterface[] $nodes
     */
    private array $nodes = [];
    
    public function append(IdentifierNodeInterface $node)
    {
        $this->nodes[] = $node;
    }
    
    public function parse(TokenQueue $tokens): IdentifierNodeInterface
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
