<?php

use Mohachi\CliParser\Exception\ParserException;
use Mohachi\CliParser\Exception\UnderflowException;
use Mohachi\CliParser\IdParserTrait;
use Mohachi\CliParser\Token\Id\LiteralIdToken;
use Mohachi\CliParser\Token\Id\LongIdToken;
use Mohachi\CliParser\TokenQueue;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversTrait(IdParserTrait::class)]
class IdParserTraitTest extends TestCase
{
    
    /* METHOD: parse */
    
    #[Test]
    public function parse_empty_queue()
    {
        $parser = new IdParserStub;
        $parser->id(new LongIdToken("cmd"));
        
        $this->expectException(UnderflowException::class);
        
        $parser->parseId(new TokenQueue);
    }
    
    #[Test]
    public function parse_against_empty_id_list()
    {
        $queue = new TokenQueue;
        $queue->enqueue(new LiteralIdToken("cmd"));
        
        $this->expectException(ParserException::class);
        
        (new IdParserStub)->parseId($queue);
    }
    
    #[Test]
    public function parse_unsatisfied_id()
    {
        $queue = new TokenQueue;
        $queue->enqueue(new LongIdToken("unexpected"));
        $parser = new IdParserStub;
        $parser->id(new LongIdToken("expected"));
        
        $this->expectException(ParserException::class);
        
        $parser->parseId($queue);
    }
    
    #[Test]
    public function parse_satisfied_id()
    {
        $queue = new TokenQueue;
        $parser = new IdParserStub;
        $id = new LongIdToken("expected");
        $queue->enqueue($id);
        $parser->id($id);
        
        $id = $parser->parseId($queue);
        
        $this->assertSame("--expected", $id);
    }
    
}

class IdParserStub
{
    use IdParserTrait;
}
