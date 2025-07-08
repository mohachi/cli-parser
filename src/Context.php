<?php

namespace Mohachi\CliParser;

use Mohachi\CliParser\Exception\InvalidArgumentException;
use Mohachi\CliParser\Exception\ParserException;
use Mohachi\CliParser\Exception\UnderflowException;

abstract class Context extends Component
{
    
    /**
     * @var object[] $options
     */
    private array $options = [];
    
    public function opt(string $name, int $min = 0, int $max = -1): Option
    {
        if( $min < 0 )
        {
            throw new InvalidArgumentException("invalid minimum value");
        }
        
        if( $max == 0 || (0 < $max && $max < $min) )
        {
            throw new InvalidArgumentException("invalid maximum value");
        }
        
        if( isset($this->options[$name]) )
        {
            throw new InvalidArgumentException("duplicate parser");
        }
        
        $this->options[$name] = [
            "min" => $min,
            "max" => $max,
            "option" => new Option($name),
        ];
        
        return $this->options[$name]["option"];
    }
    
    public function parseOptions(TokenQueue $queue): array
    {
        $options = [];
        $rest = $this->options;
        
        do
        {
            $parsed = false;
            
            /**
             * @var Option $option
             */
            foreach( $rest as $i => ["min" => &$min, "max" => &$max, "option" => $option])
            {
                try
                {
                    $options[] = $option->parse($queue);
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
