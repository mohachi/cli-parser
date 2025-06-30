<?php

namespace Mohachi\CommandLine\IdTokenizer;

use Mohachi\CommandLine\Token\Id\IdTokenInterface;

interface IdTokenizerInterface
{
    
    /**
     * @return IdTokenInterface[]
     */
    public function tokenize(string $input): array;
    
}
