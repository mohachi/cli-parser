<?php

use Mohachi\CliParser\Exception\InvalidArgumentException;
use Mohachi\CliParser\Exception\ParserException;
use Mohachi\CliParser\Exception\UnderflowException;
use Mohachi\CliParser\Parser\OptionParser;
use Mohachi\CliParser\Parser\OptionsParser;
use Mohachi\CliParser\Token\ArgumentToken;
use Mohachi\CliParser\Token\Id\LiteralIdToken;
use Mohachi\CliParser\Token\Id\LongIdToken;
use Mohachi\CliParser\TokenQueue;
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
        $optParser = new OptionParser("opt");
        $optParser->id->append(new LongIdToken("opt"));
        
        $this->expectException(InvalidArgumentException::class);
        
        (new OptionsParser)->append($optParser, -1);
    }
    
    #[Test]
    public function appent_max_equal_to_zero()
    {
        $optParser = new OptionParser("opt");
        $optParser->id->append(new LongIdToken("opt"));
        
        $this->expectException(InvalidArgumentException::class);
        
        (new OptionsParser)->append($optParser, 0, 0);
    }
    
    #[Test]
    public function appent_positive_max_less_than_min()
    {
        $optParser = new OptionParser("opt");
        $optParser->id->append(new LongIdToken("opt"));
        
        $this->expectException(InvalidArgumentException::class);
        
        (new OptionsParser)->append($optParser, 5, 2);
    }
    
    /* METHOD: parse */
    
    #[Test]
    public function parse_empty_queue()
    {
        $parser = new OptionsParser;
        $optParser = new OptionParser("opt");
        $optParser->id->append(new LiteralIdToken("cmd"));
        $parser->append($optParser);
        
        $options = $parser->parse(new TokenQueue);
        
        $this->assertEmpty($options);
    }
    
    #[Test]
    public function parse_against_empty_parsers()
    {
        $id = new LiteralIdToken("cmd");
        $queue = new TokenQueue;
        $queue->enqueue($id);
        
        $options = (new OptionsParser())->parse($queue);
        
        $this->assertEmpty($options);
        $this->assertSame($id, $queue->getHead()); // ensure the token doesn't get dequeued
    }
    
    #[Test]
    public function parse_required_option_agains_empty_queue()
    {
        $parser = new OptionsParser;
        $optParser = new OptionParser("opt");
        $optParser->id->append(new LongIdToken("opt"));
        $parser->append($optParser, 1);
        
        $this->expectException(UnderflowException::class);
        
        $parser->parse(new TokenQueue);
    }
    
    #[Test]
    public function parse_insufficient_option_min()
    {
        $queue = new TokenQueue;
        $queue->enqueue(new LiteralIdToken("extra"));
        $parser = new OptionsParser;
        $optParser = new OptionParser("opt");
        $optParser->id->append(new LongIdToken("opt"));
        $parser->append($optParser, 1);
        
        $this->expectException(ParserException::class);
        
        $parser->parse($queue);
    }
    
    #[Test]
    public function parse_overwhelmed_option()
    {
        $id = new LongIdToken("opt");
        $queue = new TokenQueue;
        $queue->enqueue($id);
        $queue->enqueue($id);
        $parser = new OptionsParser;
        $optParser = new OptionParser("opt");
        $optParser->id->append($id);
        $parser->append($optParser, 0, 1);
        
        $options = $parser->parse($queue);
        
        $this->assertSame($id, $queue->getHead()); // ensure the token doesn't get dequeued
        $this->assertEquals("opt", $options[0]->name);
    }
    
    #[Test]
    public function parse_unsatisfied_option_id()
    {
        $id = new LiteralIdToken("unexpected");
        $queue = new TokenQueue;
        $queue->enqueue($id);
        $parser = new OptionsParser;
        $optParser = new OptionParser("opt");
        $optParser->id->append(new LiteralIdToken("expected"));
        $parser->append($optParser);
        
        $options = $parser->parse($queue);
        
        $this->assertEmpty($options);
        $this->assertSame($id, $queue->getHead()); // ensure the token doesn't get dequeued
    }
    
    #[Test]
    public function parse_unsatisfied_option_arguments()
    {
        $queue = new TokenQueue;
        $queue->enqueue(new LongIdToken("opt"));
        $queue->enqueue(new LiteralIdToken("unexpected"));
        $opt = new OptionParser("opt");
        $opt->id->append(new LongIdToken("expected"));
        $opt->arguments->append("arg", fn($v) => $v == "expected");
        $parser = new OptionsParser;
        $parser->append($opt, 1);
        
        $this->expectException(ParserException::class);
        
        $parser->parse($queue);
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
        $opt = new OptionParser("num");
        $opt->id->append($id1);
        $opt->arguments->append("value", fn(string $v) => is_numeric($v));
        $parser = new OptionsParser;
        $parser->append($opt);
        $opt = new OptionParser("opt");
        $opt->id->append($id2);
        $parser->append($opt);
        
        $options = $parser->parse($queue);
        
        $this->assertCount(3, $options);
        $this->assertEquals("opt", $options[0]->name);
        $this->assertEquals("num", $options[1]->name);
        $this->assertEquals("opt", $options[2]->name);
    }
    
}
