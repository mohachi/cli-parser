<?php

use Mohachi\CommandLine\Token\Identifier\LongIdentifierToken;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(LongIdentifierToken::class)]
class LongIdentifierTokenTest extends TestCase
{
    
    /* METHOD: __construct */
    
    #[Test]
    public function construct_empty_value()
    {
        $this->expectException(InvalidArgumentException::class);
        
        new LongIdentifierToken("");
    }
    
    #[Test]
    public function construct_value_of_two_hyphens()
    {
        $this->expectException(InvalidArgumentException::class);
        
        new LongIdentifierToken("--");
    }
    
    #[Test]
    public function construct_value_begins_with_two_hyphens()
    {
        $token = new LongIdentifierToken("--expected");
        
        $this->assertSame("expected", $token->value);
    }
    
    /* METHOD: __toString */
    
    #[Test]
    public function case_value_to_string()
    {
        $token = new LongIdentifierToken("expected");
        
        $this->assertSame("--expected", (string) $token);
    }
    
}
