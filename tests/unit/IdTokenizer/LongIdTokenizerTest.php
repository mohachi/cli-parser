<?php

use Mohachi\CliParser\Exception\TokenizerException;
use Mohachi\CliParser\IdTokenizer\LongIdTokenizer;
use Mohachi\CliParser\Token\ArgumentToken;
use Mohachi\CliParser\Token\Id\LongIdToken;
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
        $tokenizer->append(new LongIdToken("id"));
        
        $this->expectException(TokenizerException::class);
        
        $tokenizer->tokenize("");
    }
    
    #[Test]
    public function tokenize_literal_input()
    {
        $tokenizer = new LongIdTokenizer;
        $tokenizer->append(new LongIdToken("id"));
        
        $this->expectException(TokenizerException::class);
        
        $tokenizer->tokenize("id");
    }
    
    #[Test]
    public function tokenize_against_empty_tokens()
    {
        $this->expectException(TokenizerException::class);
        
        (new LongIdTokenizer)->tokenize("--id");
    }
    
    #[Test]
    public function tokenize_unsatisfied_tokens()
    {
        $tokenizer = new LongIdTokenizer;
        $tokenizer->append(new LongIdToken("expected-1"));
        $tokenizer->append(new LongIdToken("expected-2"));
        
        $this->expectException(TokenizerException::class);
        
        $tokenizer->tokenize("--unexpected");
    }
    
    #[Test]
    public function tokenize_input_begins_with_satisfied_token_followed_by_extra_chars()
    {
        $tokenizer = new LongIdTokenizer;
        $tokenizer->append(new LongIdToken("expected"));
        
        $this->expectException(TokenizerException::class);
        
        $tokenizer->tokenize("--expecteddd");
    }
    
    #[Test]
    public function tokenize_satisfactory_input_of_id()
    {
        $token = new LongIdToken("expected");
        $tokenizer = new LongIdTokenizer;
        $tokenizer->append($token);
        
        $tokens = $tokenizer->tokenize("--expected");
        
        $this->assertEquals([$token], $tokens);
    }
    
    #[Test]
    public function tokenize_satisfactory_input_of_id_and_empty_argument()
    {
        $token = new LongIdToken("expected");
        $tokenizer = new LongIdTokenizer;
        $tokenizer->append($token);
        
        $tokens = $tokenizer->tokenize("--expected=");
        
        $this->assertEquals([$token, new ArgumentToken("")], $tokens);
    }
    
    #[Test]
    public function tokenize_satisfactory_input_of_id_and_argument()
    {
        $token = new LongIdToken("expected");
        $tokenizer = new LongIdTokenizer;
        $tokenizer->append($token);
        
        $tokens = $tokenizer->tokenize("--expected=arg");
        
        $this->assertEquals([$token, new ArgumentToken("arg")], $tokens);
    }
    
    #[Test]
    public function tokenize_against_callabsable_tokens()
    {
        $token_1 = new LongIdToken("option");
        $token_2 = new LongIdToken("opt");
        $tokenizer = new LongIdTokenizer;
        $tokenizer->append($token_2);
        $tokenizer->append($token_1);
        
        $tokens = $tokenizer->tokenize("--option");
        
        $this->assertEquals([$token_1], $tokens);
    }
    
}
