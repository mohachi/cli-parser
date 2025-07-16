<?php

use Mohachi\CliParser\Context;
use Mohachi\CliParser\Exception\ParserException;
use Mohachi\CliParser\Token\ArgumentToken;
use Mohachi\CliParser\Token\IdToken;
use Mohachi\CliParser\TokenQueue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ConcreteContext extends Context
{
    public function testParseOptions(TokenQueue $queue)
    {
        return $this->parseOptions($queue);
    }
}

#[CoversClass(Context::class)]
class ContextTest extends TestCase
{
    
    private ConcreteContext $context;
    
    protected function setUp(): void
    {
        $this->context = new ConcreteContext;
    }
    
    #[Test]
    public function opt_negativeMin_throwsInvalidArgumentException()
    {
        $this->expectException(InvalidArgumentException::class);
        
        $this->context->opt("opt", -1);
    }
    
    #[Test]
    public function opt_maxEqualToZero_throwsInvalidArgumentException()
    {
        $this->expectException(InvalidArgumentException::class);
        
        $this->context->opt("opt", 0, 0);
    }
    
    #[Test]
    public function opt_maxLessThanMin_throwsInvalidArgumentException()
    {
        $this->expectException(InvalidArgumentException::class);
        
        $this->context->opt("opt", 5, 2);
    }
    
    #[Test]
    public function parseOptions_ofEmptyQueue_returnsEmptyArray()
    {
        $this->context->opt("opt")->id(new IdToken("--opt"));
        
        $options = $this->context->parseOptions(new TokenQueue);
        
        $this->assertEmpty($options);
    }
    
    #[Test]
    public function parseOptions_ofEmptyParsers_returnsEmptyArrayAndNoTokenConsumption()
    {
        $id = new IdToken("cmd");
        $queue = new TokenQueue;
        $queue->enqueue($id);
        
        $options = $this->context->parseOptions($queue);
        
        $this->assertEmpty($options);
        $this->assertSame($id, $queue->getHead());
    }
    
    #[Test]
    public function parseOptions_requiredOptionAgainstEmptyQueue_throwsUnderflowException()
    {
        $this->context->opt("opt", 1)->id(new IdToken("--opt"));
        
        $this->expectException(UnderflowException::class);
        
        $this->context->parseOptions(new TokenQueue);
    }
    
    #[Test]
    public function parseOptions_insufficientOptionMin_throwsParserException()
    {
        $queue = new TokenQueue;
        $queue->enqueue(new IdToken("extra"));
        $this->context->opt("opt", 1)->id(new IdToken("--opt"));
        
        $this->expectException(ParserException::class);
        
        $this->context->parseOptions($queue);
    }
    
    #[Test]
    public function parseOptions_ofOverwhelmedOption_returnsArrayOfJustNeededAmountOfOption()
    {
        $id = new IdToken("--opt");
        $queue = new TokenQueue;
        $queue->enqueue($id);
        $queue->enqueue($id);
        $this->context->opt("opt", 0, 1)->id($id);
        
        $options = $this->context->parseOptions($queue);
        
        $this->assertSame($id, $queue->getHead()); // ensure the token doesn't get dequeued
        $this->assertEquals("opt", $options[0]->name);
    }
    
    #[Test]
    public function parseOptions_ofInvalidIdToken_returnsEmptyArray()
    {
        $id = new IdToken("unexpected");
        $queue = new TokenQueue;
        $queue->enqueue($id);
        $this->context->opt("opt")->id(new IdToken("expected"));
        
        $options = $this->context->parseOptions($queue);
        
        $this->assertEmpty($options);
        $this->assertSame($id, $queue->getHead()); // ensure the token doesn't get dequeued
    }
    
    #[Test]
    public function parseOptions_ofInvalidArgumentToken_throwsParserException()
    {
        $queue = new TokenQueue;
        $queue->enqueue(new IdToken("--opt"));
        $queue->enqueue(new IdToken("unexpected"));
        $this->context->opt("opt", 1)
            ->id(new IdToken("--expected"))
            ->arg("arg", fn($v) => $v == "expected");
        
        $this->expectException(ParserException::class);
        
        $this->context->parseOptions($queue);
    }
    
    #[Test]
    public function parseOptions_ofValidTokens_returnsArrayOfOptions()
    {
        $id1 = new IdToken("--num");
        $arg1 = new ArgumentToken("12");
        $id2 = new IdToken("--opt");
        $queue = new TokenQueue;
        $queue->enqueue($id2);
        $queue->enqueue($id1);
        $queue->enqueue($arg1);
        $queue->enqueue($id2);
        $this->context->opt("num")
            ->id($id1)
            ->arg("value", fn(string $v) => is_numeric($v));
        $this->context->opt("opt")
            ->id($id2);
        
        $options = $this->context->parseOptions($queue);
        
        $this->assertCount(3, $options);
        $this->assertEquals("opt", $options[0]->name);
        $this->assertEquals("num", $options[1]->name);
        $this->assertEquals("opt", $options[2]->name);
    }
    
}
