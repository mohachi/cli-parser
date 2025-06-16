<?php

namespace Mohachi\CommandLine\Token\Identifier;

use Mohachi\CommandLine\Exception\InvalidArgumentException;

class ShortIdentifierToken implements IdentifierTokenInterface
{
    
    public function __construct(public string $value)
    {
        if( str_starts_with($value, "-") )
        {
            $value = substr($value, 1);
        }
        
        if( strlen($value) != 1 )
        {
            throw new InvalidArgumentException();
        }
        
        $this->value = $value;
    }
    
    public function __toString(): string
    {
        return "-" . $this->value;
    }
    
}
