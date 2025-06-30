<?php

namespace Mohachi\CommandLine\Parser;

use Mohachi\CommandLine\Exception\ParserException;
use Mohachi\CommandLine\Token\Id\IdTokenInterface;
use Mohachi\CommandLine\TokenQueue;

class IdParser implements ParserInterface
{
    
    /**
     * @var IdTokenInterface[] $tokens
     */
    private array $tokens = [];
    
    public function append(IdTokenInterface $token)
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
