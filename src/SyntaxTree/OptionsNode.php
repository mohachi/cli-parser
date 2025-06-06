<?php

namespace Mohachi\CommandLine\SyntaxTree;

class OptionsNode implements NodeInterface
{
    
    /**
     * @var OptionNode[] $nodes
     */
    public array $nodes = [];
    
    public function append(OptionNode $node)
    {
        $this->nodes[] = $node;
    }
    
}
