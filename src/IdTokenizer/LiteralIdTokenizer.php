<?php

namespace Mohachi\CliParser\IdTokenizer;

use Mohachi\CliParser\Exception\InvalidArgumentException;
use Mohachi\CliParser\Token\IdToken;

class LiteralIdTokenizer implements IdTokenizerInterface
{
    
    /**
     * @var list<string,IdToken> $token
     */
    private array $tokens = [];
    
    public function create(string $value): IdToken
    {
        if( "" == $value || ! ctype_alpha($value[0]) )
        {
            throw new InvalidArgumentException();
        }
        
        if( isset($this->tokens[$value]) )
        {
            return $this->tokens[$value];
        }
        
        return $this->tokens[$value] = new IdToken($value);
    }
    
    public function tokenize(string $input): ?array
    {
        if( isset($this->tokens[$input]) )
        {
            return [$this->tokens[$input]];
        }
        
        return null;
    }
    
}
