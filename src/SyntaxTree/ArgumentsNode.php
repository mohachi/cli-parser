<?php

namespace Mohachi\CommandLine\SyntaxTree;

class ArgumentsNode implements NodeInterface
{
    
    /**
     * @var ArgumentNode[] $nodes
     */
    public array $nodes = [];
    
    public function append(string $name, ArgumentNode $node)
    {
        $this->nodes[$name] = $node;
    }
    
}
