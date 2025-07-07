<?php

use Mohachi\CommandLine\OptionsParserTrait;
use Mohachi\CommandLine\Exception\InvalidArgumentException;
use Mohachi\CommandLine\Exception\ParserException;
use Mohachi\CommandLine\Exception\UnderflowException;
use Mohachi\CommandLine\Option;
use Mohachi\CommandLine\Token\ArgumentToken;
use Mohachi\CommandLine\Token\Id\LiteralIdToken;
use Mohachi\CommandLine\Token\Id\LongIdToken;
use Mohachi\CommandLine\TokenQueue;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversTrait(OptionsParserTrait::class)]
class OptionsParserTraitTest extends TestCase
{
    
    /* METHOD: append */
    
    #[Test]
    public function appent_negative_min()
    {
        $optParser = new Option("opt");
        $optParser->id(new LongIdToken("opt"));
        
        $this->expectException(InvalidArgumentException::class);
        
        (new OptionsParserStub)->opt($optParser, -1);
    }
    
    #[Test]
    public function appent_max_equal_to_zero()
    {
        $optParser = new Option("opt");
        $optParser->id(new LongIdToken("opt"));
        
        $this->expectException(InvalidArgumentException::class);
        
        (new OptionsParserStub)->opt($optParser, 0, 0);
    }
    
    #[Test]
    public function appent_positive_max_less_than_min()
    {
        $optParser = new Option("opt");
        $optParser->id(new LongIdToken("opt"));
        
        $this->expectException(InvalidArgumentException::class);
        
        (new OptionsParserStub)->opt($optParser, 5, 2);
    }
    
    /* METHOD: parse */
    
    #[Test]
    public function parse_empty_queue()
    {
        $parser = new OptionsParserStub;
        $optParser = new Option("opt");
        $optParser->id(new LiteralIdToken("cmd"));
        $parser->opt($optParser);
        
        $options = $parser->parseOptions(new TokenQueue);
        
        $this->assertEmpty($options);
    }
    
    #[Test]
    public function parse_against_empty_parsers()
    {
        $id = new LiteralIdToken("cmd");
        $queue = new TokenQueue;
        $queue->enqueue($id);
        
        $options = (new OptionsParserStub())->parseOptions($queue);
        
        $this->assertEmpty($options);
        $this->assertSame($id, $queue->getHead()); // ensure the token doesn't get dequeued
    }
    
    #[Test]
    public function parse_required_option_agains_empty_queue()
    {
        $parser = new OptionsParserStub;
        $optParser = new Option("opt");
        $optParser->id(new LongIdToken("opt"));
        $parser->opt($optParser, 1);
        
        $this->expectException(UnderflowException::class);
        
        $parser->parseOptions(new TokenQueue);
    }
    
    #[Test]
    public function parse_insufficient_option_min()
    {
        $queue = new TokenQueue;
        $queue->enqueue(new LiteralIdToken("extra"));
        $parser = new OptionsParserStub;
        $optParser = new Option("opt");
        $optParser->id(new LongIdToken("opt"));
        $parser->opt($optParser, 1);
        
        $this->expectException(ParserException::class);
        
        $parser->parseOptions($queue);
    }
    
    #[Test]
    public function parse_overwhelmed_option()
    {
        $id = new LongIdToken("opt");
        $queue = new TokenQueue;
        $queue->enqueue($id);
        $queue->enqueue($id);
        $parser = new OptionsParserStub;
        $optParser = new Option("opt");
        $optParser->id($id);
        $parser->opt($optParser, 0, 1);
        
        $options = $parser->parseOptions($queue);
        
        $this->assertSame($id, $queue->getHead()); // ensure the token doesn't get dequeued
        $this->assertEquals("opt", $options[0]->name);
    }
    
    #[Test]
    public function parse_unsatisfied_option_id()
    {
        $id = new LiteralIdToken("unexpected");
        $queue = new TokenQueue;
        $queue->enqueue($id);
        $parser = new OptionsParserStub;
        $optParser = new Option("opt");
        $optParser->id(new LiteralIdToken("expected"));
        $parser->opt($optParser);
        
        $options = $parser->parseOptions($queue);
        
        $this->assertEmpty($options);
        $this->assertSame($id, $queue->getHead()); // ensure the token doesn't get dequeued
    }
    
    #[Test]
    public function parse_unsatisfied_option_arguments()
    {
        $queue = new TokenQueue;
        $queue->enqueue(new LongIdToken("opt"));
        $queue->enqueue(new LiteralIdToken("unexpected"));
        $opt = new Option("opt");
        $opt->id(new LongIdToken("expected"));
        $opt->arg("arg", fn($v) => $v == "expected");
        $parser = new OptionsParserStub;
        $parser->opt($opt, 1);
        
        $this->expectException(ParserException::class);
        
        $parser->parseOptions($queue);
    }
    
    #[Test]
    public function parse_satisfied_option()
    {
        $id1 = new LongIdToken("num");
        $arg1 = new ArgumentToken("12");
        $id2 = new LongIdToken("opt");
        $queue = new TokenQueue;
        $queue->enqueue($id2);
        $queue->enqueue($id1);
        $queue->enqueue($arg1);
        $queue->enqueue($id2);
        $opt = new Option("num");
        $opt->id($id1);
        $opt->arg("value", fn(string $v) => is_numeric($v));
        $parser = new OptionsParserStub;
        $parser->opt($opt);
        $opt = new Option("opt");
        $opt->id($id2);
        $parser->opt($opt);
        
        $options = $parser->parseOptions($queue);
        
        $this->assertCount(3, $options);
        $this->assertEquals("opt", $options[0]->name);
        $this->assertEquals("num", $options[1]->name);
        $this->assertEquals("opt", $options[2]->name);
    }
    
}

class OptionsParserStub
{
    use OptionsParserTrait;
}
