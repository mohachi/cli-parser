<?php

namespace Mohachi\CommandLine\SyntaxTree;

use ArrayAccess;
use Countable;
use Iterator;

class ArgumentsNode implements NodeInterface, ArrayAccess, Iterator, Countable
{
    use IteratorTrait;
    
    /**
     * @var ArgumentNode[] $nodes
     */
    private array $nodes = [];
    
    public function append(string $name, ArgumentNode $node)
    {
        $this->nodes[$name] = $node;
    }
    
}
