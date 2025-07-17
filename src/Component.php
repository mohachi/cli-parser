<?php

namespace Mohachi\CliParser;

use Mohachi\CliParser\Exception\InvalidArgumentException;
use Mohachi\CliParser\Exception\ParserException;
use Mohachi\CliParser\Token\ArgumentToken;
use Mohachi\CliParser\Token\IdToken;
use stdClass;

abstract class Component
{
    
    protected Lexer $lexer;
    
    /**
     * @var list<IdToken> $idTokens
     */
    private array $idTokens = [];
    
    /**
     * @var array<string,callable> $arguments
     */
    private array $arguments = [];
    
    public function id(string $name, string $value): static
    {
        $this->idTokens[] = $this->lexer->get($name)->create($value);
        
        return $this;
    }
    
    public function arg(string $name, ?callable $criterion = null): static
    {
        if( "" == $name || isset($this->arguments[$name]) )
        {
            throw new InvalidArgumentException();
        }
        
        $this->arguments[$name] = $criterion ?? fn() => true;
        
        return $this;
    }
    
    protected function parseId():  string
    {
        if( in_array($this->lexer->current(), $this->idTokens, true) )
        {
            return (string) $this->lexer->advance();
        }
        
        throw new ParserException();
    }
    
    protected function parseArguments(): stdClass
    {
        $arguments = [];
        
        foreach( $this->arguments as $name => $criterion )
        {
            if( ! $this->lexer->current() instanceof ArgumentToken )
            {
                throw new ParserException();
            }
            
            if( ! call_user_func($criterion, (string) $this->lexer->current()) )
            {
                throw new ParserException();
            }
            
            $arguments[$name] = (string) $this->lexer->advance();
        }
        
        return (object) $arguments;
    }
    
}
