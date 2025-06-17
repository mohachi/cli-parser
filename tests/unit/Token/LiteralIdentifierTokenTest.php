<?php

use Mohachi\CommandLine\Exception\InvalidArgumentException;
use Mohachi\CommandLine\Token\Identifier\LiteralIdentifierToken;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(LiteralIdentifierToken::class)]
class LiteralIdentifierTokenTest extends TestCase
{
    
    /* METHOD: __construct */
    
    #[Test]
    public function construct_empty_value()
    {
        $this->expectException(InvalidArgumentException::class);
        
        new LiteralIdentifierToken("");
    }
    
    #[Test]
    public function construct_value_begins_with_hyphen()
    {
        $this->expectException(InvalidArgumentException::class);
        
        new LiteralIdentifierToken("-");
    }
    
    /* METHOD: __toString */
    
    #[Test]
    public function cast_value_to_string()
    {
        $token = new LiteralIdentifierToken("literal");
        
        $this->assertSame("literal", (string) $token);
    }
    
}
