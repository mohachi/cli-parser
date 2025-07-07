<?php

use Mohachi\CliParser\Token\Id\ShortIdToken;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(ShortIdToken::class)]
class ShortIdTokenTest extends TestCase
{
    
    /* METHOD: __construct */
    
    #[Test]
    public function construct_empty_value()
    {
        $this->expectException(InvalidArgumentException::class);
        
        new ShortIdToken("");
    }
    
    #[Test]
    public function construct_value_of_length_more_than_one()
    {
        $this->expectException(InvalidArgumentException::class);
        
        new ShortIdToken("short");
    }
    
    #[Test]
    public function construct_value_of_one_hyphen()
    {
        $this->expectException(InvalidArgumentException::class);
        
        new ShortIdToken("-");
    }
    
    #[Test]
    public function construct_value_begins_with_one_hyphens()
    {
        $token = new ShortIdToken("-e");
        
        $this->assertSame("e", $token->value);
    }
    
    /* METHOD: __toString */
    
    #[Test]
    public function case_value_to_string()
    {
        $token = new ShortIdToken("e");
        
        $this->assertSame("-e", (string) $token);
    }
    
}
