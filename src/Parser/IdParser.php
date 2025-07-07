<?php

namespace Mohachi\CliParser\Parser;

use Mohachi\CliParser\Exception\ParserException;
use Mohachi\CliParser\Token\Id\IdTokenInterface;
use Mohachi\CliParser\TokenQueue;

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
