<?php

namespace Mohachi\CommandLine\Parser;

use Mohachi\CommandLine\SyntaxTree\NodeInterface;
use Mohachi\CommandLine\TokenQueue;

interface ParserInterface
{
    public function parse(TokenQueue $tokens): NodeInterface;
}
