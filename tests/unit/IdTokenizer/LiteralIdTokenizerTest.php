<?php

use Mohachi\CommandLine\Exception\TokenizerException;
use Mohachi\CommandLine\IdTokenizer\LiteralIdTokenizer;
use Mohachi\CommandLine\Token\Id\LiteralIdToken;
use Mohachi\CommandLine\TokenQueue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(LiteralIdTokenizer::class)]
class LiteralIdTokenizerTest extends TestCase
{
    
    /* METHOD: tokenize */
    
    #[Test]
    public function tokenize_empty_input()
    {
        $tokenizer = new LiteralIdTokenizer;
        $tokenizer->append(new LiteralIdToken("id"));
        
        $this->expectException(TokenizerException::class);
        
        $tokenizer->tokenize("");
    }
    
    #[Test]
    public function tokenize_against_empty_tokens()
    {
        $this->expectException(TokenizerException::class);
        
        (new LiteralIdTokenizer)->tokenize("unexpected");
    }
    
    #[Test]
    public function tokenize_unsatisfactory_input()
    {
        $tokenizer = new LiteralIdTokenizer;
        $tokenizer->append(new LiteralIdToken("expected"));
        
        $this->expectException(TokenizerException::class);
        
        $tokenizer->tokenize("unexpected");
    }
    
    #[Test]
    public function tokenize_satisfactory_input()
    {
        $token = new LiteralIdToken("expected");
        $tokenizer = new LiteralIdTokenizer;
        $tokenizer->append($token);
        
        $tokens = $tokenizer->tokenize("expected");
        
        $this->assertSame([$token], $tokens);
    }
    
}
