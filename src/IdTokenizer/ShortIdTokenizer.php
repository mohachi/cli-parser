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
        if( empty($this->tokens) )
        {
            return null;
        }
        
        if( in_array($input, ["", "-", "--", "-="]) || ! str_starts_with($input, "-") )
        {
            return null;
        }
        
        $pos = 1;
        $tokens = [];
        $id = substr($input, $pos, 1);
        
        while( isset($this->tokens[$id]) )
        {
            $tokens[] = $this->tokens[$id];
            $id = substr($input, ++$pos, 1);
        }
        
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
