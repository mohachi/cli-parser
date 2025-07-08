<?php

use Mohachi\CliParser\IdTokenizer\LiteralIdTokenizer;
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
        $tokenizer->create("id");
        
        $tokens = $tokenizer->tokenize("");
        
        $this->assertEmpty($tokens);
    }
    
    #[Test]
    public function tokenize_against_empty_tokens()
    {
        $tokens = (new LiteralIdTokenizer)->tokenize("unexpected");
        
        $this->assertEmpty($tokens);
    }
    
    #[Test]
    public function tokenize_unsatisfactory_input()
    {
        $tokenizer = new LiteralIdTokenizer;
        $tokenizer->create("expected");
        
        $tokens = $tokenizer->tokenize("unexpected");
        
        $this->assertEmpty($tokens);
    }
    
    #[Test]
    public function tokenize_satisfactory_input()
    {
        $tokenizer = new LiteralIdTokenizer;
        $token = $tokenizer->create("expected");
        
        $tokens = $tokenizer->tokenize("expected");
        
        $this->assertSame([$token], $tokens);
    }
    
}
