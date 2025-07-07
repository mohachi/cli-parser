<?php

use Mohachi\CommandLine\ArgumentsParserTrait;
use Mohachi\CommandLine\Exception\InvalidArgumentException;
use Mohachi\CommandLine\Exception\ParserException;
use Mohachi\CommandLine\Exception\UnderflowException;
use Mohachi\CommandLine\Token\ArgumentToken;
use Mohachi\CommandLine\TokenQueue;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversTrait(ArgumentsParserTrait::class)]
class ArgumentsParserTraitTest extends TestCase
{
    
    /* METHOD: append */
    
    #[Test]
    public function append_empty_name()
    {
        $this->expectException(InvalidArgumentException::class);
        
        (new ArgumentsParserStub)->arg("");
    }
    
    /* METHOD: parse */
    
    #[Test]
    public function parse_empty_queue()
    {
        $parser = new ArgumentsParserStub;
        $parser->arg("arg");
        
        $this->expectException(UnderflowException::class);
        
        $parser->parseArguments(new TokenQueue);
    }
    
    #[Test]
    public function parse_against_empty_argument_list()
    {
        $queue = new TokenQueue;
        $queue->enqueue(new ArgumentToken("cmd"));
        
        $arguments = (new ArgumentsParserStub)->parseArguments($queue);
        
        $this->assertObjectNotHasProperty("cmd", $arguments);
    }
    
    #[Test]
    public function parse_unsatisfied_argument()
    {
        $queue = new TokenQueue;
        $queue->enqueue(new ArgumentToken("unexpected"));
        $parser = new ArgumentsParserStub;
        $parser->arg("num", fn($v) => is_numeric($v));
        
        $this->expectException(ParserException::class);
        
        $parser->parseArguments($queue);
    }
    
    #[Test]
    public function parse_satisfied_argument()
    {
        $queue = new TokenQueue;
        $arg = new ArgumentToken("26");
        $queue->enqueue($arg);
        $parser = new ArgumentsParserStub;
        $parser->arg("num", fn($v) => is_numeric($v));
        
        $arguments = $parser->parseArguments($queue);
        
        $this->assertObjectHasProperty("num", $arguments);
        $this->assertEquals($arguments->num, 26);
    }
    
}

class ArgumentsParserStub
{
    use ArgumentsParserTrait;
}
