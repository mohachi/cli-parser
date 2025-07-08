<?php

use Mohachi\CliParser\Command;
use Mohachi\CliParser\Exception\ParserException;
use Mohachi\CliParser\Token\ArgumentToken;
use Mohachi\CliParser\Token\IdToken;
use Mohachi\CliParser\TokenQueue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Command::class)]
class CommandTest extends TestCase
{
    
    /* METHOD: construct */
    
    #[Test]
    public function construct_empty_name()
    {
        $this->expectException(InvalidArgumentException::class);
        
        new Command("");
    }
    
    /* METHOD: parse */
    
    #[Test]
    public function parse_empty_queue()
    {
        $parser = new Command("cmd");
        $parser->id(new IdToken("cmd"));
        
        $this->expectException(UnderflowException::class);
        
        $parser->parse(new TokenQueue);
    }
    
    #[Test]
    public function parse_unsatisfied_id()
    {
        $queue = new TokenQueue;
        $parser = new Command("cmd");
        $parser->id(new IdToken("cmd"));
        $queue->enqueue(new IdToken("unexpected"));
        
        $this->expectException(ParserException::class);
        
        $parser->parse($queue);
    }
    
    #[Test]
    public function parse_unsatisfied_argument()
    {
        $id = new IdToken("--num");
        $queue = new TokenQueue;
        $queue->enqueue($id);
        $queue->enqueue(new IdToken("unexpected"));
        $parser = new Command("number");
        $parser->id($id);
        $parser->arg("arg", fn($v) => is_numeric($v));
        
        $this->expectException(ParserException::class);
        
        $parser->parse($queue);
    }
    
    #[Test]
    public function parse_satisfied_command()
    {
        $id1 = new IdToken("cmd");
        $id2 = new IdToken("--opt");
        $arg = new ArgumentToken("value");
        $queue = new TokenQueue;
        $queue->enqueue($id1);
        $queue->enqueue($id2);
        $queue->enqueue($arg);
        $parser = new Command("cmd");
        $parser->id($id1);
        $parser->arg("arg");
        $parser->opt("opt")
            ->id($id2);
        
        $command = $parser->parse($queue);
        
        $this->assertEquals($id1, $command->id);
        $this->assertCount(1, $command->options);
        $this->assertEquals((object) ["arg" => "value"], $command->arguments);
        $this->assertEquals("opt", $command->options[0]->name);
    }
    
}
