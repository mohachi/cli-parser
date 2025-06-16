<?php

namespace Mohachi\CommandLine\Parser;

use Mohachi\CommandLine\TokenQueue;

interface ParserInterface
{
    public function parse(TokenQueue $queue): mixed;
}
