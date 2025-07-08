<?php

use Mohachi\CliParser\Exception\InvalidArgumentException;
use Mohachi\CliParser\Exception\ParserException;
use Mohachi\CliParser\Exception\UnderflowException;
use Mohachi\CliParser\Option;
use Mohachi\CliParser\OptionsParserTrait;
use Mohachi\CliParser\Token\ArgumentToken;
use Mohachi\CliParser\Token\IdToken;
use Mohachi\CliParser\TokenQueue;
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
        $this->expectException(InvalidArgumentException::class);
        
        (new OptionsParserStub)->opt("opt", -1);
    }
    
    #[Test]
    public function appent_max_equal_to_zero()
    {
        $this->expectException(InvalidArgumentException::class);
        
        (new OptionsParserStub)->opt("opt", 0, 0);
    }
    
    #[Test]
    public function appent_positive_max_less_than_min()
    {
        $this->expectException(InvalidArgumentException::class);
        
        (new OptionsParserStub)->opt("opt", 5, 2);
    }
    
    /* METHOD: parse */
    
    #[Test]
    public function parse_empty_queue()
    {
        $parser = new OptionsParserStub;
        $parser->opt("opt")->id(new IdToken("--opt"));
        
        $options = $parser->parseOptions(new TokenQueue);
        
        $this->assertEmpty($options);
    }
    
    #[Test]
    public function parse_against_empty_parsers()
    {
        $id = new IdToken("cmd");
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
        $parser->opt("opt", 1)->id(new IdToken("--opt"));
        
        $this->expectException(UnderflowException::class);
        
        $parser->parseOptions(new TokenQueue);
    }
    
    #[Test]
    public function parse_insufficient_option_min()
    {
        $queue = new TokenQueue;
        $parser = new OptionsParserStub;
        $queue->enqueue(new IdToken("extra"));
        $parser->opt("opt", 1)->id(new IdToken("--opt"));
        
        $this->expectException(ParserException::class);
        
        $parser->parseOptions($queue);
    }
    
    #[Test]
    public function parse_overwhelmed_option()
    {
        $id = new IdToken("--opt");
        $queue = new TokenQueue;
        $queue->enqueue($id);
        $queue->enqueue($id);
        $parser = new OptionsParserStub;
        $parser->opt("opt", 0, 1)->id($id);
        
        $options = $parser->parseOptions($queue);
        
        $this->assertSame($id, $queue->getHead()); // ensure the token doesn't get dequeued
        $this->assertEquals("opt", $options[0]->name);
    }
    
    #[Test]
    public function parse_unsatisfied_option_id()
    {
        $id = new IdToken("unexpected");
        $queue = new TokenQueue;
        $queue->enqueue($id);
        $parser = new OptionsParserStub;
        $parser->opt("opt")->id(new IdToken("expected"));
        
        $options = $parser->parseOptions($queue);
        
        $this->assertEmpty($options);
        $this->assertSame($id, $queue->getHead()); // ensure the token doesn't get dequeued
    }
    
    #[Test]
    public function parse_unsatisfied_option_arguments()
    {
        $queue = new TokenQueue;
        $queue->enqueue(new IdToken("--opt"));
        $queue->enqueue(new IdToken("unexpected"));
        $parser = new OptionsParserStub;
        $parser->opt("opt", 1)
            ->id(new IdToken("--expected"))
            ->arg("arg", fn($v) => $v == "expected");
        
        $this->expectException(ParserException::class);
        
        $parser->parseOptions($queue);
    }
    
    #[Test]
    public function parse_satisfied_option()
    {
        $id1 = new IdToken("--num");
        $arg1 = new ArgumentToken("12");
        $id2 = new IdToken("--opt");
        $queue = new TokenQueue;
        $queue->enqueue($id2);
        $queue->enqueue($id1);
        $queue->enqueue($arg1);
        $queue->enqueue($id2);
        $parser = new OptionsParserStub;
        $parser->opt("num")
            ->id($id1)
            ->arg("value", fn(string $v) => is_numeric($v));
        $parser->opt("opt")
            ->id($id2);
        
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
