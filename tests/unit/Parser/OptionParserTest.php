<?php

use Mohachi\CommandLine\Exception\InvalidArgumentException;
use Mohachi\CommandLine\Exception\ParserException;
use Mohachi\CommandLine\Parser\OptionParser;
use Mohachi\CommandLine\SyntaxTree\ArgumentNode;
use Mohachi\CommandLine\SyntaxTree\LiteralIdentifierNode;
use Mohachi\CommandLine\SyntaxTree\LongIdentifierNode;
use Mohachi\CommandLine\TokenQueue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(OptionParser::class)]
class OptionParserTest extends TestCase
{
    
    /* METHOD: construct */
    
    #[Test]
    public function construct_empty_name()
    {
        $this->expectException(InvalidArgumentException::class);
        
        new OptionParser("", new LongIdentifierNode("opt"));
    }
    
    /* METHOD: parse */
    
    #[Test]
    public function parse_empty_stack()
    {
        $this->expectException(UnderflowException::class);
        
        (new OptionParser("opt", new LongIdentifierNode("opt")))
            ->parse(new TokenQueue);
    }
    
    #[Test]
    public function parse_unsatisfied_id()
    {
        $tokens = new TokenQueue;
        $tokens->push(new LiteralIdentifierNode("unexpected"));
        
        $this->expectException(ParserException::class);
        
        (new OptionParser("opt", new LiteralIdentifierNode("expected")))
            ->parse($tokens);
    }
    
    #[Test]
    public function parse_unsatisfied_argument()
    {
        $id = new LongIdentifierNode("num");
        $arg = new ArgumentNode("unexpected");
        $tokens = new TokenQueue;
        $tokens->push($id);
        $tokens->push($arg);
        $parser = new OptionParser("number", $id);
        $parser->arguments->append("arg", fn($v) => is_numeric($v));
        
        $this->expectException(ParserException::class);
        
        $parser->parse($tokens);
    }
    
    #[Test]
    public function parse_satisfied_option()
    {
        $id = new LongIdentifierNode("num");
        $arg = new ArgumentNode("12");
        $tokens = new TokenQueue;
        $tokens->push($id);
        $tokens->push($arg);
        $parser = new OptionParser("number", $id);
        $parser->arguments->append("arg", fn(string $v) => is_numeric($v));
        
        $node = $parser->parse($tokens);
        
        $this->assertSame($id, $node->id);
        $this->assertContains($arg, $node->arguments);
        $this->assertArrayHasKey("arg", $node->arguments);
    }
    
}
