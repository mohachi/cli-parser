<?php

use Mohachi\CliParser\Exception\InvalidArgumentException;
use Mohachi\CliParser\Token\Id\LiteralIdToken;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(LiteralIdToken::class)]
class LiteralIdTokenTest extends TestCase
{
    
    /* METHOD: __construct */
    
    #[Test]
    public function construct_empty_value()
    {
        $this->expectException(InvalidArgumentException::class);
        
        new LiteralIdToken("");
    }
    
    #[Test]
    public function construct_value_begins_with_hyphen()
    {
        $this->expectException(InvalidArgumentException::class);
        
        new LiteralIdToken("-");
    }
    
    /* METHOD: __toString */
    
    #[Test]
    public function cast_value_to_string()
    {
        $token = new LiteralIdToken("literal");
        
        $this->assertSame("literal", (string) $token);
    }
    
}
