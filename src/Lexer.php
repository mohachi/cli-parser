<?php

namespace Mohachi\CliParser;

use Mohachi\CliParser\Exception\InvalidArgumentException;
use Mohachi\CliParser\IdTokenizer\IdTokenizerInterface;
use Mohachi\CliParser\Token\ArgumentToken;
use Mohachi\CliParser\TokenQueue;

class Lexer
{
    
    /**
     * @var array<string,IdTokenizerInterface> $tokenizers
     */
    private array $tokenizers = [];
    
    public function register(IdTokenizerInterface $tokenizer)
    {
        $type = get_class($tokenizer);
        
        if( isset($this->tokenizers[$type]) )
        {
            throw new InvalidArgumentException();
        }
        
        $this->tokenizers[$type] = $tokenizer;
    }
    
    function get(string $type): IdTokenizerInterface
    {
        if( ! isset($this->tokenizers[$type]) )
        {
            throw new InvalidArgumentException();
        }
        
        return $this->tokenizers[$type];
    }
    
    public function lex(array &$args): TokenQueue
    {
        switch( true )
        {
            case empty($args):
            case ! array_is_list($args):
            case ! array_all($args, fn($v) => is_string($v)):
                throw new InvalidArgumentException();
        }
        
        reset($args);
        $arg = current($args);
        $queue = new TokenQueue;
        
        while( false !== $arg )
        {
            $tokens = [];
            
            foreach( $this->tokenizers as $tokenizer )
            {
                $tokens = $tokenizer->tokenize($arg);
                
                if( empty($tokens) )
                {
                    continue;
                }
                
                foreach( $tokens as $token )
                {
                    $queue->enqueue($token);
                }
                
                break;
            }
            
            if( empty($tokens) )
            {
                $queue->enqueue(new ArgumentToken($arg));
            }
            
            $arg = next($args);
        }
        
        return $queue;
    }
    
}
