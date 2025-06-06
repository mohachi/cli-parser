<?php

namespace Mohachi\CommandLine\Parser;

use Mohachi\CommandLine\Exception\InvalidArgumentException;
use Mohachi\CommandLine\Exception\ParserException;
use Mohachi\CommandLine\SyntaxTree\ArgumentNode;
use Mohachi\CommandLine\SyntaxTree\ArgumentsNode;
use Mohachi\CommandLine\TokenQueue;

class ArgumentsParser implements ParserInterface
{
    
    private array $criteria = [];
    
    public function append(string $name, ?callable $criterion = null)
    {
        if( "" == $name || isset($this->criteria[$name]) )
        {
            throw new InvalidArgumentException();
        }
        
        $this->criteria[$name] = $criterion ?? fn() => true;
    }
    
    public function parse(TokenQueue $tokens): ArgumentsNode
    {
        $node = new ArgumentsNode;
        
        foreach( $this->criteria as $name => $criterion )
        {
            if( ! $tokens->getHead() instanceof ArgumentNode )
            {
                throw new ParserException();
            }
            
            if( ! call_user_func($criterion, $tokens->getHead()->getValue()) )
            {
                throw new ParserException();
            }
            
            $node->append($name, $tokens->pull());
        }
        
        return $node;
    }
    
}
