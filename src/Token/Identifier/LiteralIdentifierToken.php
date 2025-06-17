<?php

namespace Mohachi\CommandLine\Token\Identifier;

use Mohachi\CommandLine\Exception\InvalidArgumentException;

class LiteralIdentifierToken implements IdentifierTokenInterface
{
    
    public function __construct(public string $value)
    {
        if( "" == $value || "-" == $value[0] )
        {
            throw new InvalidArgumentException();
        }
        
        $this->value = $value;
    }
    
    public function __toString(): string
    {
        return $this->value;
    }
    
}
