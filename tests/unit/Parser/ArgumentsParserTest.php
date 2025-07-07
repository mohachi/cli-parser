<?php

use Mohachi\CliParser\Exception\InvalidArgumentException;
use Mohachi\CliParser\Exception\ParserException;
use Mohachi\CliParser\Exception\UnderflowException;
use Mohachi\CliParser\Parser\ArgumentsParser;
use Mohachi\CliParser\Token\ArgumentToken;
use Mohachi\CliParser\TokenQueue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(ArgumentsParser::class)]
class ArgumentsParserTest extends TestCase
{
    
    /* METHOD: append */
    
    #[Test]
    public function append_empty_name()
    {
        $this->expectException(InvalidArgumentException::class);
        
        (new ArgumentsParser)->append("");
    }
    
    /* METHOD: parse */
    
    #[Test]
    public function parse_empty_queue()
    {
        $parser = new ArgumentsParser;
        $parser->append("arg");
        
        $this->expectException(UnderflowException::class);
        
        $parser->parse(new TokenQueue);
    }
    
    #[Test]
    public function parse_against_empty_argument_list()
    {
        $queue = new TokenQueue;
        $queue->enqueue(new ArgumentToken("cmd"));
        
        $arguments = (new ArgumentsParser)->parse($queue);
        
        $this->assertObjectNotHasProperty("cmd", $arguments);
    }
    
    #[Test]
    public function parse_unsatisfied_argument()
    {
        $queue = new TokenQueue;
        $queue->enqueue(new ArgumentToken("unexpected"));
        $parser = new ArgumentsParser;
        $parser->append("num", fn($v) => is_numeric($v));
        
        $this->expectException(ParserException::class);
        
        $parser->parse($queue);
    }
    
    #[Test]
    public function parse_satisfied_argument()
    {
        $queue = new TokenQueue;
        $arg = new ArgumentToken("26");
        $queue->enqueue($arg);
        $parser = new ArgumentsParser;
        $parser->append("num", fn($v) => is_numeric($v));
        
        $arguments = $parser->parse($queue);
        
        $this->assertObjectHasProperty("num", $arguments);
        $this->assertEquals($arguments->num, 26);
    }
    
}
