<?php

namespace Mohachi\CliParser\Parser;

use Mohachi\CliParser\TokenQueue;

interface ParserInterface
{
    public function parse(TokenQueue $queue): mixed;
}
