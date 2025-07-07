<?php

use Mohachi\CliParser\Token\Id\LongIdToken;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(LongIdToken::class)]
class LongIdTokenTest extends TestCase
{
    
    /* METHOD: __construct */
    
    #[Test]
    public function construct_empty_value()
    {
        $this->expectException(InvalidArgumentException::class);
        
        new LongIdToken("");
    }
    
    #[Test]
    public function construct_value_of_two_hyphens()
    {
        $this->expectException(InvalidArgumentException::class);
        
        new LongIdToken("--");
    }
    
    #[Test]
    public function construct_value_begins_with_two_hyphens()
    {
        $token = new LongIdToken("--expected");
        
        $this->assertSame("expected", $token->value);
    }
    
    /* METHOD: __toString */
    
    #[Test]
    public function case_value_to_string()
    {
        $token = new LongIdToken("expected");
        
        $this->assertSame("--expected", (string) $token);
    }
    
}
