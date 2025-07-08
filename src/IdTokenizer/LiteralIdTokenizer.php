<?php

namespace Mohachi\CliParser\IdTokenizer;

use Mohachi\CliParser\Exception\InvalidArgumentException;
use Mohachi\CliParser\Exception\TokenizerException;
use Mohachi\CliParser\Token\IdToken;

class LiteralIdTokenizer implements IdTokenizerInterface
{
    
    /**
     * @var list<string,IdToken> $token
     */
    private array $tokens = [];
    
    public function create(string $value): IdToken
    {
        if( "" == $value || "-" == $value[0] )
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
        if( ! isset($this->tokens[$input]) )
        {
            return null;
        }
        
        return [$this->tokens[$input]];
    }
    
}
