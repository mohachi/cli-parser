<?php

namespace Mohachi\CommandLine\SyntaxTree\Identifier;

use Mohachi\CommandLine\Exception\InvalidArgumentException;
use Override;

class LongIdentifierNode extends AbstractIdentifierNode
{
    
    #[Override]
    public function setValue(string $value)
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
