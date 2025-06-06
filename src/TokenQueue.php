<?php

namespace Mohachi\CommandLine;

use Mohachi\CommandLine\Exception\UnderflowException;
use Mohachi\CommandLine\SyntaxTree\LeafNodeInterface;

class TokenQueue
{
    
    /**
     * @var LeafNodeInterface[] $buffer
     */
    private array $buffer;
    
    public function push(LeafNodeInterface $node)
    {
        $this->buffer[] = $node;
    }
    
    public function getHead(): LeafNodeInterface
    {
        if( empty($this->buffer) )
        {
            throw new UnderflowException();
        }
        
        return $this->buffer[0];
    }
    
    public function pull(): LeafNodeInterface
    {
        if( empty($this->buffer) )
        {
            throw new UnderflowException();
        }
        
        return array_shift($this->buffer);
    }
    
}
