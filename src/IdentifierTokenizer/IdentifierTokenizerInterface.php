<?php

namespace Mohachi\CommandLine\IdentifierTokenizer;

use Mohachi\CommandLine\Token\Identifier\IdentifierTokenInterface;

interface IdentifierTokenizerInterface
{
    
    /**
     * @return IdentifierTokenInterface[]
     */
    public function tokenize(string $input): array;
    
}
