<?php

namespace Mohachi\CommandLine\SyntaxTree;

use Mohachi\CommandLine\SyntaxTree\Identifier\AbstractIdentifierNode;

class CommandNode implements NodeInterface
{
    
    public function __construct(
        public string $name,
        public AbstractIdentifierNode $id,
        public OptionsNode $options,
        public ArgumentsNode $arguments
    )
    {
        
    }
    
}
