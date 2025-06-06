<?php

namespace Mohachi\CommandLine\SyntaxTree;

use Mohachi\CommandLine\Exception\InvalidArgumentException;

class LiteralIdentifierNode implements IdentifierNodeInterface
{
    use LeafNodeTrait;
    
    public function __construct(protected string $value)
    {
        if( "" == $value || "-" == $value[0] )
        {
            throw new InvalidArgumentException();
        }
        
        $this->value = $value;
    }
    
}
