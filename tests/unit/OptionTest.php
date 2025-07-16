<?php

use Mohachi\CliParser\Exception\InvalidArgumentException;
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
        
        new Option("");
    }
    
    #[Test]
    public function parse_validTokens_returnsOptionStdClassObject()
    {
        $id = new IdToken("--num");
        $queue = new TokenQueue;
        $queue->enqueue($id);
        $queue->enqueue(new ArgumentToken("12"));
        $parser = new Option("number");
        $parser->id($id);
        $parser->arg("arg", fn(string $v) => is_numeric($v));
        
        $option = $parser->parse($queue);
        
        $this->assertEquals($id, $option->id);
        $this->assertEquals((object) ["arg" => "12"], $option->arguments);
    }
    
}
