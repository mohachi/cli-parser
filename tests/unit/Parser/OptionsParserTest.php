<?php

use Mohachi\CommandLine\Exception\InvalidArgumentException;
use Mohachi\CommandLine\Exception\ParserException;
use Mohachi\CommandLine\Exception\UnderflowException;
use Mohachi\CommandLine\Parser\OptionParser;
use Mohachi\CommandLine\Parser\OptionsParser;
use Mohachi\CommandLine\SyntaxTree\ArgumentNode;
use Mohachi\CommandLine\SyntaxTree\LiteralIdentifierNode;
use Mohachi\CommandLine\SyntaxTree\LongIdentifierNode;
use Mohachi\CommandLine\TokenQueue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(OptionsParser::class)]
class OptionsParserTest extends TestCase
{
    
    /* METHOD: append */
    
    #[Test]
    public function appent_negative_min()
    {
        $this->expectException(InvalidArgumentException::class);
        
        (new OptionsParser)
            ->append(new OptionParser("opt", new LongIdentifierNode("opt")), -1);
    }
    
    #[Test]
    public function appent_max_equal_to_zero()
    {
        $this->expectException(InvalidArgumentException::class);
        
        (new OptionsParser)
            ->append(new OptionParser("opt", new LongIdentifierNode("opt")), 0, 0);
    }
    
    #[Test]
    public function appent_positive_max_less_than_min()
    {
        $this->expectException(InvalidArgumentException::class);
        
        (new OptionsParser)
            ->append(new OptionParser("opt", new LongIdentifierNode("opt")), 5, 2);
    }
    
    /* METHOD: parse */
    
    #[Test]
    public function parse_empty_queue()
    {
        $parser = new OptionsParser();
        $parser->append(new OptionParser("opt", new LiteralIdentifierNode("cmd")));
        
        $node = $parser->parse(new TokenQueue);
        
        $this->assertEmpty($node);
    }
    
    #[Test]
    public function parse_against_empty_parsers()
    {
        $id = new LiteralIdentifierNode("cmd");
        $tokens = new TokenQueue;
        $tokens->push($id);
        
        $node = (new OptionsParser())->parse($tokens);
        
        $this->assertEmpty($node);
        $this->assertSame($id, $tokens->getHead());
    }
    
    #[Test]
    public function parse_required_option_agains_empty_queue()
    {
        $parser = new OptionsParser;
        $parser->append(new OptionParser("opt", new LongIdentifierNode("opt")), 1);
        
        $this->expectException(UnderflowException::class);
        
        $parser->parse(new TokenQueue);
    }
    
    #[Test]
    public function parse_insufficient_option_min()
    {
        $tokens = new TokenQueue;
        $tokens->push(new LiteralIdentifierNode("extra"));
        $parser = new OptionsParser;
        $parser->append(new OptionParser("opt", new LongIdentifierNode("opt")), 1);
        
        $this->expectException(ParserException::class);
        
        $parser->parse($tokens);
    }
    
    #[Test]
    public function parse_overwhelmed_option()
    {
        $id = new LongIdentifierNode("opt");
        $tokens = new TokenQueue;
        $tokens->push($id);
        $tokens->push($id);
        $parser = new OptionsParser;
        $parser->append(new OptionParser("opt", $id), 0, 1);
        
        $node = $parser->parse($tokens);
        
        $this->assertSame($id, $tokens->getHead());
        $this->assertEquals("opt", $node[0]->name);
    }
    
    #[Test]
    public function parse_unsatisfied_option_id()
    {
        $id = new LiteralIdentifierNode("unexpected");
        $tokens = new TokenQueue;
        $tokens->push($id);
        $parser = new OptionsParser;
        $parser->append(new OptionParser("opt", new LiteralIdentifierNode("expected")));
        
        $node = $parser->parse($tokens);
        
        $this->assertEmpty($node);
        $this->assertSame($id, $tokens->getHead());
    }
    
    #[Test]
    public function parse_unsatisfied_option_arguments()
    {
        $tokens = new TokenQueue;
        $tokens->push(new LongIdentifierNode("opt"));
        $tokens->push(new LiteralIdentifierNode("unexpected"));
        $opt = new OptionParser("opt", new LongIdentifierNode("expected"));
        $opt->arguments->append("arg", fn($v) => $v == "expected");
        $parser = new OptionsParser;
        $parser->append($opt, 1);
        
        $this->expectException(ParserException::class);
        
        $parser->parse($tokens);
    }
    
    #[Test]
    public function parse_satisfied_option()
    {
        $id1 = new LongIdentifierNode("num");
        $arg1 = new ArgumentNode("12");
        $id2 = new LongIdentifierNode("opt");
        $tokens = new TokenQueue;
        $tokens->push($id2);
        $tokens->push($id1);
        $tokens->push($arg1);
        $tokens->push($id2);
        $opt = new OptionParser("num", $id1);
        $opt->arguments->append("value", fn(string $v) => is_numeric($v));
        $parser = new OptionsParser;
        $parser->append($opt);
        $parser->append(new OptionParser("opt", $id2));
        
        $node = $parser->parse($tokens);
        
        $this->assertCount(3, $node);
        $this->assertEquals("opt", $node[0]->name);
        $this->assertEquals("num", $node[1]->name);
        $this->assertEquals("opt", $node[2]->name);
    }
    
}
