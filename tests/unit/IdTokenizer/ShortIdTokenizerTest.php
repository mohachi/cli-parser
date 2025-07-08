<?php

use Mohachi\CliParser\IdTokenizer\ShortIdTokenizer;
use Mohachi\CliParser\Token\ArgumentToken;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(ShortIdTokenizer::class)]
class ShortIdTokenizerTest extends TestCase
{
    
    /* METHOD: tokenize */
    
    #[Test]
    public function tokenize_empty_input()
    {
        $tokenizer = new ShortIdTokenizer;
        $tokenizer->create("i");
        
        $tokens = $tokenizer->tokenize("");
        
        $this->assertEmpty($tokens);
    }
    
    #[Test]
    public function tokenize_literal_input()
    {
        $tokenizer = new ShortIdTokenizer;
        $tokenizer->create("l");
        $tokenizer->create("i");
        $tokenizer->create("t");
        $tokenizer->create("e");
        $tokenizer->create("r");
        $tokenizer->create("a");
        $tokenizer->create("l");
        
        $tokens = $tokenizer->tokenize("literal");
        
        $this->assertEmpty($tokens);
    }
    
    #[Test]
    public function tokenize_long_input()
    {
        $tokenizer = new ShortIdTokenizer;
        $tokenizer->create("l");
        $tokenizer->create("o");
        $tokenizer->create("n");
        $tokenizer->create("g");
        
        $tokens = $tokenizer->tokenize("--long");
        
        $this->assertNull($tokens);
    }
    
    #[Test]
    public function tokenize_against_empty_tokens()
    {
        $tokens = (new ShortIdTokenizer)->tokenize("-i");
        
        $this->assertEmpty($tokens);
    }
    
    #[Test]
    public function tokenize_unsatisfied_tokens()
    {
        $tokenizer = new ShortIdTokenizer;
        $tokenizer->create("1");
        $tokenizer->create("2");
        
        $tokens = $tokenizer->tokenize("-0");
        
        $this->assertEmpty($tokens);
    }
    
    #[Test]
    public function tokenize_input_that_satisfies_multiple_tokens()
    {
        $tokenizer = new ShortIdTokenizer;
        $token_1 = $tokenizer->create("1");
        $token_2 = $tokenizer->create("2");
        
        $tokens = $tokenizer->tokenize("-2121");
        
        $this->assertEquals([$token_2, $token_1, $token_2, $token_1], $tokens);
    }
    
    #[Test]
    public function tokenize_input_that_contains_unsatisfactory_ids()
    {
        $tokenizer = new ShortIdTokenizer;
        $tok1 = $tokenizer->create("1");
        $tok2 = $tokenizer->create("2");
        
        $tokens = $tokenizer->tokenize("-123");
        
        $this->assertEquals($tokens, [$tok1, $tok2, new ArgumentToken("3")]);
    }
    
    #[Test]
    public function tokenize_input_of_satisfactory_id_then_argument()
    {
        $tokenizer = new ShortIdTokenizer;
        $token = $tokenizer->create("1");
        
        $tokens = $tokenizer->tokenize("-1arg");
        
        $this->assertEquals([$token, new ArgumentToken("arg")], $tokens);
    }
    
    #[Test]
    public function tokenize_input_of_satisfactory_id_then_equal_sign_then_argument()
    {
        $tokenizer = new ShortIdTokenizer;
        $token = $tokenizer->create("1");
        
        $tokens = $tokenizer->tokenize("-1=arg");
        
        $this->assertEquals([$token, new ArgumentToken("arg")], $tokens);
    }
    
}
