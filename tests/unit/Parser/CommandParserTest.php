<?php

use Mohachi\CommandLine\Parser\CommandParser;
use Mohachi\CommandLine\Exception\ParserException;
use Mohachi\CommandLine\Parser\OptionParser;
use Mohachi\CommandLine\SyntaxTree\ArgumentNode;
use Mohachi\CommandLine\SyntaxTree\LiteralIdentifierNode;
use Mohachi\CommandLine\SyntaxTree\LongIdentifierNode;
use Mohachi\CommandLine\TokenQueue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(CommandParser::class)]
class CommandParserTest extends TestCase
{
    
    /* METHOD: construct */
    
    #[Test]
    public function construct_empty_name()
    {
        $this->expectException(InvalidArgumentException::class);
        
        new CommandParser("", new LiteralIdentifierNode("cmd"));
    }
    
    /* METHOD: parse */
    
    #[Test]
    public function parse_empty_queue()
    {
        $this->expectException(UnderflowException::class);
        
        (new CommandParser("cmd", new LiteralIdentifierNode("cmd")))
            ->parse(new TokenQueue);
    }
    
    #[Test]
    public function parse_unsatisfied_id()
    {
        $tokens = new TokenQueue;
        $tokens->push(new LiteralIdentifierNode("unexpected"));
        
        $this->expectException(ParserException::class);
        
        (new CommandParser("cmd", new LiteralIdentifierNode("cmd")))
            ->parse($tokens);
    }
    
    #[Test]
    public function parse_unsatisfied_argument()
    {
        $id = new LongIdentifierNode("num");
        $tokens = new TokenQueue;
        $tokens->push($id);
        $tokens->push(new LiteralIdentifierNode("unexpected"));
        $parser = new CommandParser("number", $id);
        $parser->arguments->append("arg", fn($v) => is_numeric($v));
        
        $this->expectException(ParserException::class);
        
        $parser->parse($tokens);
    }
    
    #[Test]
    public function parse_satisfied_command()
    {
        $id1 = new LiteralIdentifierNode("cmd");
        $id2 = new LongIdentifierNode("opt");
        $arg = new ArgumentNode("value");
        $tokens = new TokenQueue;
        $tokens->push($id1);
        $tokens->push($id2);
        $tokens->push($arg);
        $parser = new CommandParser("cmd", $id1);
        $parser->arguments->append("arg");
        $parser->options->append(new OptionParser("opt", $id2));
        
        $node = $parser->parse($tokens);
        
        $this->assertSame($id1, $node->id);
        $this->assertCount(1, $node->options);
        $this->assertContains($arg, $node->arguments);
        $this->assertEquals("opt", $node->options[0]->name);
    }
    
}
