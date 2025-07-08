<?php

namespace Mohachi\CliParser\IdTokenizer;

use Mohachi\CliParser\Exception\InvalidArgumentException;
use Mohachi\CliParser\Token\ArgumentToken;
use Mohachi\CliParser\Token\IdToken;

class ShortIdTokenizer implements IdTokenizerInterface
{
    
    /**
     * @var list<string,IdToken> $token
     */
    private array $tokens = [];
    
    public function create(string $value): IdToken
    {
        if( in_array($value, ["", "-", "--", "-="]) )
        {
            throw new InvalidArgumentException();
        }
        
        if( str_starts_with($value, "-") )
        {
            $value = substr($value, 1);
        }
        
        if( strlen($value) != 1 )
        {
            throw new InvalidArgumentException();
        }
        
        if( isset($this->tokens[$value]) )
        {
            return $this->tokens[$value];
        }
        
        return $this->tokens[$value] = new IdToken("-$value");
    }
    
    public function tokenize(string $input): ?array
    {
        if( in_array($input, ["", "-", "-="]) || ! str_starts_with($input, "-") )
        {
            return null;
        }
        
        $pos = -1;
        $tokens = [];
        $input = substr($input, 1);
        
        do
        {
            $pos++;
            $tokenized = false;
            $id = substr($input, $pos, 1);
            
            if( isset($this->tokens[$id]) )
            {
                $tokenized = true;
                $tokens[] = $this->tokens[$id];
            }
        }
        while( $tokenized && "=" != $id );
        
        if( empty($tokens) )
        {
            return null;
        }
        
        if( "=" == $id )
        {
            $tokens[] = new ArgumentToken(substr($input, $pos + 1));
        }
        elseif( ! empty($arg = substr($input, $pos)) )
        {
            $tokens[] = new ArgumentToken($arg);
        }
        
        return $tokens;
    }
}
