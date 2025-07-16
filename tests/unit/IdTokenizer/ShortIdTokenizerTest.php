<?php

use Mohachi\CliParser\IdTokenizer\ShortIdTokenizer;
use Mohachi\CliParser\Token\ArgumentToken;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(ShortIdTokenizer::class)]
class ShortIdTokenizerTest extends TestCase
{
    
    private ShortIdTokenizer $tokenizer;
    
    protected function setUp(): void
    {
        $this->tokenizer = new ShortIdTokenizer;
    }
    
    #[Test]
    public function create_usingEmptyValue_throwsInvalidArgumentException()
    {
        $this->expectException(InvalidArgumentException::class);
        
        $this->tokenizer->create("");
    }
    
    #[Test]
    public function create_usingValueEqualsToDash_throwsInvalidArgumentException()
    {
        $this->expectException(InvalidArgumentException::class);
        
        $this->tokenizer->create("-");
    }
    
    #[Test]
    public function create_usingValueEqualsToDoubleDash_throwsInvalidArgumentException()
    {
        $this->expectException(InvalidArgumentException::class);
        
        $this->tokenizer->create("--");
    }
    
    #[Test]
    public function create_usingValueEqualsDashThenEqual_throwsInvalidArgumentException()
    {
        $this->expectException(InvalidArgumentException::class);
        
        $this->tokenizer->create("-=");
    }
    
    #[Test]
    public function create_usingValueOfMoreThanOneChar__throwsInvalidArgumentException()
    {
        $this->expectException(InvalidArgumentException::class);
        
        $this->tokenizer->create("-id");
    }
    
    #[Test]
    public function create_usingNonPrefixedValue_returnsIdTokenWithPrefixedValue()
    {
        $token = $this->tokenizer->create("i");
        
        $this->assertEquals("-i", (string) $token);
    }
    
    #[Test]
    public function create_usingAlreadyCreatedId_returnsSameToken()
    {
        $token = $this->tokenizer->create("i");
        
        $this->assertSame($token, $this->tokenizer->create("i"));
    }
    
    #[Test]
    public function tokenize_withNoTokens_returnsNull()
    {
        $tokens = $this->tokenizer->tokenize("-i");
        
        $this->assertNull($tokens);
    }
    
    #[Test]
    public function tokenize_recongnizedInvalidInputs_returnsNull()
    {
        $this->tokenizer->create("i");
        
        $this->assertNull($this->tokenizer->tokenize(""));
        $this->assertNull($this->tokenizer->tokenize("-"));
        $this->assertNull($this->tokenizer->tokenize("--"));
        $this->assertNull($this->tokenizer->tokenize("-="));
    }
    
    #[Test]
    public function tokenize_unrecognizedInput_returnsNull()
    {
        $this->tokenizer->create("i");
        $this->tokenizer->create("d");
        
        $this->assertNull($this->tokenizer->tokenize("-h"));
    }
    
    #[Test]
    public function tokenize_inputOfMultipleRecognizableTokens_retunsIdTokensArray()
    {
        $i = $this->tokenizer->create("i");
        $d = $this->tokenizer->create("d");
        
        $tokens = $this->tokenizer->tokenize("-didi");
        
        $this->assertSame([$d, $i, $d, $i], $tokens);
    }
    
    #[Test]
    public function tokenize_inputAppendedWithNonRecognizableToken_returnsArrayOfIdTokensThenArgumentToken()
    {
        $i = $this->tokenizer->create("i");
        $d = $this->tokenizer->create("d");
        
        $tokens = $this->tokenizer->tokenize("-ida");
        
        $this->assertCount(3, $tokens);
        $this->assertSame($i, $tokens[0]);
        $this->assertSame($d, $tokens[1]);
        $this->assertEquals(new ArgumentToken("a"), $tokens[2]);
    }
    
    #[Test]
    public function tokenize_inputSuffixedWithExplicitArgument_returnsArrayOfIdTokensThenArgumentToken()
    {
        $i = $this->tokenizer->create("i");
        $d = $this->tokenizer->create("d");
        
        $tokens = $this->tokenizer->tokenize("-id=arg");
        
        $this->assertCount(3, $tokens);
        $this->assertSame($i, $tokens[0]);
        $this->assertSame($d, $tokens[1]);
        $this->assertEquals(new ArgumentToken("arg"), $tokens[2]);
    }
    
    #[Test]
    public function tokenize_inputContainsUnrecognizableToken_returnsArrayOfRecognizedToeknsThenArgumentToken()
    {
        $i = $this->tokenizer->create("i");
        $this->tokenizer->create("d");
        
        $tokens = $this->tokenizer->tokenize("-iad");
        
        $this->assertCount(2, $tokens);
        $this->assertSame($i, $tokens[0]);
        $this->assertEquals(new ArgumentToken("ad"), $tokens[1]);
    }
    
}
