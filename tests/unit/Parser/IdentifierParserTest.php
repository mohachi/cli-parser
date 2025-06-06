<?php

use Mohachi\CommandLine\Exception\ParserException;
use Mohachi\CommandLine\Exception\UnderflowException;
use Mohachi\CommandLine\Parser\IdentifierParser;
use Mohachi\CommandLine\SyntaxTree\Identifier\LiteralIdentifierNode;
use Mohachi\CommandLine\SyntaxTree\Identifier\LongIdentifierNode;
use Mohachi\CommandLine\TokenQueue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(IdentifierParser::class)]
class IdentifierParserTest extends TestCase
{
    
    /* METHOD: parse */
    
    #[Test]
    public function parse_empty_queue()
    {
        $parser = new IdentifierParser;
        $parser->append(new LongIdentifierNode("cmd"));
        
        $this->expectException(UnderflowException::class);
        
        $parser->parse(new TokenQueue);
    }
    
    #[Test]
    public function parse_against_empty_id_list()
    {
        $tokens = new TokenQueue;
        $tokens->push(new LiteralIdentifierNode("cmd"));
        
        $this->expectException(ParserException::class);
        
        (new IdentifierParser)->parse($tokens);
    }
    
    #[Test]
    public function parse_unsatisfied_id()
    {
        $tokens = new TokenQueue;
        $tokens->push(new LongIdentifierNode("unexpected"));
        $parser = new IdentifierParser;
        $parser->append(new LongIdentifierNode("expected"));
        
        $this->expectException(ParserException::class);
        
        $parser->parse($tokens);
    }
    
    #[Test]
    public function parse_satisfied_id()
    {
        $id = new LongIdentifierNode("expected");
        $tokens = new TokenQueue;
        $tokens->push($id);
        $parser = new IdentifierParser;
        $parser->append($id);
        
        $node = $parser->parse($tokens);
        
        $this->assertSame($id, $node);
    }
    
}
