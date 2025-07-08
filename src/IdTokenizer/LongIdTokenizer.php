<?php

namespace Mohachi\CliParser\IdTokenizer;

use Mohachi\CliParser\Exception\InvalidArgumentException;
use Mohachi\CliParser\Exception\TokenizerException;
use Mohachi\CliParser\Token\ArgumentToken;
use Mohachi\CliParser\Token\IdToken;

class LongIdTokenizer implements IdTokenizerInterface
{
    
    /**
     * @var list<string,IdToken> $token
     */
    private array $tokens = [];
    
    public function create(string $value): IdToken
    {
        if( "" == $value || "-" == $value || "--" == $value )
        {
            throw new InvalidArgumentException();
        }
        
        if( ! str_starts_with($value, "--") )
        {
            $value = "--$value";
        }
        
        if( isset($this->tokens[$value]) )
        {
            return $this->tokens[$value];
        }
        
        return $this->tokens[$value] = new IdToken($value);
    }
    
    public function tokenize(string $input): ?array
    {
        if( "" == $input || ! str_starts_with($input, "--") )
        {
            return null;
        }
        
        $delimiter = strpos($input, "=");
        $id = substr($input, 0, false !== $delimiter ? $delimiter : null);
        
        if( ! isset($this->tokens[$id]) )
        {
            return null;
        }
        
        if( false === $delimiter )
        {
            return [$this->tokens[$id]];
        }
        
        return [
            $this->tokens[$id],
            new ArgumentToken(substr($input, $delimiter + 1)),
        ];
    }
}
