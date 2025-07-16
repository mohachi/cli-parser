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

class ConcreteComponent extends Component
{
    public function testParseId(TokenQueue $queue)
    {
        return $this->parseId($queue);
    }
    
    public function testParseArguments(TokenQueue $queue)
    {
        return $this->parseArguments($queue);
    }
}

#[CoversClass(Component::class)]
class ComponentTest extends TestCase
{
    
    private ConcreteComponent $component;
    
    protected function setUp(): void
    {
        $this->component = new ConcreteComponent;
    }
    
    #[Test]
    public function arg_emptyName_throwsInvalidArgumentException()
    {
        $this->expectException(InvalidArgumentException::class);
        
        $this->component->arg("");
    }
    
    #[Test]
    public function arg_existingName_throwsInvalidArgumentException()
    {
        $this->component->arg("first");
        
        $this->expectException(InvalidArgumentException::class);
        
        $this->component->arg("first");
    }
    
    #[Test]
    public function parseId_fromEmptyQueue_throwsUnderflowException()
    {
        $this->component->id(new IdToken("--cmd"));
        
        $this->expectException(UnderflowException::class);
        
        $this->component->parseId(new TokenQueue);
    }
    
    #[Test]
    public function parseId_ofEmptyIdList_throwsParserException()
    {
        $queue = new TokenQueue;
        $queue->enqueue(new IdToken("cmd"));
        
        $this->expectException(ParserException::class);
        
        $this->component->parseId($queue);
    }
    
    #[Test]
    public function parseId_ofInvalidToken_throwsParserException()
    {
        $queue = new TokenQueue;
        $queue->enqueue(new IdToken("--unexpected"));
        $this->component->id(new IdToken("--expected"));
        
        $this->expectException(ParserException::class);
        
        $this->component->parseId($queue);
    }
    
    #[Test]
    public function parseId_ofValidToken_returnsItsStringValue()
    {
        $queue = new TokenQueue;
        $id = new IdToken("--expected");
        $queue->enqueue($id);
        $this->component->id($id);
        
        $id = $this->component->parseId($queue);
        
        $this->assertSame("--expected", $id);
    }
    
    #[Test]
    public function parseArguments_fromEmptyQueue_throwsUnderflowException()
    {
        $this->component->arg("arg");
        
        $this->expectException(UnderflowException::class);
        
        $this->component->parseArguments(new TokenQueue);
    }
    
    #[Test]
    public function parseArguments_ofEmptyArgumentList_returnsEmptyObject()
    {
        $queue = new TokenQueue;
        $queue->enqueue(new ArgumentToken("cmd"));
        
        $arguments = $this->component->parseArguments($queue);
        
        $this->assertEmpty(get_object_vars($arguments));
    }
    
    #[Test]
    public function parseArguments_ofInvalidToken_throwsParserException()
    {
        $queue = new TokenQueue;
        $queue->enqueue(new ArgumentToken("unexpected"));
        $this->component->arg("num", fn($v) => is_numeric($v));
        
        $this->expectException(ParserException::class);
        
        $this->component->parseArguments($queue);
    }
    
    #[Test]
    public function parseArguments_ofValidTokens_returnsObjectOfThereValues()
    {
        $queue = new TokenQueue;
        $queue->enqueue(new ArgumentToken("26"));
        $this->component->arg("num", fn($v) => is_numeric($v));
        
        $arguments = $this->component->parseArguments($queue);
        
        $this->assertObjectHasProperty("num", $arguments);
        $this->assertEquals($arguments->num, 26);
    }
    
}
