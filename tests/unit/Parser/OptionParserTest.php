<?php

use Mohachi\CommandLine\Exception\InvalidArgumentException;
use Mohachi\CommandLine\Exception\ParserException;
use Mohachi\CommandLine\Parser\OptionParser;
use Mohachi\CommandLine\Token\ArgumentToken;
use Mohachi\CommandLine\Token\Id\LiteralIdToken;
use Mohachi\CommandLine\Token\Id\LongIdToken;
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
        
        new OptionParser("");
    }
    
    /* METHOD: parse */
    
    #[Test]
    public function parse_empty_stack()
    {
        $parser = new OptionParser("opt");
        $parser->id->append(new LongIdToken("opt"));
        
        $this->expectException(UnderflowException::class);
        
        $parser->parse(new TokenQueue);
    }
    
    #[Test]
    public function parse_unsatisfied_id_parser()
    {
        $queue = new TokenQueue;
        $parser = new OptionParser("opt");
        $queue->enqueue(new LiteralIdToken("unexpected"));
        $parser->id->append(new LiteralIdToken("expected"));
        
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
        $parser = new OptionParser("number");
        $parser->id->append($id);
        $parser->arguments->append("arg", fn($v) => is_numeric($v));
        
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
        $parser = new OptionParser("number");
        $parser->id->append($id);
        $parser->arguments->append("arg", fn(string $v) => is_numeric($v));
        
        $option = $parser->parse($queue);
        
        $this->assertEquals($id, $option->id);
        $this->assertEquals((object) ["arg" => "12"], $option->arguments);
    }
    
}
