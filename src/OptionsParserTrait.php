<?php

namespace Mohachi\CliParser;

use Mohachi\CliParser\Exception\InvalidArgumentException;
use Mohachi\CliParser\Exception\ParserException;
use Mohachi\CliParser\Exception\UnderflowException;
use Mohachi\CliParser\TokenQueue;

trait OptionsParserTrait
{
    
    /**
     * @var object[] $parsers
     */
    private array $parsers = [];
    
    public function opt(Option $parser, int $min = 0, int $max = -1)
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
    
    public function parseOptions(TokenQueue $queue): array
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
