<?php

use Mohachi\CliParser\Command;
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
