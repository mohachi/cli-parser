<?php

use Mohachi\CommandLine\Option;
use Mohachi\CommandLine\Exception\InvalidArgumentException;
use Mohachi\CommandLine\Exception\ParserException;
use Mohachi\CommandLine\Token\ArgumentToken;
use Mohachi\CommandLine\Token\Id\LiteralIdToken;
use Mohachi\CommandLine\Token\Id\LongIdToken;
use Mohachi\CommandLine\TokenQueue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Option::class)]
class OptionTest extends TestCase
{
    
    /* METHOD: construct */
    
    #[Test]
    public function construct_empty_name()
    {
        $this->expectException(InvalidArgumentException::class);
        
        new Option("");
    }
    
    /* METHOD: parse */
    
    #[Test]
    public function parse_empty_stack()
    {
        $parser = new Option("opt");
        $parser->id(new LongIdToken("opt"));
        
        $this->expectException(UnderflowException::class);
        
        $parser->parse(new TokenQueue);
    }
    
    #[Test]
    public function parse_unsatisfied_id_parser()
    {
        $queue = new TokenQueue;
        $parser = new Option("opt");
        $queue->enqueue(new LiteralIdToken("unexpected"));
        $parser->id(new LiteralIdToken("expected"));
        
        $this->expectException(ParserException::class);
        
        $parser->parse($queue);
    }
    
    #[Test]
    public function parse_unsatisfied_argument_parser()
    {
        $id = new LongIdToken("num");
        $queue = new TokenQueue;
        $queue->enqueue($id);
        $queue->enqueue(new ArgumentToken("unexpected"));
        $parser = new Option("number");
        $parser->id($id);
        $parser->arg("arg", fn($v) => is_numeric($v));
        
        $this->expectException(ParserException::class);
        
        $parser->parse($queue);
    }
    
    #[Test]
    public function parse_satisfied_option()
    {
        $id = new LongIdToken("num");
        $queue = new TokenQueue;
        $queue->enqueue($id);
        $queue->enqueue(new ArgumentToken("12"));
        $parser = new Option("number");
        $parser->id($id);
        $parser->arg("arg", fn(string $v) => is_numeric($v));
        
        $option = $parser->parse($queue);
        
        $this->assertEquals($id, $option->id);
        $this->assertEquals((object) ["arg" => "12"], $option->arguments);
    }
    
}
