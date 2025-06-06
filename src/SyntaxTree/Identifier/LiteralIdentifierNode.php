<?php

namespace Mohachi\CommandLine\SyntaxTree\Identifier;

use Mohachi\CommandLine\Exception\InvalidArgumentException;
use Override;

class LiteralIdentifierNode extends AbstractIdentifierNode
{
    
    #[Override]
    public function setValue(string $value)
    {
        if( "" == $value || "-" == $value[0] )
        {
            throw new InvalidArgumentException();
        }
        
        $this->value = $value;
    }
    
}
