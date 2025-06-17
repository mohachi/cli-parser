<?php

namespace Mohachi\CommandLine\IdentifierTokenizer;

use Mohachi\CommandLine\Exception\TokenizerException;
use Mohachi\CommandLine\Token\ArgumentToken;
use Mohachi\CommandLine\Token\Identifier\ShortIdentifierToken;

class ShortIdentifierTokenizer implements IdentifierTokenizerInterface
{
    
    /**
     * @var ShortIdentifierToken[] $tokens
     */
    private array $tokens = [];
    
    public function append(ShortIdentifierToken $token)
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
