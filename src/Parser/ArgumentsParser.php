<?php

namespace Mohachi\CommandLine\Parser;

use Mohachi\CommandLine\Exception\InvalidArgumentException;
use Mohachi\CommandLine\Exception\ParserException;
use Mohachi\CommandLine\Token\ArgumentToken;
use Mohachi\CommandLine\TokenQueue;
use stdClass;

class ArgumentsParser implements ParserInterface
{
    
    private array $criteria = [];
    
    public function append(string $name, ?callable $criterion = null)
    {
        if( "" == $name || isset($this->criteria[$name]) )
        {
            throw new InvalidArgumentException();
        }
        
        $this->criteria[$name] = $criterion ?? fn() => true;
    }
    
    public function parse(TokenQueue $queue): stdClass
    {
        $arguments = [];
        
        foreach( $this->criteria as $name => $criterion )
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
