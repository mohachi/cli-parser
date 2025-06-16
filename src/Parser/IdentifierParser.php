<?php

namespace Mohachi\CommandLine\Parser;

use Mohachi\CommandLine\Exception\ParserException;
use Mohachi\CommandLine\Token\Identifier\IdentifierTokenInterface;
use Mohachi\CommandLine\TokenQueue;

class IdentifierParser implements ParserInterface
{
    
    /**
     * @var IdentifierTokenInterface[] $tokens
     */
    private array $tokens = [];
    
    public function append(IdentifierTokenInterface $token)
    {
        $this->tokens[] = $token;
    }
    
    public function parse(TokenQueue $queue):  string
    {
        foreach( $this->tokens as $token )
        {
            if( $token === $queue->getHead() )
            {
                return (string) $queue->dequeue();
            }
        }
        
        throw new ParserException();
    }
    
}
