<?php

namespace Mohachi\CliParser;

use Mohachi\CliParser\Exception\InvalidArgumentException;
use Mohachi\CliParser\Exception\ParserException;
use Mohachi\CliParser\Token\ArgumentToken;
use Mohachi\CliParser\TokenQueue;
use stdClass;

trait ArgumentsParserTrait
{
    
    private array $criteria = [];
    
    public function arg(string $name, ?callable $criterion = null)
    {
        if( "" == $name || isset($this->criteria[$name]) )
        {
            throw new InvalidArgumentException();
        }
        
        $this->criteria[$name] = $criterion ?? fn() => true;
    }
    
    public function parseArguments(TokenQueue $queue): stdClass
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
