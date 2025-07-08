<?php

namespace Mohachi\CliParser;

use Mohachi\CliParser\Exception\UnderflowException;
use Mohachi\CliParser\Token\AbstractToken;

class TokenQueue
{
    
    /**
     * @var AbstractToken[] $tokens
     */
    private array $tokens = [];
    
    public function isEmpty()
    {
        return empty($this->tokens);
    }
    
    public function enqueue(AbstractToken $token)
    {
        $this->tokens[] = $token;
    }
    
    public function getHead(): AbstractToken
    {
        if( empty($this->tokens) )
        {
            throw new UnderflowException();
        }
        
        return $this->tokens[0];
    }
    
    public function dequeue(): AbstractToken
    {
        if( empty($this->tokens) )
        {
            throw new UnderflowException();
        }
        
        return array_shift($this->tokens);
    }
    
}
