<?php

namespace Mohachi\CommandLine\SyntaxTree;

readonly class OptionNode implements NodeInterface
{
    
    public function __construct(
        public string $name,
        public IdentifierNodeInterface $id,
        public ArgumentsNode $arguments
    )
    {
        
    }
    
}
