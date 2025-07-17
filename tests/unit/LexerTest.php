<?php

namespace Mohachi\CliParser\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Mohachi\CliParser\Lexer;
use Mohachi\CliParser\IdTokenizer\LiteralIdTokenizer;
use Mohachi\CliParser\Token\ArgumentToken;
use Mohachi\CliParser\Exception\InvalidArgumentException;
use Mohachi\CliParser\Exception\LogicException;
use Mohachi\CliParser\Exception\OutOfBoundsException;
use Mohachi\CliParser\Exception\UnderflowException;
use Mohachi\CliParser\IdTokenizer\LongIdTokenizer;
use Mohachi\CliParser\IdTokenizer\ShortIdTokenizer;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Lexer::class)]
class LexerTest extends TestCase
{
    
    private Lexer $lexer;

    protected function setUp(): void
    {
        $this->lexer = new Lexer();
    }

    #[Test]
    public function get_unregisteredTokenizer_throwsOutOfBoundsException(): void
    {
        $this->expectException(OutOfBoundsException::class);
        
        $this->lexer->get(LiteralIdTokenizer::class);
    }

    #[Test]
    public function register_sameTokenizerNameTwice_throwsLogicException(): void
    {
        $this->lexer->register(new LiteralIdTokenizer());
        
        $this->expectException(LogicException::class);
        
        $this->lexer->register(new LiteralIdTokenizer());
    }

    #[Test]
    public function get_registeredTokenizer_returnsIt(): void
    {
        $tokenizer = new LiteralIdTokenizer;
        $this->lexer->register($tokenizer);
        
        $retrieved = $this->lexer->get(LiteralIdTokenizer::class);
        
        $this->assertSame($tokenizer, $retrieved);
    }
    
    #[Test]
    public function rewind_beforeConsume_throwsUnderflowException()
    {
        $this->lexer->register(new LiteralIdTokenizer);
        
        $this->expectException(UnderflowException::class);
        
        $this->lexer->rewind();
    }
    
    #[Test]
    public function valid_beforeConsume_returnsFalse()
    {
        $this->assertFalse($this->lexer->valid());
    }
    
    #[Test]
    public function current_beforeConsume_throwsUnderflowException()
    {
        $this->expectException(UnderflowException::class);
        
        $this->lexer->current();
    }
    
    #[Test]
    public function key_beforeConsume_throwsUnderflowException()
    {
        $this->expectException(UnderflowException::class);
        
        $this->lexer->key();
    }
    
    #[Test]
    public function next_beforeConsume_throwsUnderflowException()
    {
        $this->expectException(UnderflowException::class);
        
        $this->lexer->next();
    }
    
    #[Test]
    public function consume_emptyArgvArray_throwsInvalidArgumentException(): void
    {
        $argv = [];
        
        $this->expectException(InvalidArgumentException::class);
        
        $this->lexer->consume($argv);
    }

    #[Test]
    public function consume_aNonListArgvArray_throwsInvalidArgumentException(): void
    {
        $argv = [1 => "test", 5 => "arg"];
        
        $this->expectException(InvalidArgumentException::class);
        
        $this->lexer->consume($argv);
    }

    #[Test]
    public function consume_argvArrayThatHasNonStringValue_throwsInvalidArgumentException(): void
    {
        $argv = [[]];
        
        $this->expectException(InvalidArgumentException::class);
        
        $this->lexer->consume($argv);
    }

    #[Test]
    public function consume_differentConsumeCalls_resetsBuffer(): void
    {
        $argv1 = ["first"];
        $argv2 = ["second"];
        $this->lexer->consume($argv1);
        $this->lexer->current();
        $this->lexer->next();
        $this->lexer->consume($argv2);
        
        $token = $this->lexer->current();
        
        $this->assertEquals(new ArgumentToken("second"), $token);
    }

    #[Test]
    public function current_noMoreTokens_throwsUnderflowException(): void
    {
        $argv = ["first"];
        $this->lexer->consume($argv);
        $this->lexer->next();
        
        $this->expectException(UnderflowException::class);
        $this->expectExceptionMessage("No more tokens available");
        
        $this->lexer->current();
    }
    
    #[Test]
    public function key_noMoreTokens_throwsUnderflowException(): void
    {
        $argv = ["first"];
        $this->lexer->consume($argv);
        $this->lexer->next();
        
        $this->expectException(UnderflowException::class);
        $this->expectExceptionMessage("No more tokens available");
        
        $this->lexer->key();
    }

    #[Test]
    public function next_noMoreTokens_throwsUnderflowException(): void
    {
        $argv = ["first"];
        $this->lexer->consume($argv);
        $this->lexer->next();
        
        $this->expectException(UnderflowException::class);
        $this->expectExceptionMessage("No more tokens available");
        
        $this->lexer->next();
    }

    #[Test]
    public function iterateThrough_NonIdTokenizedTokens_iteratesThroughArgumentTokens()
    {
        $argv = ["first", "second", "third"];
        $tokenizer = new LiteralIdTokenizer;
        $this->lexer->register($tokenizer);
        $tokenizer->create("unexpected");
        $this->lexer->consume($argv);
        
        foreach( $argv as $i => $arg )
        {
            $this->assertTrue($this->lexer->valid());
            $this->assertEquals($i, $this->lexer->key());
            $this->assertEquals(new ArgumentToken($arg), $this->lexer->current());
            $this->lexer->next();
        }
        
        $this->assertFalse($this->lexer->valid());
        
        $this->lexer->rewind();
        $this->assertTrue($this->lexer->valid());
        $this->assertEquals(0, $this->lexer->key());
        $this->assertEquals(new ArgumentToken("first"), $this->lexer->current());
    }
    
    #[Test]
    public function iterateThrough_idTokenizedTokens_buffersAndIteratesThroughThemFirst()
    {
        $tokens = [];
        $argv = ["cmd", "--long=arg", "-ab"];
        $tokenizers = [
            "long" => new LongIdTokenizer,
            "short" => new ShortIdTokenizer,
            "literal" => new LiteralIdTokenizer,
        ];
        
        foreach( $tokenizers as $tokenizer )
        {
            $this->lexer->register($tokenizer);
        }
        
        $tokens[] = $tokenizers["literal"]->create("cmd");
        $tokens[] = $tokenizers["long"]->create("long");
        $tokens[] = new ArgumentToken("arg");
        $tokens[] = $tokenizers["short"]->create("a");
        $tokens[] = $tokenizers["short"]->create("b");
        $this->lexer->consume($argv);
        
        
        foreach( $tokens as $i => $expected )
        {
            $this->assertTrue($this->lexer->valid());
            $this->assertEquals($i, $this->lexer->key());
            $this->assertEquals($expected, $this->lexer->current());
            $this->lexer->next();
        }
        
        $this->assertFalse($this->lexer->valid());
        
        $this->lexer->rewind();
        $this->assertTrue($this->lexer->valid());
        $this->assertEquals(0, $this->lexer->key());
        $this->assertEquals($tokens[0], $this->lexer->current());
    }

}
