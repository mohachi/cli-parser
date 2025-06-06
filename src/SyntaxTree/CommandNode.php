<?php

namespace Mohachi\CommandLine\SyntaxTree;

readonly class CommandNode implements NodeInterface
{
    
    public function __construct(
        public string $name,
        public IdentifierNodeInterface $id,
        public OptionsNode $options,
        public ArgumentsNode $arguments
    )
    {
        
    }
    
}
