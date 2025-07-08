<?php

namespace Mohachi\CliParser;

use Mohachi\CliParser\Exception\InvalidArgumentException;
use Mohachi\CliParser\Exception\ParserException;
use Mohachi\CliParser\Token\ArgumentToken;
use Mohachi\CliParser\Token\IdToken;
use stdClass;

abstract class Component
{
    
    /**
     * @var list<IdToken> $tokens
     */
    private array $tokens = [];
    
    /**
     * @var array<string,callable> $arguments
     */
    private array $arguments = [];
    
    public function id(IdToken $token): self
    {
        $this->tokens[] = $token;
        
        return $this;
    }
    
    public function arg(string $name, ?callable $criterion = null): self
    {
        if( "" == $name || isset($this->arguments[$name]) )
        {
            throw new InvalidArgumentException();
        }
        
        $this->arguments[$name] = $criterion ?? fn() => true;
        
        return $this;
    }
    
    public function parseId(TokenQueue $queue):  string
    {
        if( in_array($queue->getHead(), $this->tokens, true) )
        {
            return (string) $queue->dequeue();
        }
        
        throw new ParserException();
    }
    
    public function parseArguments(TokenQueue $queue): stdClass
    {
        $arguments = [];
        
        foreach( $this->arguments as $name => $criterion )
        {
            if( ! $queue->getHead() instanceof ArgumentToken )
            {
                throw new ParserException();
            }
            
            if( ! call_user_func($criterion, (string) $queue->getHead()) )
            {
                throw new ParserException();
            }
            
            $arguments[$name] = (string) $queue->dequeue();
        }
        
        return (object) $arguments;
    }
    
}
