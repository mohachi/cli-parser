<?php

use Mohachi\CommandLine\Exception\TokenizerException;
use Mohachi\CommandLine\IdentifierTokenizer\LiteralIdentifierTokenizer;
use Mohachi\CommandLine\Token\Identifier\LiteralIdentifierToken;
use Mohachi\CommandLine\TokenQueue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(LiteralIdentifierTokenizer::class)]
class LiteralIdentifierTokenizerTest extends TestCase
{
    
    /* METHOD: tokenize */
    
    #[Test]
    public function tokenize_empty_input()
    {
        $tokenizer = new LiteralIdentifierTokenizer;
        $tokenizer->append(new LiteralIdentifierToken("id"));
        
        $this->expectException(TokenizerException::class);
        
        $tokenizer->tokenize("");
    }
    
    #[Test]
    public function tokenize_against_empty_tokens()
    {
        $this->expectException(TokenizerException::class);
        
        (new LiteralIdentifierTokenizer)->tokenize("unexpected");
    }
    
    #[Test]
    public function tokenize_unsatisfactory_input()
    {
        $tokenizer = new LiteralIdentifierTokenizer;
        $tokenizer->append(new LiteralIdentifierToken("expected"));
        
        $this->expectException(TokenizerException::class);
        
        $tokenizer->tokenize("unexpected");
    }
    
    #[Test]
    public function tokenize_satisfactory_input()
    {
        $token = new LiteralIdentifierToken("expected");
        $tokenizer = new LiteralIdentifierTokenizer;
        $tokenizer->append($token);
        
        $tokens = $tokenizer->tokenize("expected");
        
        $this->assertSame([$token], $tokens);
    }
    
}
