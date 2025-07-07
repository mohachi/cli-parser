<?php

namespace Mohachi\CliParser\IdTokenizer;

use Mohachi\CliParser\Exception\TokenizerException;
use Mohachi\CliParser\Token\Id\LiteralIdToken;

class LiteralIdTokenizer implements IdTokenizerInterface
{
    
    private array $tokens = [];
    
    public function append(LiteralIdToken $token)
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
