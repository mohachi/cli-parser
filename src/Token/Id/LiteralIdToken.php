<?php

namespace Mohachi\CliParser\Token\Id;

use Mohachi\CliParser\Exception\InvalidArgumentException;

class LiteralIdToken implements IdTokenInterface
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
