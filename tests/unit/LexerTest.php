<?php

use Mohachi\CommandLine\Exception\InvalidArgumentException;
use Mohachi\CommandLine\Exception\TokenizerException;
use Mohachi\CommandLine\IdTokenizer\IdTokenizerInterface;
use Mohachi\CommandLine\Lexer;
use Mohachi\CommandLine\Token\ArgumentToken;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Lexer::class)]
class LexerTest extends TestCase
{
    
    public function get_unsatisfiable_tokenizer_extension(): IdTokenizerInterface
    {
        $tokenizer = $this->createStub(IdTokenizerInterface::class);
        $tokenizer->method("tokenize")->willThrowException(new TokenizerException);
        return $tokenizer;
    }
    
    /* METHOD: tokenize */
    
    #[Test]
    public function tokenize_empty_args()
    {
        $args = [];
        $lexer = new Lexer;
        $lexer->append("tokenizer", $this->get_unsatisfiable_tokenizer_extension());
        
        $this->expectException(InvalidArgumentException::class);
        
        $lexer->lex($args);
    }
    
    #[Test]
    public function tokenize_non_list_args()
    {
        $args = [5 => "cmd"];
        $lexer = new Lexer;
        $lexer->append("tokenizer", $this->get_unsatisfiable_tokenizer_extension());
        
        $this->expectException(InvalidArgumentException::class);
        
        $lexer->lex($args);
    }
    
    #[Test]
    public function tokenize_non_string_args()
    {
        $args = [[]];
        $lexer = new Lexer;
        $lexer->append("tokenizer", $this->get_unsatisfiable_tokenizer_extension());
        
        $this->expectException(InvalidArgumentException::class);
        
        $lexer->lex($args);
    }
    
    #[Test]
    public function tokenize_against_empty_tokenizer_extensions()
    {
        $lexer = new Lexer;
        $args = ["literal", "--long", "-s"];
        
        $queue = $lexer->lex($args);
        
        foreach( $args as $arg )
        {
            $this->assertEquals(new ArgumentToken($arg), $queue->dequeue());
        }
        
        $this->assertTrue($queue->isEmpty());
    }
    
    #[Test]
    public function tokenize_unsatisfiable_args()
    {
        $lexer = new Lexer;
        $args = ["first", "second"];
        $args = ["literal", "--long", "-s"];
        $lexer->append("tokenizer", $this->get_unsatisfiable_tokenizer_extension());
        
        $queue = $lexer->lex($args);
        
        foreach( $args as $arg )
        {
            $this->assertEquals(new ArgumentToken($arg), $queue->dequeue());
        }
        
        $this->assertTrue($queue->isEmpty());
    }
    
}
