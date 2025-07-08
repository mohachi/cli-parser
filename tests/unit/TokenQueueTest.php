<?php

use Mohachi\CliParser\Exception\UnderflowException;
use Mohachi\CliParser\Token\AbstractToken;
use Mohachi\CliParser\Token\ArgumentToken;
use Mohachi\CliParser\TokenQueue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(TokenQueue::class)]
class TokenQueueTest extends TestCase
{
    
    /* METHOD: isEmpty */
    
    #[Test]
    public function is_empty_of_empty_queue()
    {
        $this->assertTrue((new TokenQueue)->isEmpty());
    }
    
    #[Test]
    public function is_empty_of_non_empty_queue()
    {
        $queue = new TokenQueue;
        $queue->enqueue($this->createStub(AbstractToken::class));
        
        $this->assertFalse($queue->isEmpty());
    }
    
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
        $token = new ArgumentToken("value");
        $queue = new TokenQueue;
        $queue->enqueue($token);
        
        $this->assertSame($token, $queue->getHead());
    }
    
    /* METHOD: dequeue */
    
    #[Test]
    public function dequeue_from_empty_queue()
    {
        $this->expectException(UnderflowException::class);
        
        (new TokenQueue)->getHead();
    }
    
    #[Test]
    public function dequeue_from_non_empty_queue()
    {
        $token = new ArgumentToken("value");
        $tokens = new TokenQueue;
        $tokens->enqueue($token);
        
        $this->assertSame($token, $tokens->getHead());
    }
    
}
