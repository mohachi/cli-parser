<?php

namespace Mohachi\CommandLine\SyntaxTree;

use Mohachi\CommandLine\SyntaxTree\Identifier\AbstractIdentifierNode;

class OptionNode implements NodeInterface
{
    
    public function __construct(
        public string $name,
        public AbstractIdentifierNode $id,
        public ArgumentsNode $arguments
    )
    {
        
    }
    
}
