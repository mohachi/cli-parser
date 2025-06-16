<?php

use Mohachi\CommandLine\Exception\InvalidArgumentException;
use Mohachi\CommandLine\Exception\TokenizerException;
use Mohachi\CommandLine\IdentifierTokenizer\IdentifierTokenizerInterface;
use Mohachi\CommandLine\Normalizer;
use Mohachi\CommandLine\Token\ArgumentToken;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Normalizer::class)]
class NormalizerTest extends TestCase
{
    
    public function get_unsatisfiable_tokenizer_extension(): IdentifierTokenizerInterface
    {
        $tokenizer = $this->createStub(IdentifierTokenizerInterface::class);
        $tokenizer->method("tokenize")->willThrowException(new TokenizerException);
        return $tokenizer;
    }
    
    /* METHOD: tokenize */
    
    #[Test]
    public function tokenize_empty_args()
    {
        $args = [];
        $normalizer = new Normalizer;
        $normalizer->appeand("tokenizer", $this->get_unsatisfiable_tokenizer_extension());
        
        $this->expectException(InvalidArgumentException::class);
        
        $normalizer->normalize($args);
    }
    
    #[Test]
    public function tokenize_non_list_args()
    {
        $args = [5 => "cmd"];
        $normalizer = new Normalizer;
        $normalizer->appeand("tokenizer", $this->get_unsatisfiable_tokenizer_extension());
        
        $this->expectException(InvalidArgumentException::class);
        
        $normalizer->normalize($args);
    }
    
    #[Test]
    public function tokenize_non_string_args()
    {
        $args = [[]];
        $normalizer = new Normalizer;
        $normalizer->appeand("tokenizer", $this->get_unsatisfiable_tokenizer_extension());
        
        $this->expectException(InvalidArgumentException::class);
        
        $normalizer->normalize($args);
    }
    
    #[Test]
    public function tokenize_against_empty_tokenizer_extensions()
    {
        $normalizer = new Normalizer;
        $args = ["literal", "--long", "-s"];
        
        $queue = $normalizer->normalize($args);
        
        foreach( $args as $arg )
        {
            $this->assertEquals(new ArgumentToken($arg), $queue->dequeue());
        }
        
        $this->assertTrue($queue->isEmpty());
    }
    
    #[Test]
    public function tokenize_unsatisfiable_args()
    {
        $normalizer = new Normalizer;
        $args = ["first", "second"];
        $args = ["literal", "--long", "-s"];
        $normalizer->appeand("tokenizer", $this->get_unsatisfiable_tokenizer_extension());
        
        $queue = $normalizer->normalize($args);
        
        foreach( $args as $arg )
        {
            $this->assertEquals(new ArgumentToken($arg), $queue->dequeue());
        }
        
        $this->assertTrue($queue->isEmpty());
    }
    
}
