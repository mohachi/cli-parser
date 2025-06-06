<?php

namespace Mohachi\CommandLine\Parser;

use Mohachi\CommandLine\Exception\InvalidArgumentException;
use Mohachi\CommandLine\SyntaxTree\OptionsNode;
use Mohachi\CommandLine\Exception\ParserException;
use Mohachi\CommandLine\Exception\UnderflowException;
use Mohachi\CommandLine\TokenQueue;

class OptionsParser implements ParserInterface
{
    
    /**
     * @var object[] $parsers
     */
    private array $parsers = [];
    
    public function append(OptionParser $parser, int $min = 0, int $max = -1)
    {
        if( $min < 0 )
        {
            throw new InvalidArgumentException("invalid minimum value");
        }
        
        if( $max == 0 || (0 < $max && $max < $min) )
        {
            throw new InvalidArgumentException("invalid maximum value");
        }
        
        $this->parsers[] = [
            "min" => $min,
            "max" => $max,
            "parser" => $parser
        ];
    }
    
    public function parse(TokenQueue $tokens): OptionsNode
    {
        $rest = $this->parsers;
        $node = new OptionsNode;
        
        do
        {
            $parsed = false;
            
            /**
             * @var OptionParser $parser
             */
            foreach( $rest as $i => ["min" => &$min, "max" => &$max, "parser" => $parser])
            {
                try
                {
                    $node->append($parser->parse($tokens));
                    $min--;
                    $max--;
                    $parsed = true;
                }
                catch( UnderflowException $e )
                {
                    if( 0 < $min )
                    {
                        throw $e;
                    }
                }
                catch( ParserException )
                {
                    continue;
                }
                
                if( 0 == $max )
                {
                    unset($rest[$i]);
                }
            }
        }
        while( $parsed );
        
        foreach( $rest as ["min" => $min] )
        {
            if( 0 < $min )
            {
                throw new ParserException();
            }
        }
        
        return $node;
    }
    
}
