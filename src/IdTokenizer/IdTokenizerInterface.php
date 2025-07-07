<?php

namespace Mohachi\CliParser\IdTokenizer;

use Mohachi\CliParser\Token\Id\IdTokenInterface;

interface IdTokenizerInterface
{
    
    /**
     * @return IdTokenInterface[]
     */
    public function tokenize(string $input): array;
    
}
