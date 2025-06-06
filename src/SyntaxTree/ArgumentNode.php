<?php

namespace Mohachi\CommandLine\SyntaxTree;

class ArgumentNode implements LeafNodeInterface
{
    use LeafNodeTrait;
    
    public function __construct(protected string $value)
    {
        
    }
    
}
