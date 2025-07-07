<?php

namespace Mohachi\CliParser\IdTokenizer;

use Mohachi\CliParser\Exception\TokenizerException;
use Mohachi\CliParser\Token\ArgumentToken;
use Mohachi\CliParser\Token\Id\ShortIdToken;

class ShortIdTokenizer implements IdTokenizerInterface
{
    
    /**
     * @var ShortIdToken[] $tokens
     */
    private array $tokens = [];
    
    public function append(ShortIdToken $token)
    {
        $this->tokens[] = $token;
    }
    
    public function tokenize(string $input): array
    {
        if( "" == $input || ! str_starts_with($input, "-") )
        {
            throw new TokenizerException();
        }
        
        $tokens = [];
        $input = substr($input, 1);
        
        do
        {
            $tokenized = false;
            
            foreach( $this->tokens as $token )
            {
                if( str_starts_with($input, $token->value) )
                {
                    $tokenized = true;
                    $tokens[] = $token;
                    $input = substr($input, 1);
                    break;
                }
            }
        }
        while( $tokenized );
        
        if( empty($tokens) )
        {
            throw new TokenizerException();
        }
        
        if( "" == $input )
        {
            return $tokens;
        }
        
        if( count($tokens) != 1 )
        {
            throw new TokenizerException();
        }
        
        if( "=" == $input[0] )
        {
            $input = substr($input, 1);
        }
        
        return [$tokens[0], new ArgumentToken($input)];
    }
}
