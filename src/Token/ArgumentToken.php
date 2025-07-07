<?php

namespace Mohachi\CliParser\Token;

class ArgumentToken implements TokenInterface
{
    
    public function __construct(public string $value)
    {
        
    }
    
    public function __toString(): string
    {
        return $this->value;
    }
    
}
