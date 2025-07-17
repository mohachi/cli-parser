<?php

use Mohachi\CliParser\Command;
use Mohachi\CliParser\IdTokenizer\LiteralIdTokenizer;
use Mohachi\CliParser\IdTokenizer\LongIdTokenizer;
use Mohachi\CliParser\Lexer;
use Mohachi\CliParser\Token\ArgumentToken;
use Mohachi\CliParser\Token\IdToken;
use Mohachi\CliParser\TokenQueue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Command::class)]
class CommandTest extends TestCase
{
    
    #[Test]
    public function construct_emptyName_throwsInvalidArgumentException()
    {
        $this->expectException(InvalidArgumentException::class);
        
        new Command("");
    }
    
    #[Test]
    public function parse_validTokens_returnsCommandStdClassObject()
    {
        $lexer = new Lexer;
        $lexer->register(new LongIdTokenizer, "long");
        $lexer->register(new LiteralIdTokenizer, "literal");
        $parser = new Command("cmd", $lexer);
        $parser->id("literal", "cmd")
            ->arg("arg")
            ->opt("opt")
                ->id("long", "--opt");
        $argv = ["cmd", "--opt", "value"];
        
        $command = $parser->parse($argv);
        
        $this->assertEquals("cmd", $command->id);
        $this->assertCount(1, $command->options);
        $this->assertEquals((object) ["arg" => "value"], $command->arguments);
        $this->assertEquals("opt", $command->options[0]->name);
        $this->assertEquals("--opt", $command->options[0]->id);
    }
    
}
