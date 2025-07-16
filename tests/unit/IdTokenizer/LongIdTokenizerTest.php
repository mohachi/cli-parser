<?php

use Mohachi\CliParser\Exception\InvalidArgumentException;
use Mohachi\CliParser\IdTokenizer\LongIdTokenizer;
use Mohachi\CliParser\Token\ArgumentToken;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(LongIdTokenizer::class)]
class LongIdTokenizerTest extends TestCase
{
    
    private LongIdTokenizer $tokenizer;
    
    protected function setUp(): void
    {
        $this->tokenizer = new LongIdTokenizer;
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
    public function create_usingNonPrefixedValue_returnsIdTokenWithPrefixedValue()
    {
        $token = $this->tokenizer->create("id");
        
        $this->assertEquals("--id", (string) $token);
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
    public function tokenize_nonPrefixedInput_returnsNull()
    {
        $this->tokenizer->create("id");
        
        
        $this->assertNull($this->tokenizer->tokenize("id"));
    }
    
    #[Test]
    public function tokenize_withNoTokens_returnsNull()
    {
        $tokens = $this->tokenizer->tokenize("--id");
        
        $this->assertNull($tokens);
    }
    
    #[Test]
    public function tokenize_unrecognizedInput_returnsNull()
    {
        $this->tokenizer->create("expected-1");
        $this->tokenizer->create("expected-2");
        
        $tokens = $this->tokenizer->tokenize("--unexpected");
        
        $this->assertNull($tokens);
    }
    
    #[Test]
    public function tokenize_inputStartsWithMatchingToken_returnNull()
    {
        $this->tokenizer->create("expected");
        
        $tokens = $this->tokenizer->tokenize("--expecteddd");
        
        $this->assertNull($tokens);
    }
    
    #[Test]
    public function tokenize_recognizedInput_returnsArrayContainsMatchedIdToken()
    {
        $token = $this->tokenizer->create("expected");
        
        $tokens = $this->tokenizer->tokenize("--expected");
        
        $this->assertSame([$token], $tokens);
    }
    
    #[Test]
    public function tokenize_recognizedInputWithEmptyArgument_returnsArrayContainsMatchedIdTokenAndEmptyArgumentToken()
    {
        $token = $this->tokenizer->create("expected");
        
        $tokens = $this->tokenizer->tokenize("--expected=");
        
        $this->assertCount(2, $tokens);
        $this->assertSame($token, $tokens[0]);
        $this->assertEquals(new ArgumentToken(""), $tokens[1]);
    }
    
    #[Test]
    public function tokenize_recognizedInputWithArgument_returnsArrayContainsMatchedIdTokenAndArgumentToken()
    {
        $token = $this->tokenizer->create("expected");
        
        $tokens = $this->tokenizer->tokenize("--expected=arg");
        
        $this->assertCount(2, $tokens);
        $this->assertSame($token, $tokens[0]);
        $this->assertEquals(new ArgumentToken("arg"), $tokens[1]);
    }
    
    #[Test]
    public function tokenize_recognizedInputAgainstCollapsibleMatches_returnsArrayContainsExactlyMatchedIdToken()
    {
        $this->tokenizer->create("option");
        $expected = $this->tokenizer->create("opt");
        
        $tokens = $this->tokenizer->tokenize("--opt");
        
        $this->assertSame([$expected], $tokens);
    }
    
}
