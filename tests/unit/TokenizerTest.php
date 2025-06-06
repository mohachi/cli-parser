<?php

use Mohachi\CommandLine\Exception\DomainException;
use Mohachi\CommandLine\Exception\InvalidArgumentException;
use Mohachi\CommandLine\SyntaxTree\ArgumentNode;
use Mohachi\CommandLine\SyntaxTree\LiteralIdentifierNode;
use Mohachi\CommandLine\SyntaxTree\LongIdentifierNode;
use Mohachi\CommandLine\SyntaxTree\IdentifierNodeInterface;
use Mohachi\CommandLine\SyntaxTree\LeafNodeTrait;
use Mohachi\CommandLine\Tokenizer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Tokenizer::class)]
class TokenizerTest extends TestCase
{
    
    /* METHOD: appendIdentifier */
    
    #[Test]
    public function append_out_of_domain_identifier()
    {
        $tokenizer = new Tokenizer;
        
        $this->expectException(DomainException::class);
        
        $tokenizer->appendIdentifier(new class ("id") implements IdentifierNodeInterface
        {
            use LeafNodeTrait;
            
            public function __construct(string $value)
            {
                $this->value = $value;
            }
        });
    }
    
    
    /* METHOD: tokenize */
    
    #[Test]
    public function tokenize_empty_subjects()
    {
        $this->expectException(InvalidArgumentException::class);
        
        $cli = [];
        (new Tokenizer)->tokenize($cli);
    }
    
    #[Test]
    public function tokenize_non_list_subjects()
    {
        $this->expectException(InvalidArgumentException::class);
        
        $cli = [5 => "cmd"];
        (new Tokenizer)->tokenize($cli);
    }
    
    #[Test]
    public function tokenize_non_string_subjects()
    {
        $this->expectException(InvalidArgumentException::class);
        
        $cli = [[]];
        (new Tokenizer)->tokenize($cli);
    }
    
    #[Test]
    public function tokenize_unsatisfied_literal_id()
    {
        $cli = ["unexpected"];
        $tokenizer = new Tokenizer;
        $tokenizer->appendIdentifier(new LiteralIdentifierNode("expected"));
        
        $tokens = $tokenizer->tokenize($cli);
        
        $this->assertEquals($cli[0], $tokens->getHead());
        $this->assertInstanceOf(ArgumentNode::class, $tokens->getHead());
    }
    
    #[Test]
    public function tokenize_unsatisfied_long_id()
    {
        $cli = ["--unexpected"];
        $tokenizer = new Tokenizer;
        $tokenizer->appendIdentifier(new LongIdentifierNode("expected"));
        
        $tokens = $tokenizer->tokenize($cli);
        
        $this->assertEquals($cli[0], $tokens->getHead());
        $this->assertInstanceOf(ArgumentNode::class, $tokens->getHead());
    }
    
    #[Test]
    public function tokenize_satisfied_literal_id()
    {
        $cli = ["expected"];
        $tokenizer = new Tokenizer;
        $id = new LiteralIdentifierNode("expected");
        $tokenizer->appendIdentifier($id);
        
        $tokens = $tokenizer->tokenize($cli);
        
        $this->assertEquals($cli[0], $tokens->getHead());
        $this->assertInstanceOf(LiteralIdentifierNode::class, $tokens->getHead());
    }
    
    #[Test]
    public function tokenize_satisfied_long_id()
    {
        $cli = ["--expected"];
        $tokenizer = new Tokenizer;
        $id = new LongIdentifierNode("expected");
        $tokenizer->appendIdentifier($id);
        
        $tokens = $tokenizer->tokenize($cli);
        
        $this->assertEquals($cli[0], $tokens->getHead());
        $this->assertInstanceOf(LongIdentifierNode::class, $tokens->getHead());
    }
    
}
