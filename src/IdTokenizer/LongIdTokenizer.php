<?php

namespace Mohachi\CommandLine\IdTokenizer;

use Mohachi\CommandLine\Exception\TokenizerException;
use Mohachi\CommandLine\Token\ArgumentToken;
use Mohachi\CommandLine\Token\Id\LongIdToken;

class LongIdTokenizer implements IdTokenizerInterface
{
    
    /**
     * @var LongIdToken[] $tokens
     */
    private array $tokens = [];
    
    public function append(LongIdToken $token)
    {
        $this->tokens[] = $token;
    }
    
    public function tokenize(string $input): array
    {
        if( "" == $input || ! str_starts_with($input, "--") )
        {
            throw new TokenizerException();
        }
        
        $token = null;
        $input = substr($input, 2);
        rsort($this->tokens, SORT_NATURAL);
        
        foreach( $this->tokens as $token )
        {
            if( str_starts_with($input, $token->value) )
            {
                $input = substr($input, strlen($token->value));
                break;
            }
        }
        
        if( null === $token )
        {
            throw new TokenizerException();
        }
        
        if( "" == $input )
        {
            return [$token];
        }
        elseif( "=" !== $input[0] )
        {
            throw new TokenizerException();
        }
        
        return [$token, new ArgumentToken(substr($input, 1))];
    }
}
