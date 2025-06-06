<?php

namespace Mohachi\CommandLine\SyntaxTree;

trait IteratorTrait
{
    
    private array $nodes = [];
    
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->nodes[$offset]);
    }
    
    public function offsetGet(mixed $offset): mixed
    {
        return $this->nodes[$offset];
    }
    
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->append($offset, $value);
    }
    
    public function offsetUnset(mixed $offset): void
    {
        unset($this->nodes[$offset]);
    }
    
    public function current(): mixed
    {
        return current($this->nodes);
    }
    
    public function key(): mixed
    {
        return key($this->nodes);
    }
    
    public function next(): void
    {
        next($this->nodes);
    }
    
    public function rewind(): void
    {
        reset($this->nodes);
    }
    
    public function valid(): bool
    {
        return null !== $this->key();
    }
    
    public function count(): int
    {
        return count($this->nodes);
    }
    
}
