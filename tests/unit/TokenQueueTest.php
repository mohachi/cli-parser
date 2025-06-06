<?php

use Mohachi\CommandLine\Exception\UnderflowException;
use Mohachi\CommandLine\SyntaxTree\ArgumentNode;
use Mohachi\CommandLine\TokenQueue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(TokenQueue::class)]
class TokenQueueTest extends TestCase
{
    
    /* METHOD: getHead */
    
    #[Test]
    public function get_head_of_empty_queue()
    {
        $this->expectException(UnderflowException::class);
        
        (new TokenQueue)->getHead();
    }
    
    #[Test]
    public function get_head_of_non_empty_queue()
    {
        $arg = new ArgumentNode("value");
        $tokens = new TokenQueue;
        $tokens->push($arg);
        
        $this->assertSame($arg, $tokens->getHead());
    }
    
    /* METHOD: pull */
    
    #[Test]
    public function pull_from_empty_queue()
    {
        $this->expectException(UnderflowException::class);
        
        (new TokenQueue)->getHead();
    }
    
    #[Test]
    public function pull_from_non_empty_queue()
    {
        $token = new ArgumentNode("value");
        $tokens = new TokenQueue;
        $tokens->push($token);
        
        $this->assertSame($token, $tokens->getHead());
    }
    
}
