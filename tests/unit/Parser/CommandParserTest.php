<?php

use Mohachi\CommandLine\Parser\CommandParser;
use Mohachi\CommandLine\Exception\ParserException;
use Mohachi\CommandLine\Parser\OptionParser;
use Mohachi\CommandLine\Token\ArgumentToken;
use Mohachi\CommandLine\Token\Identifier\LiteralIdentifierToken;
use Mohachi\CommandLine\Token\Identifier\LongIdentifierToken;
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
        
        new CommandParser("");
    }
    
    /* METHOD: parse */
    
    #[Test]
    public function parse_empty_queue()
    {
        $parser = new CommandParser("cmd");
        $parser->id->append(new LiteralIdentifierToken("cmd"));
        
        $this->expectException(UnderflowException::class);
        
        $parser->parse(new TokenQueue);
    }
    
    #[Test]
    public function parse_unsatisfied_id()
    {
        $queue = new TokenQueue;
        $parser = new CommandParser("cmd");
        $parser->id->append(new LiteralIdentifierToken("cmd"));
        $queue->enqueue(new LiteralIdentifierToken("unexpected"));
        
        $this->expectException(ParserException::class);
        
        $parser->parse($queue);
    }
    
    #[Test]
    public function parse_unsatisfied_argument()
    {
        $id = new LongIdentifierToken("num");
        $queue = new TokenQueue;
        $queue->enqueue($id);
        $queue->enqueue(new LiteralIdentifierToken("unexpected"));
        $parser = new CommandParser("number");
        $parser->id->append($id);
        $parser->arguments->append("arg", fn($v) => is_numeric($v));
        
        $this->expectException(ParserException::class);
        
        $parser->parse($queue);
    }
    
    #[Test]
    public function parse_satisfied_command()
    {
        $id1 = new LiteralIdentifierToken("cmd");
        $id2 = new LongIdentifierToken("opt");
        $arg = new ArgumentToken("value");
        $queue = new TokenQueue;
        $queue->enqueue($id1);
        $queue->enqueue($id2);
        $queue->enqueue($arg);
        $parser = new CommandParser("cmd");
        $parser->id->append($id1);
        $parser->arguments->append("arg");
        $optParser = new OptionParser("opt");
        $optParser->id->append($id2);
        $parser->options->append($optParser);
        
        $command = $parser->parse($queue);
        
        $this->assertEquals($id1, $command->id);
        $this->assertCount(1, $command->options);
        $this->assertEquals((object) ["arg" => "value"], $command->arguments);
        $this->assertEquals("opt", $command->options[0]->name);
    }
    
}
