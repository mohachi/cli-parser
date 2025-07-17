<?php

use Mohachi\CliParser\Exception\InvalidArgumentException;
use Mohachi\CliParser\IdTokenizer\LongIdTokenizer;
use Mohachi\CliParser\Lexer;
use Mohachi\CliParser\Option;
use Mohachi\CliParser\Token\ArgumentToken;
use Mohachi\CliParser\Token\IdToken;
use Mohachi\CliParser\TokenQueue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Option::class)]
class OptionTest extends TestCase
{
    
    #[Test]
    public function construct_emptyName_throwsInvalidArgumentException()
    {
        $this->expectException(InvalidArgumentException::class);
        
        new Option("", new Lexer);
    }
    
    #[Test]
    public function parse_validTokens_returnsOptionStdClassObject()
    {
        $lexer = new Lexer;
        $lexer->register(new LongIdTokenizer, "long");
        $parser = new Option("number", $lexer);
        $parser->id("long", "--num")
            ->arg("arg", fn(string $v) => is_numeric($v));
        $argv = ["--num", "12"];
        $lexer->consume($argv);
        
        $option = $parser->parse();
        
        $this->assertEquals("--num", $option->id);
        $this->assertEquals((object) ["arg" => "12"], $option->arguments);
    }
    
}
