<?php

use Mohachi\CliParser\Exception\InvalidArgumentException;
use Mohachi\CliParser\IdTokenizer\LiteralIdTokenizer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(LiteralIdTokenizer::class)]
class LiteralIdTokenizerTest extends TestCase
{
    
    private LiteralIdTokenizer $tokenizer;
    
    protected function setUp(): void
    {
        $this->tokenizer = new LiteralIdTokenizer;
    }
    
    #[Test]
    public function create_usingEmptyValue_throwsInvalidArgumentException()
    {
        $this->expectException(InvalidArgumentException::class);
        
        $this->tokenizer->create("");
    }
    
    #[Test]
    public function create_usingNonAlphabeticallyPrefixedValue_throwsInvalidArgumentException()
    {
        $this->expectException(InvalidArgumentException::class);
        
        $this->tokenizer->create("-id");
    }
    
    #[Test]
    public function create_usingAlreadyCreatedId_returnsTheSameToken()
    {
        $token = $this->tokenizer->create("id");
        
        $this->assertSame($token, $this->tokenizer->create("id"));
    }
    
    #[Test]
    public function tokenize_emptyInput_returnsNull()
    {
        $this->tokenizer->create("id");
        
        $this->assertNull($this->tokenizer->tokenize(""));
    }
    
    #[Test]
    public function tokenize_withNoTokens_returnsNull()
    {
        $tokens = $this->tokenizer->tokenize("id");
        
        $this->assertNull($tokens);
    }
    
    #[Test]
    public function tokenize_unrecognizedInput_returnsNull()
    {
        $this->tokenizer->create("expected");
        
        $tokens = $this->tokenizer->tokenize("unexpected");
        
        $this->assertNull($tokens);
    }
    
    #[Test]
    public function tokenize_recognizedInput_returnsArrayContainsMatchedIdToken()
    {
        $token = $this->tokenizer->create("expected");
        
        $tokens = $this->tokenizer->tokenize("expected");
        
        $this->assertSame([$token], $tokens);
    }
    
}
