<?php

use Mohachi\CliParser\Component;
use Mohachi\CliParser\Exception\InvalidArgumentException;
use Mohachi\CliParser\Exception\ParserException;
use Mohachi\CliParser\Exception\UnderflowException;
use Mohachi\CliParser\IdTokenizer\LiteralIdTokenizer;
use Mohachi\CliParser\Lexer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ConcreteComponent extends Component
{
    public function __construct(Lexer $lexer)
    {
        $this->lexer = $lexer;
    }
    
    public function testParseId()
    {
        return $this->parseId();
    }
    
    public function testParseArguments()
    {
        return $this->parseArguments();
    }
}

#[CoversClass(Component::class)]
class ComponentTest extends TestCase
{
    
    private Lexer $lexer;
    private ConcreteComponent $component;
    
    protected function setUp(): void
    {
        $this->lexer = new Lexer;
        $this->component = new ConcreteComponent($this->lexer);
        $this->lexer->register(new LiteralIdTokenizer, "literal");
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
    public function parseId_withNoIdTokens_throwsParserException()
    {
        $this->lexer->get("literal")->create("id");
        $argv = ["first"];
        $this->lexer->consume($argv);
        
        $this->expectException(ParserException::class);
        
        $this->component->testParseId();
    }
    
    #[Test]
    public function parseId_fromEmptyLexer_throwsUnderflowException()
    {
        $this->lexer->get("literal")->create("id");
        
        $this->expectException(UnderflowException::class);
        
        $this->component->testParseId();
    }
    
    #[Test]
    public function parseId_ofInvalidToken_throwsParserException()
    {
        $this->component->id("literal", "expected");
        $argv = ["unexpected"];
        $this->lexer->consume($argv);
        
        $this->expectException(ParserException::class);
        
        $this->component->testParseId();
    }
    
    #[Test]
    public function parseId_ofValidToken_returnsItsStringValue()
    {
        $this->component->id("literal", "expected");
        $argv = ["expected"];
        $this->lexer->consume($argv);
        
        $syntax = $this->component->testParseId();
        
        $this->assertSame("expected", $syntax);
    }
    
    #[Test]
    public function parseArguments_fromEmptyLexer_throwsUnderflowException()
    {
        $this->component->arg("arg");
        
        $this->expectException(UnderflowException::class);
        
        $this->component->testParseArguments();
    }
    
    #[Test]
    public function parseArguments_ofEmptyArgumentList_returnsEmptyObject()
    {
        $argv = ["arg"];
        $this->lexer->consume($argv);
        
        $arguments = $this->component->testParseArguments();
        
        $this->assertEmpty(get_object_vars($arguments));
    }
    
    #[Test]
    public function parseArguments_ofInvalidToken_throwsParserException()
    {
        $this->component->arg("num", fn($v) => is_numeric($v));
        $argv = ["unexpected"];
        $this->lexer->consume($argv);
        
        $this->expectException(ParserException::class);
        
        $this->component->testParseArguments();
    }
    
    #[Test]
    public function parseArguments_ofValidTokens_returnsObjectOfThereValues()
    {
        $this->component->arg("num", fn($v) => is_numeric($v));
        $argv = ["26"];
        $this->lexer->consume($argv);
        
        $arguments = $this->component->testParseArguments();
        
        $this->assertObjectHasProperty("num", $arguments);
        $this->assertEquals($arguments->num, 26);
    }
    
}
