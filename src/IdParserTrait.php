<?php

namespace Mohachi\CliParser;

use Mohachi\CliParser\Exception\ParserException;
use Mohachi\CliParser\Token\IdToken;
use Mohachi\CliParser\TokenQueue;

trait IdParserTrait
{
    
    /**
     * @var IdToken[] $tokens
     */
    private array $tokens = [];
    
    public function id(IdToken $token)
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
