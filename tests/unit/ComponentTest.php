<?php

use Mohachi\CliParser\Component;
use Mohachi\CliParser\Exception\ParserException;
use Mohachi\CliParser\Exception\UnderflowException;
use Mohachi\CliParser\Token\ArgumentToken;
use Mohachi\CliParser\Token\IdToken;
use Mohachi\CliParser\TokenQueue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Component::class)]
class ComponentTest extends TestCase
{
    
    /* METHOD: id */
    
    // ...
    
    /* METHOD: arg */
    
    #[Test]
    public function append_empty_name()
    {
        $this->expectException(InvalidArgumentException::class);
        
        (new ConcreteComponent)->arg("");
    }
    
    /* METHOD: parseId */
    
    #[Test]
    public function parse_id_against_empty_queue()
    {
        $parser = new ConcreteComponent;
        $parser->id(new IdToken("--cmd"));
        
        $this->expectException(UnderflowException::class);
        
        $parser->parseId(new TokenQueue);
    }
    
    #[Test]
    public function parse_id_against_empty_id_list()
    {
        $queue = new TokenQueue;
        $queue->enqueue(new IdToken("cmd"));
        $parser = new ConcreteComponent;
        
        $this->expectException(ParserException::class);
        
        $parser->parseId($queue);
    }
    
    #[Test]
    public function parse_unsatisfied_id()
    {
        $queue = new TokenQueue;
        $queue->enqueue(new IdToken("--unexpected"));
        $parser = new ConcreteComponent;
        $parser->id(new IdToken("--expected"));
        
        $this->expectException(ParserException::class);
        
        $parser->parseId($queue);
    }
    
    #[Test]
    public function parse_satisfied_id()
    {
        $queue = new TokenQueue;
        $parser = new ConcreteComponent;
        $id = new IdToken("--expected");
        $queue->enqueue($id);
        $parser->id($id);
        
        $id = $parser->parseId($queue);
        
        $this->assertSame("--expected", $id);
    }
    
    /* METHOD: parseArguments */
    
    #[Test]
    public function parse_args_against_empty_queue()
    {
        $parser = new ConcreteComponent;
        $parser->arg("arg");
        
        $this->expectException(UnderflowException::class);
        
        $parser->parseArguments(new TokenQueue);
    }
    
    #[Test]
    public function parse_against_empty_argument_list()
    {
        $queue = new TokenQueue;
        $queue->enqueue(new ArgumentToken("cmd"));
        
        $arguments = (new ConcreteComponent)->parseArguments($queue);
        
        $this->assertObjectNotHasProperty("cmd", $arguments);
    }
    
    #[Test]
    public function parse_unsatisfied_argument()
    {
        $queue = new TokenQueue;
        $queue->enqueue(new ArgumentToken("unexpected"));
        $parser = new ConcreteComponent;
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
        $parser = new ConcreteComponent;
        $parser->arg("num", fn($v) => is_numeric($v));
        
        $arguments = $parser->parseArguments($queue);
        
        $this->assertObjectHasProperty("num", $arguments);
        $this->assertEquals($arguments->num, 26);
    }
    
}

class ConcreteComponent extends Component
{
    
}
