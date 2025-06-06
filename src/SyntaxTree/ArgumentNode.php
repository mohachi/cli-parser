<?php

namespace Mohachi\CommandLine\SyntaxTree;

class ArgumentNode implements LeafNodeInterface
{
    
    public string $value;
    
    public function getValue(): string
    {
        return $this->value;
    }
    
    public function setValue(string $value)
    {
        $this->value = $value;
    }
    
}
