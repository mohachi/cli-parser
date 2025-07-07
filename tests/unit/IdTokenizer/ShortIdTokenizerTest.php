<?php

use Mohachi\CliParser\Exception\TokenizerException;
use Mohachi\CliParser\IdTokenizer\ShortIdTokenizer;
use Mohachi\CliParser\Token\ArgumentToken;
use Mohachi\CliParser\Token\Id\ShortIdToken;
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
        $tokenizer->append(new ShortIdToken("i"));
        
        $this->expectException(TokenizerException::class);
        
        $tokenizer->tokenize("");
    }
    
    #[Test]
    public function tokenize_literal_input()
    {
        $tokenizer = new ShortIdTokenizer;
        $tokenizer->append(new ShortIdToken("l"));
        $tokenizer->append(new ShortIdToken("i"));
        $tokenizer->append(new ShortIdToken("t"));
        $tokenizer->append(new ShortIdToken("e"));
        $tokenizer->append(new ShortIdToken("r"));
        $tokenizer->append(new ShortIdToken("a"));
        $tokenizer->append(new ShortIdToken("l"));
        
        $this->expectException(TokenizerException::class);
        
        $tokenizer->tokenize("literal");
    }
    
    #[Test]
    public function tokenize_long_input()
    {
        $tokenizer = new ShortIdTokenizer;
        $tokenizer->append(new ShortIdToken("l"));
        $tokenizer->append(new ShortIdToken("o"));
        $tokenizer->append(new ShortIdToken("n"));
        $tokenizer->append(new ShortIdToken("g"));
        
        $this->expectException(TokenizerException::class);
        
        $tokenizer->tokenize("--long");
    }
    
    #[Test]
    public function tokenize_against_empty_tokens()
    {
        $this->expectException(TokenizerException::class);
        
        (new ShortIdTokenizer)->tokenize("-i");
    }
    
    #[Test]
    public function tokenize_unsatisfied_tokens()
    {
        $tokenizer = new ShortIdTokenizer;
        $tokenizer->append(new ShortIdToken("1"));
        $tokenizer->append(new ShortIdToken("2"));
        
        $this->expectException(TokenizerException::class);
        
        $tokenizer->tokenize("-0");
    }
    
    #[Test]
    public function tokenize_input_that_satisfies_multiple_tokens()
    {
        $token_1 = new ShortIdToken("1");
        $token_2 = new ShortIdToken("2");
        $tokenizer = new ShortIdTokenizer;
        $tokenizer->append($token_1);
        $tokenizer->append($token_2);
        
        $tokens = $tokenizer->tokenize("-2121");
        
        $this->assertEquals([$token_2, $token_1, $token_2, $token_1], $tokens);
    }
    
    #[Test]
    public function tokenize_input_that_contains_unsatisfactory_ids()
    {
        $tokenizer = new ShortIdTokenizer;
        $tokenizer->append(new ShortIdToken("1"));
        $tokenizer->append(new ShortIdToken("2"));
        
        $this->expectException(TokenizerException::class);
        
        $tokenizer->tokenize("-123");
    }
    
    #[Test]
    public function tokenize_input_of_satisfactory_id_then_argument()
    {
        $tokenizer = new ShortIdTokenizer;
        $token = new ShortIdToken("1");
        $tokenizer->append($token);
        
        $tokens = $tokenizer->tokenize("-1arg");
        
        $this->assertEquals([$token, new ArgumentToken("arg")], $tokens);
    }
    
    #[Test]
    public function tokenize_input_of_satisfactory_id_then_equal_sign_then_argument()
    {
        $tokenizer = new ShortIdTokenizer;
        $token = new ShortIdToken("1");
        $tokenizer->append($token);
        
        $tokens = $tokenizer->tokenize("-1=arg");
        
        $this->assertEquals([$token, new ArgumentToken("arg")], $tokens);
    }
    
}
