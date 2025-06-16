<?php

namespace Mohachi\CommandLine\IdentifierTokenizer;

use Mohachi\CommandLine\Exception\TokenizerException;
use Mohachi\CommandLine\Token\Identifier\LiteralIdentifierToken;

class LiteralIdentifierTokenizer implements IdentifierTokenizerInterface
{
    
    private array $tokens = [];
    
    public function append(LiteralIdentifierToken $token)
    {
        $this->tokens[(string) $token] = $token;
    }
    
    public function tokenize(string $input): array
    {
        if( ! isset($this->tokens[$input]) )
        {
            throw new TokenizerException();
        }
        
        return [$this->tokens[$input]];
    }
    
}
