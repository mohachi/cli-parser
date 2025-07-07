<?php

namespace Mohachi\CommandLine;

use Mohachi\CommandLine\Exception\ParserException;
use Mohachi\CommandLine\Token\Id\IdTokenInterface;
use Mohachi\CommandLine\TokenQueue;

trait IdParserTrait
{
    
    /**
     * @var IdTokenInterface[] $tokens
     */
    private array $tokens = [];
    
    public function id(IdTokenInterface $token)
    {
        $this->tokens[] = $token;
    }
    
    public function parseId(TokenQueue $queue):  string
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
