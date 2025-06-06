<?php

namespace Mohachi\CommandLine\SyntaxTree\Identifier;

use Mohachi\CommandLine\SyntaxTree\LeafNodeInterface;

abstract class AbstractIdentifierNode implements LeafNodeInterface
{
    
    public function __construct(protected string $value)
    {
        $this->setValue($value);
    }
    
    public function getValue(): string
    {
        return $this->value;
    }
    
    abstract public function setValue(string $value);
    
}
