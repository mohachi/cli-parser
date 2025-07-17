<?php

use Mohachi\CliParser\Context;
use Mohachi\CliParser\Exception\ParserException;
use Mohachi\CliParser\IdTokenizer\LiteralIdTokenizer;
use Mohachi\CliParser\IdTokenizer\LongIdTokenizer;
use Mohachi\CliParser\Lexer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ConcreteContext extends Context
{
    public function __construct(Lexer $lexer)
    {
        $this->lexer = $lexer;
    }
    
    public function testParseOptions()
    {
        return $this->parseOptions();
    }
}

#[CoversClass(Context::class)]
class ContextTest extends TestCase
{
    
    private Lexer $lexer;
    private ConcreteContext $context;
    
    protected function setUp(): void
    {
        $this->lexer = new Lexer;
        $this->context = new ConcreteContext($this->lexer);
        $this->lexer->register(new LongIdTokenizer, "long");
        $this->lexer->register(new LiteralIdTokenizer, "literal");
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
    public function parseOptions_ofEmptyLexer_returnsEmptyArray()
    {
        $this->context->opt("opt")->id("long", "--opt");
        
        $options = $this->context->testParseOptions();
        
        $this->assertEmpty($options);
    }
    
    #[Test]
    public function parseOptions_ofEmptyParsers_returnsEmptyArrayAndNoTokenHasConsumed()
    {
        $id = $this->lexer->get("literal")->create("cmd");
        $argv = ["cmd"];
        $this->lexer->consume($argv);
        
        $options = $this->context->testParseOptions();
        
        $this->assertEmpty($options);
        $this->assertSame($id, $this->lexer->current());
    }
    
    #[Test]
    public function parseOptions_requiredOptionAgainstEmptyLexer_throwsUnderflowException()
    {
        $this->context->opt("opt", 1)->id("long", "--opt");
        
        $this->expectException(UnderflowException::class);
        
        $this->context->testParseOptions();
    }
    
    #[Test]
    public function parseOptions_insufficientOptionMin_throwsParserException()
    {
        $this->context->opt("opt", 1)->id("long", "--opt");
        $this->lexer->get("literal")->create("extra");
        $argv = ["extra"];
        $this->lexer->consume($argv);
        
        $this->expectException(ParserException::class);
        
        $this->context->testParseOptions();
    }
    
    #[Test]
    public function parseOptions_ofOverwhelmedOption_returnsArrayOfJustNeededAmountOfOption()
    {
        $this->context->opt("opt", 0, 1)->id("long", "opt");
        $id = $this->lexer->get("long")->create("opt");
        $argv = ["--opt", "--opt"];
        $this->lexer->consume($argv);
        
        $options = $this->context->testParseOptions();
        
        $this->assertSame($id, $this->lexer->current()); // ensure the token doesn't get dequeued
        $this->assertEquals("opt", $options[0]->name);
        $this->assertEquals("--opt", $options[0]->id);
    }
    
    #[Test]
    public function parseOptions_ofInvalidIdToken_returnsEmptyArray()
    {
        $this->context->opt("opt")->id("literal", "expected");
        $id = $this->lexer->get("literal")->create("unexpected");
        $argv = ["unexpected"];
        $this->lexer->consume($argv);
        
        $options = $this->context->testParseOptions();
        
        $this->assertEmpty($options);
        $this->assertSame($id, $this->lexer->current()); // ensure the token doesn't get dequeued
    }
    
    #[Test]
    public function parseOptions_ofInvalidArgumentToken_throwsParserException()
    {
        $this->context->opt("opt", 1)
            ->id("long", "--expected")
            ->arg("arg", fn($v) => $v == "expected");
        $argv = ["--opt", "unexpected"];
        $this->lexer->get("long")->create("opt");
        $this->lexer->get("literal")->create("unexpected");
        $this->lexer->consume($argv);
        
        $this->expectException(ParserException::class);
        
        $this->context->testParseOptions();
    }
    
    #[Test]
    public function parseOptions_ofValidTokens_returnsArrayOfOptions()
    {
        $this->context->opt("num")
            ->id("long", "--num")
            ->arg("value", fn(string $v) => is_numeric($v));
        $this->context->opt("opt")
            ->id("long", "--opt");
        $argv = ["--opt", "--num", "12", "--opt"];
        $this->lexer->get("long")->create("--num");
        $this->lexer->get("long")->create("--opt");
        $this->lexer->consume($argv);
        
        $options = $this->context->testParseOptions();
        
        $this->assertCount(3, $options);
        $this->assertEquals("opt", $options[0]->name);
        $this->assertEquals("num", $options[1]->name);
        $this->assertEquals("opt", $options[2]->name);
    }
    
}
