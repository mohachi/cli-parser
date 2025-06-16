<?php

use Mohachi\CommandLine\Token\Identifier\ShortIdentifierToken;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(ShortIdentifierToken::class)]
class ShortIdentifierTokenTest extends TestCase
{
    
    /* METHOD: __construct */
    
    #[Test]
    public function construct_empty_value()
    {
        $this->expectException(InvalidArgumentException::class);
        
        new ShortIdentifierToken("");
    }
    
    #[Test]
    public function construct_value_of_length_more_than_one()
    {
        $this->expectException(InvalidArgumentException::class);
        
        new ShortIdentifierToken("short");
    }
    
    #[Test]
    public function construct_value_of_one_hyphen()
    {
        $this->expectException(InvalidArgumentException::class);
        
        new ShortIdentifierToken("-");
    }
    
    #[Test]
    public function construct_value_begins_with_one_hyphens()
    {
        $token = new ShortIdentifierToken("-e");
        
        $this->assertSame("e", $token->value);
    }
    
    /* METHOD: __toString */
    
    #[Test]
    public function case_value_to_string()
    {
        $token = new ShortIdentifierToken("e");
        
        $this->assertSame("-e", (string) $token);
    }
    
}
