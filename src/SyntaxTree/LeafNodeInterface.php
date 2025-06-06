<?php

namespace Mohachi\CommandLine\SyntaxTree;

interface LeafNodeInterface extends NodeInterface
{
    
    public function getValue(): string;
    public function setValue(string $value);
    
}
