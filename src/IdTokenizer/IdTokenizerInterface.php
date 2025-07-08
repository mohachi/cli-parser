<?php

namespace Mohachi\CliParser\IdTokenizer;

use Mohachi\CliParser\Token\AbstractToken;
use Mohachi\CliParser\Token\IdToken;

interface IdTokenizerInterface
{
    
    public function create(string $value): IdToken;
    
    /**
     * @return list<AbstractToken>
     */
    public function tokenize(string $input): ?array;
    
}
