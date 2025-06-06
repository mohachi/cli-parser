<?php

namespace Mohachi\CommandLine\SyntaxTree;

trait LeafNodeTrait
{
    
    protected string $value;
    
    public function __toString(): string
    {
        return $this->value;
    }
    
}
