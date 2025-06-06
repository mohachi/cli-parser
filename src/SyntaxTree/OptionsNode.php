<?php

namespace Mohachi\CommandLine\SyntaxTree;

use ArrayAccess;
use Countable;
use Iterator;

class OptionsNode implements NodeInterface, ArrayAccess, Iterator, Countable
{
    use IteratorTrait;
    
    /**
     * @var OptionNode[] $nodes
     */
    private array $nodes = [];
    
    public function append(OptionNode $node)
    {
        $this->nodes[] = $node;
    }
    
}
