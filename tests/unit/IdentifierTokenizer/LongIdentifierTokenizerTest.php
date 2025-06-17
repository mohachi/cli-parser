<?php

use Mohachi\CommandLine\Exception\TokenizerException;
use Mohachi\CommandLine\IdentifierTokenizer\LongIdentifierTokenizer;
use Mohachi\CommandLine\Token\ArgumentToken;
use Mohachi\CommandLine\Token\Identifier\LongIdentifierToken;
use Mohachi\CommandLine\TokenQueue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(LongIdentifierTokenizer::class)]
class LongIdentifierTokenizerTest extends TestCase
{
    
    /* METHOD: tokenize */
    
    #[Test]
    public function tokenize_empty_input()
    {
        $tokenizer = new LongIdentifierTokenizer;
        $tokenizer->append(new LongIdentifierToken("id"));
        
        $this->expectException(TokenizerException::class);
        
        $tokenizer->tokenize("");
    }
    
    #[Test]
    public function tokenize_literal_input()
    {
        $tokenizer = new LongIdentifierTokenizer;
        $tokenizer->append(new LongIdentifierToken("id"));
        
        $this->expectException(TokenizerException::class);
        
        $tokenizer->tokenize("id");
    }
    
    #[Test]
    public function tokenize_against_empty_tokens()
    {
        $this->expectException(TokenizerException::class);
        
        (new LongIdentifierTokenizer)->tokenize("--id");
    }
    
    #[Test]
    public function tokenize_unsatisfied_tokens()
    {
        $tokenizer = new LongIdentifierTokenizer;
        $tokenizer->append(new LongIdentifierToken("expected-1"));
        $tokenizer->append(new LongIdentifierToken("expected-2"));
        
        $this->expectException(TokenizerException::class);
        
        $tokenizer->tokenize("--unexpected");
    }
    
    #[Test]
    public function tokenize_input_begins_with_satisfied_token_followed_by_extra_chars()
    {
        $tokenizer = new LongIdentifierTokenizer;
        $tokenizer->append(new LongIdentifierToken("expected"));
        
        $this->expectException(TokenizerException::class);
        
        $tokenizer->tokenize("--expecteddd");
    }
    
    #[Test]
    public function tokenize_satisfactory_input_of_id()
    {
        $token = new LongIdentifierToken("expected");
        $tokenizer = new LongIdentifierTokenizer;
        $tokenizer->append($token);
        
        $tokens = $tokenizer->tokenize("--expected");
        
        $this->assertEquals([$token], $tokens);
    }
    
    #[Test]
    public function tokenize_satisfactory_input_of_id_and_empty_argument()
    {
        $token = new LongIdentifierToken("expected");
        $tokenizer = new LongIdentifierTokenizer;
        $tokenizer->append($token);
        
        $tokens = $tokenizer->tokenize("--expected=");
        
        $this->assertEquals([$token, new ArgumentToken("")], $tokens);
    }
    
    #[Test]
    public function tokenize_satisfactory_input_of_id_and_argument()
    {
        $token = new LongIdentifierToken("expected");
        $tokenizer = new LongIdentifierTokenizer;
        $tokenizer->append($token);
        
        $tokens = $tokenizer->tokenize("--expected=arg");
        
        $this->assertEquals([$token, new ArgumentToken("arg")], $tokens);
    }
    
    #[Test]
    public function tokenize_against_callabsable_tokens()
    {
        $token_1 = new LongIdentifierToken("option");
        $token_2 = new LongIdentifierToken("opt");
        $tokenizer = new LongIdentifierTokenizer;
        $tokenizer->append($token_2);
        $tokenizer->append($token_1);
        
        $tokens = $tokenizer->tokenize("--option");
        
        $this->assertEquals([$token_1], $tokens);
    }
    
}
