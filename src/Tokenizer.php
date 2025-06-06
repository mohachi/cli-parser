<?php

namespace Mohachi\CommandLine;

use Mohachi\CommandLine\Exception\DomainException;
use Mohachi\CommandLine\Exception\InvalidArgumentException;
use Mohachi\CommandLine\SyntaxTree\ArgumentNode;
use Mohachi\CommandLine\SyntaxTree\Identifier\AbstractIdentifierNode;
use Mohachi\CommandLine\SyntaxTree\Identifier\LiteralIdentifierNode;
use Mohachi\CommandLine\SyntaxTree\Identifier\LongIdentifierNode;

class Tokenizer
{
    
    /**
     * @var LongIdentifierNode[] $long
     */
    private array $long = [];
    
    /**
     * @var LiteralIdentifierNode[] $literal
     */
    private array $literal = [];
    
    public function appendIdentifier(AbstractIdentifierNode $id)
    {
        match( get_class($id) )
        {
            LongIdentifierNode::class => $this->long[] = $id,
            LiteralIdentifierNode::class => $this->literal[] = $id,
            default => throw new DomainException()
        };
    }
    
    public function tokenize(array &$subjects): TokenQueue
    {
        switch( true )
        {
            case empty($subjects):
            case ! array_is_list($subjects):
            case ! array_all($subjects, fn($v) => is_string($v)):
                throw new InvalidArgumentException();
        }
        
        $tokens = new TokenQueue;
        $subject = current($subjects);
        
        while( false !== $subject )
        {
            $nodes = $this->tokenizeLiteral($subject);
            $nodes ??= $this->tokenizeLong($subject);
            $nodes ??= $this->tokenizeArgument($subject);
            
            foreach( $nodes as $node )
            {
                $tokens->push($node);
            }
            
            $subject = next($subjects);
        }
        
        return $tokens;
    }
    
    private function tokenizeLiteral(string $subject): ?array
    {
        if( str_starts_with($subject, "-") )
        {
            return null;
        }
        
        return $this->tokenizeStatic("literal", $subject);
    }
    
    private function tokenizeLong(string $subject): ?array
    {
        if( ! str_starts_with($subject, "--") )
        {
            return null;
        }
        
        return $this->tokenizeStatic("long", $subject);
    }
    
    private function tokenizeStatic(string $from, string $subject): ?array
    {
        $id = null;
        
        /**
         * @var AbstractIdentifierNode $id
         */
        foreach( $this->{$from} as $id )
        {
            if( str_starts_with($subject, $id->getValue()) )
            {
                $subject = substr($subject, strlen($id->getValue()));
                break;
            }
        }
        
        if( $subject == "" )
        {
            return [$id];
        }
        elseif( $subject[0] != "=" )
        {
            return null;
        }
        
        return [$id, ...$this->tokenizeArgument(substr($subject, 1))];
    }
    
    private function tokenizeArgument(string $subject): array
    {
        $node = new ArgumentNode;
        $node->setValue($subject);
        return [$node];
    }
    
}
