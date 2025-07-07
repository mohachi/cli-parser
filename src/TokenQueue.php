<?php

namespace Mohachi\CliParser;

use Mohachi\CliParser\Exception\UnderflowException;
use Mohachi\CliParser\Token\TokenInterface;

class TokenQueue
{
    
    /**
     * @var TokenInterface[] $tokens
     */
    private array $tokens = [];
    
    public function isEmpty()
    {
        return empty($this->tokens);
    }
    
    public function enqueue(TokenInterface $token)
    {
        $this->tokens[] = $token;
    }
    
    public function getHead(): TokenInterface
    {
        if( empty($this->tokens) )
        {
            throw new UnderflowException();
        }
        
        return $this->tokens[0];
    }
    
    public function dequeue(): TokenInterface
    {
        if( empty($this->tokens) )
        {
            throw new UnderflowException();
        }
        
        return array_shift($this->tokens);
    }
    
}
