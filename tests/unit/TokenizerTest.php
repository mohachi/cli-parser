<?php

use Mohachi\CommandLine\Exception\DomainException;
use Mohachi\CommandLine\Exception\InvalidArgumentException;
use Mohachi\CommandLine\SyntaxTree\ArgumentNode;
use Mohachi\CommandLine\SyntaxTree\Identifier\AbstractIdentifierNode;
use Mohachi\CommandLine\SyntaxTree\Identifier\LiteralIdentifierNode;
use Mohachi\CommandLine\SyntaxTree\Identifier\LongIdentifierNode;
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
        
        $tokenizer->appendIdentifier(new class ("id") extends AbstractIdentifierNode
        {
            public function setValue(string $value)
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
        $tokenizer = new Tokenizer;
        $subjects = ["unexpected"];
        $tokenizer->appendIdentifier(new LiteralIdentifierNode("expected"));
        
        $tokens = $tokenizer->tokenize($subjects);
        
        $this->assertInstanceOf(ArgumentNode::class, $tokens->getHead());
        $this->assertEquals($subjects[0], $tokens->getHead()->getValue());
    }
    
    #[Test]
    public function tokenize_unsatisfied_long_id()
    {
        $tokenizer = new Tokenizer;
        $subjects = ["--unexpected"];
        $tokenizer->appendIdentifier(new LongIdentifierNode("expected"));
        
        $tokens = $tokenizer->tokenize($subjects);
        
        $this->assertInstanceOf(ArgumentNode::class, $tokens->getHead());
        $this->assertEquals($subjects[0], $tokens->getHead()->getValue());
    }
    
    #[Test]
    public function tokenize_satisfied_literal_id()
    {
        $subjects = ["expected"];
        $tokenizer = new Tokenizer;
        $id = new LiteralIdentifierNode("expected");
        $tokenizer->appendIdentifier($id);
        
        $tokens = $tokenizer->tokenize($subjects);
        
        $this->assertEquals($subjects[0], $tokens->getHead()->getValue());
        $this->assertInstanceOf(LiteralIdentifierNode::class, $tokens->getHead());
    }
    
    #[Test]
    public function tokenize_satisfied_long_id()
    {
        $subjects = ["--expected"];
        $tokenizer = new Tokenizer;
        $id = new LongIdentifierNode("expected");
        $tokenizer->appendIdentifier($id);
        
        $tokens = $tokenizer->tokenize($subjects);
        
        $this->assertEquals($subjects[0], $tokens->getHead()->getValue());
        $this->assertInstanceOf(LongIdentifierNode::class, $tokens->getHead());
    }
    
}
