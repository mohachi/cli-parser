<?php

use Mohachi\CliParser\Exception\ParserException;
use Mohachi\CliParser\Exception\UnderflowException;
use Mohachi\CliParser\Parser\IdParser;
use Mohachi\CliParser\Token\Id\LiteralIdToken;
use Mohachi\CliParser\Token\Id\LongIdToken;
use Mohachi\CliParser\TokenQueue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(IdParser::class)]
class IdParserTest extends TestCase
{
    
    /* METHOD: parse */
    
    #[Test]
    public function parse_empty_queue()
    {
        $parser = new IdParser;
        $parser->append(new LongIdToken("cmd"));
        
        $this->expectException(UnderflowException::class);
        
        $parser->parse(new TokenQueue);
    }
    
    #[Test]
    public function parse_against_empty_id_list()
    {
        $queue = new TokenQueue;
        $queue->enqueue(new LiteralIdToken("cmd"));
        
        $this->expectException(ParserException::class);
        
        (new IdParser)->parse($queue);
    }
    
    #[Test]
    public function parse_unsatisfied_id()
    {
        $queue = new TokenQueue;
        $queue->enqueue(new LongIdToken("unexpected"));
        $parser = new IdParser;
        $parser->append(new LongIdToken("expected"));
        
        $this->expectException(ParserException::class);
        
        $parser->parse($queue);
    }
    
    #[Test]
    public function parse_satisfied_id()
    {
        $queue = new TokenQueue;
        $parser = new IdParser;
        $id = new LongIdToken("expected");
        $queue->enqueue($id);
        $parser->append($id);
        
        $id = $parser->parse($queue);
        
        $this->assertSame("--expected", $id);
    }
    
}
