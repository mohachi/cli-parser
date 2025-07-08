<?php

namespace Mohachi\CliParser\Token;

use Stringable;

abstract class AbstractToken implements Stringable
{
    
    public function __construct(private string $value)
    {
        
    }
    
    public function __toString(): string
    {
        return $this->value;
    }
    
}
