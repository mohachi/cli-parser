<?php

namespace Mohachi\CliParser;

use Iterator;
use Mohachi\CliParser\Exception\InvalidArgumentException;
use Mohachi\CliParser\Exception\LogicException;
use Mohachi\CliParser\Exception\OutOfBoundsException;
use Mohachi\CliParser\Exception\UnderflowException;
use Mohachi\CliParser\IdTokenizer\IdTokenizerInterface;
use Mohachi\CliParser\Token\AbstractToken;
use Mohachi\CliParser\Token\ArgumentToken;

class Lexer implements Iterator
{
    
    private int $pointer = 0;
    
    /**
     * @var list<string> $argv
     */
    private array $argv = [];
    
    /**
     * @var list<AbstractToken> $buffer
     */
    private array $buffer = [];
    
    /**
     * @var IdTokenizerInterface[] $tokenizers
     */
    private array $tokenizers = [];
    
    /**
     * @var array<string,IdTokenizerInterface> $index
     */
    private array $index = [];
    
    /**
     * Get an id tokenizer instnace by its class name.
     * 
     * @throws OutOfBoundsException if `name` is available.
     */
    function get(string $name): IdTokenizerInterface
    {
        if( ! isset($this->index[$name]) )
        {
            throw new OutOfBoundsException("Unavailable tokenizer '$name'");
        }

        return $this->index[$name];
    }
    
    /**
     * Add an id tokenizer instance.
     * 
     * @throws LogicException if a `tokenizer` of the same name has already been registered.
     */
    public function register(IdTokenizerInterface $tokenizer, string ...$names)
    {
        array_unshift($names, get_class($tokenizer));
        
        foreach( $names as $name )
        {
            if( isset($this->index[$name]) )
            {
                throw new LogicException("Tokenizer already registered");
            }
            
            $this->index[$name] = $tokenizer;
        }
        
        $this->tokenizers[] = $tokenizer;
    }
    
    /**
     * Set the command line arguments to lex
     * 
     * @throws InvalidArgumentException if `argv` is empty.
     * @throws InvalidArgumentException if `argv` is not a list.
     * @throws InvalidArgumentException if `argv` has a non string value.
     */
    public function consume(array &$argv)
    {
        if( empty($argv) )
        {
            throw new InvalidArgumentException("Empty argv");
        }
        
        for( $i = 0; $i < count($argv); $i++ )
        {
            if( ! isset($argv[$i]) )
            {
                throw new InvalidArgumentException("`argv` is not a list");
            }
            
            if( ! is_string($argv[$i]) )
            {
                throw new InvalidArgumentException("Non string value at argv[$i]");
            }
        }
        
        $this->argv = &$argv;
        $this->rewind();
    }
    
    /**
     * Reset `argv`'s internal pointer then lex.
     */
    public function rewind(): void
    {
        reset($this->argv);
        $this->pointer = 0;
        $this->assert();
        $this->lex();
    }
    
    /**
     * Check for a current token.
     */
    public function valid(): bool
    {
        return null !== key($this->argv);
    }
    
    /**
     * Return the current token.
     * 
     * @throws UnderflowException if no token remains.
     */
    public function current(): AbstractToken
    {
        if( null !== key($this->buffer) )
        {
            return current($this->buffer);
        }
        
        $this->assert();
        return new ArgumentToken(current($this->argv));
    }
    
    /**
     * Return the current pointer key.
     * 
     * @throws UnderflowException if no token remains.
     */
    public function key(): int
    {
        $this->assert();
        return $this->pointer;
    }
    
    /**
     * Lex next token.
     * 
     * @throws UnderflowException if no token remains before lexing.
     */
    public function next(): void
    {
        if(  null !== key($this->buffer) )
        {
            if( false !== next($this->buffer) )
            {
                $this->pointer++;
                return;
            }
        }
        
        $this->assert();
        
        if( false !== next($this->argv) )
        {
            $this->lex();
            $this->pointer++;
        }
    }
    
    /**
     * Get current token than lex next one.
     */
    public function advance(): AbstractToken
    {
        $tok = $this->current();
        $this->next();
        return $tok;
    }
    
    private function assert()
    {
        if( ! $this->valid() )
        {
            throw new UnderflowException("No more tokens available");
        }
    }
    
    private function lex()
    {
        foreach( $this->tokenizers as $tokenizer )
        {
            $tokens = $tokenizer->tokenize(current($this->argv));
            
            if( ! empty($tokens) )
            {
                $this->buffer = $tokens;
            }
        }
    }
    
}
