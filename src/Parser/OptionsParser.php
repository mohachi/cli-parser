<?php

namespace Mohachi\CommandLine\Parser;

use Mohachi\CommandLine\Exception\InvalidArgumentException;
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
        
        if( isset($this->parsers[$parser->name]) )
        {
            throw new InvalidArgumentException("duplicate parser");
        }
        
        $this->parsers[$parser->name] = [
            "min" => $min,
            "max" => $max,
            "parser" => $parser
        ];
    }
    
    public function parse(TokenQueue $queue): array
    {
        $options = [];
        $rest = $this->parsers;
        
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
                    $options[] = $parser->parse($queue);
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
        
        return $options;
    }
    
}
