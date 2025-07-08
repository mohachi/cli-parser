<?php

use Mohachi\CliParser\IdTokenizer\LongIdTokenizer;
use Mohachi\CliParser\Token\ArgumentToken;
use Mohachi\CliParser\TokenQueue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(LongIdTokenizer::class)]
class LongIdTokenizerTest extends TestCase
{
    
    /* METHOD: tokenize */
    
    #[Test]
    public function tokenize_empty_input()
    {
        $tokenizer = new LongIdTokenizer;
        $tokenizer->create("id");
        
        $tokens = $tokenizer->tokenize("");
        
        $this->assertEmpty($tokens);
    }
    
    #[Test]
    public function tokenize_literal_input()
    {
        $tokenizer = new LongIdTokenizer;
        $tokenizer->create("id");
        
        $tokens = $tokenizer->tokenize("id");
        
        $this->assertEmpty($tokens);
    }
    
    #[Test]
    public function tokenize_against_empty_tokens()
    {
        $tokens = (new LongIdTokenizer)->tokenize("--id");
        
        $this->assertEmpty($tokens);
    }
    
    #[Test]
    public function tokenize_unsatisfied_tokens()
    {
        $tokenizer = new LongIdTokenizer;
        $tokenizer->create("expected-1");
        $tokenizer->create("expected-2");
        
        $tokens = $tokenizer->tokenize("--unexpected");
        
        $this->assertEmpty($tokens);
    }
    
    #[Test]
    public function tokenize_input_begins_with_satisfied_token_followed_by_extra_chars()
    {
        $tokenizer = new LongIdTokenizer;
        $tokenizer->create("expected");
        
        $tokens = $tokenizer->tokenize("--expecteddd");
        
        $this->assertEmpty($tokens);
    }
    
    #[Test]
    public function tokenize_satisfactory_input_of_id()
    {
        $tokenizer = new LongIdTokenizer;
        $token = $tokenizer->create("expected");
        
        $tokens = $tokenizer->tokenize("--expected");
        
        $this->assertEquals([$token], $tokens);
    }
    
    #[Test]
    public function tokenize_satisfactory_input_of_id_and_empty_argument()
    {
        $tokenizer = new LongIdTokenizer;
        $token = $tokenizer->create("expected");
        
        $tokens = $tokenizer->tokenize("--expected=");
        
        $this->assertEquals([$token, new ArgumentToken("")], $tokens);
    }
    
    #[Test]
    public function tokenize_satisfactory_input_of_id_and_argument()
    {
        $tokenizer = new LongIdTokenizer;
        $token = $tokenizer->create("expected");
        
        $tokens = $tokenizer->tokenize("--expected=arg");
        
        $this->assertEquals([$token, new ArgumentToken("arg")], $tokens);
    }
    
    #[Test]
    public function tokenize_against_callabsable_tokens()
    {
        $tokenizer = new LongIdTokenizer;
        $token_1 = $tokenizer->create("option");
        $token_2 = $tokenizer->create("opt");
        
        $tokens = $tokenizer->tokenize("--option");
        
        $this->assertEquals([$token_1], $tokens);
    }
    
}
