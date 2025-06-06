<?php

namespace Mohachi\CommandLine\SyntaxTree;

use Mohachi\CommandLine\Exception\InvalidArgumentException;

class LongIdentifierNode implements IdentifierNodeInterface
{
    use LeafNodeTrait;
    
    public function __construct(protected string $value)
    {
        if( "" == $value )
        {
            throw new InvalidArgumentException();
        }
        
        if( ! str_starts_with($value, "--") )
        {
            $value = "--$value";
        }
        elseif( "-" == $value[0] )
        {
            $value = "-$value";
        }
        
        $this->value = $value;
    }
    
}
