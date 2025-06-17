<?php

use Mohachi\CommandLine\Exception\TokenizerException;
use Mohachi\CommandLine\IdentifierTokenizer\ShortIdentifierTokenizer;
use Mohachi\CommandLine\Token\ArgumentToken;
use Mohachi\CommandLine\Token\Identifier\ShortIdentifierToken;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(ShortIdentifierTokenizer::class)]
class ShortIdentifierTokenizerTest extends TestCase
{
    
    /* METHOD: tokenize */
    
    #[Test]
    public function tokenize_empty_input()
    {
        $tokenizer = new ShortIdentifierTokenizer;
        $tokenizer->append(new ShortIdentifierToken("i"));
        
        $this->expectException(TokenizerException::class);
        
        $tokenizer->tokenize("");
    }
    
    #[Test]
    public function tokenize_literal_input()
    {
        $tokenizer = new ShortIdentifierTokenizer;
        $tokenizer->append(new ShortIdentifierToken("l"));
        $tokenizer->append(new ShortIdentifierToken("i"));
        $tokenizer->append(new ShortIdentifierToken("t"));
        $tokenizer->append(new ShortIdentifierToken("e"));
        $tokenizer->append(new ShortIdentifierToken("r"));
        $tokenizer->append(new ShortIdentifierToken("a"));
        $tokenizer->append(new ShortIdentifierToken("l"));
        
        $this->expectException(TokenizerException::class);
        
        $tokenizer->tokenize("literal");
    }
    
    #[Test]
    public function tokenize_long_input()
    {
        $tokenizer = new ShortIdentifierTokenizer;
        $tokenizer->append(new ShortIdentifierToken("l"));
        $tokenizer->append(new ShortIdentifierToken("o"));
        $tokenizer->append(new ShortIdentifierToken("n"));
        $tokenizer->append(new ShortIdentifierToken("g"));
        
        $this->expectException(TokenizerException::class);
        
        $tokenizer->tokenize("--long");
    }
    
    #[Test]
    public function tokenize_against_empty_tokens()
    {
        $this->expectException(TokenizerException::class);
        
        (new ShortIdentifierTokenizer)->tokenize("-i");
    }
    
    #[Test]
    public function tokenize_unsatisfied_tokens()
    {
        $tokenizer = new ShortIdentifierTokenizer;
        $tokenizer->append(new ShortIdentifierToken("1"));
        $tokenizer->append(new ShortIdentifierToken("2"));
        
        $this->expectException(TokenizerException::class);
        
        $tokenizer->tokenize("-0");
    }
    
    #[Test]
    public function tokenize_input_that_satisfies_multiple_tokens()
    {
        $token_1 = new ShortIdentifierToken("1");
        $token_2 = new ShortIdentifierToken("2");
        $tokenizer = new ShortIdentifierTokenizer;
        $tokenizer->append($token_1);
        $tokenizer->append($token_2);
        
        $tokens = $tokenizer->tokenize("-2121");
        
        $this->assertEquals([$token_2, $token_1, $token_2, $token_1], $tokens);
    }
    
    #[Test]
    public function tokenize_input_that_contains_unsatisfactory_ids()
    {
        $tokenizer = new ShortIdentifierTokenizer;
        $tokenizer->append(new ShortIdentifierToken("1"));
        $tokenizer->append(new ShortIdentifierToken("2"));
        
        $this->expectException(TokenizerException::class);
        
        $tokenizer->tokenize("-123");
    }
    
    #[Test]
    public function tokenize_input_of_satisfactory_id_then_argument()
    {
        $tokenizer = new ShortIdentifierTokenizer;
        $token = new ShortIdentifierToken("1");
        $tokenizer->append($token);
        
        $tokens = $tokenizer->tokenize("-1arg");
        
        $this->assertEquals([$token, new ArgumentToken("arg")], $tokens);
    }
    
    #[Test]
    public function tokenize_input_of_satisfactory_id_then_equal_sign_then_argument()
    {
        $tokenizer = new ShortIdentifierTokenizer;
        $token = new ShortIdentifierToken("1");
        $tokenizer->append($token);
        
        $tokens = $tokenizer->tokenize("-1=arg");
        
        $this->assertEquals([$token, new ArgumentToken("arg")], $tokens);
    }
    
}
