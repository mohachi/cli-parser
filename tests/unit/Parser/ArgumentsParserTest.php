<?php

use Mohachi\CommandLine\Exception\InvalidArgumentException;
use Mohachi\CommandLine\Exception\ParserException;
use Mohachi\CommandLine\Exception\UnderflowException;
use Mohachi\CommandLine\Parser\ArgumentsParser;
use Mohachi\CommandLine\SyntaxTree\ArgumentNode;
use Mohachi\CommandLine\TokenQueue;
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
        $tokens = new TokenQueue;
        $arg = new ArgumentNode;
        $arg->setValue("cmd");
        $tokens->push($arg);
        
        $node = (new ArgumentsParser)->parse($tokens);
        
        $this->assertEmpty($node->nodes);
    }
    
    #[Test]
    public function parse_unsatisfied_argument()
    {
        $tokens = new TokenQueue;
        $arg = new ArgumentNode;
        $arg->setValue("unexpected");
        $tokens->push($arg);
        $parser = new ArgumentsParser;
        $parser->append("num", fn($v) => is_numeric($v));
        
        $this->expectException(ParserException::class);
        
        $parser->parse($tokens);
    }
    
    #[Test]
    public function parse_satisfied_argument()
    {
        $tokens = new TokenQueue;
        $arg = new ArgumentNode;
        $arg->setValue("26");
        $tokens->push($arg);
        $parser = new ArgumentsParser;
        $parser->append("num", fn($v) => is_numeric($v));
        
        $node = $parser->parse($tokens);
        
        $this->assertContains($arg, $node->nodes);
        $this->assertArrayHasKey("num", $node->nodes);
    }
    
}
