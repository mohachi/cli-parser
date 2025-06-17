<?php

use Mohachi\CommandLine\Exception\ParserException;
use Mohachi\CommandLine\Exception\UnderflowException;
use Mohachi\CommandLine\Parser\IdentifierParser;
use Mohachi\CommandLine\Token\Identifier\LiteralIdentifierToken;
use Mohachi\CommandLine\Token\Identifier\LongIdentifierToken;
use Mohachi\CommandLine\TokenQueue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(IdentifierParser::class)]
class IdentifierParserTest extends TestCase
{
    
    /* METHOD: parse */
    
    #[Test]
    public function parse_empty_queue()
    {
        $parser = new IdentifierParser;
        $parser->append(new LongIdentifierToken("cmd"));
        
        $this->expectException(UnderflowException::class);
        
        $parser->parse(new TokenQueue);
    }
    
    #[Test]
    public function parse_against_empty_id_list()
    {
        $queue = new TokenQueue;
        $queue->enqueue(new LiteralIdentifierToken("cmd"));
        
        $this->expectException(ParserException::class);
        
        (new IdentifierParser)->parse($queue);
    }
    
    #[Test]
    public function parse_unsatisfied_id()
    {
        $queue = new TokenQueue;
        $queue->enqueue(new LongIdentifierToken("unexpected"));
        $parser = new IdentifierParser;
        $parser->append(new LongIdentifierToken("expected"));
        
        $this->expectException(ParserException::class);
        
        $parser->parse($queue);
    }
    
    #[Test]
    public function parse_satisfied_id()
    {
        $queue = new TokenQueue;
        $parser = new IdentifierParser;
        $id = new LongIdentifierToken("expected");
        $queue->enqueue($id);
        $parser->append($id);
        
        $id = $parser->parse($queue);
        
        $this->assertSame("--expected", $id);
    }
    
}
