<?php

use Mohachi\CommandLine\IdentifierTokenizer\LiteralIdentifierTokenizer;
use Mohachi\CommandLine\IdentifierTokenizer\LongIdentifierTokenizer;
use Mohachi\CommandLine\IdentifierTokenizer\ShortIdentifierTokenizer;
use Mohachi\CommandLine\Normalizer;
use Mohachi\CommandLine\Parser\CommandParser;
use Mohachi\CommandLine\Parser\OptionParser;
use Mohachi\CommandLine\Token\ArgumentToken;
use Mohachi\CommandLine\Token\Identifier\LiteralIdentifierToken;
use Mohachi\CommandLine\Token\Identifier\LongIdentifierToken;
use Mohachi\CommandLine\Token\Identifier\ShortIdentifierToken;
use Mohachi\CommandLine\TokenQueue;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . "/../../vendor/autoload.php";

/* Index
    1. Declaration - the declaration of the command and examples.
    2. Automation - automatic creation of tokenizers and parsers.
    3. Run test - automatic run of example tests.
*/

/* Declaration */

$cmd = [
    "name" => "ls",
    "ids" => [
        "literal" => new LiteralIdentifierToken("ls")
    ],
    "options" => [
        "all" => [
            "ids" => [
                "long" => new LongIdentifierToken("--all"),
                "short" => new ShortIdentifierToken("-a")
            ]
        ],
        "color" => [
            "ids" => [
                "long" => new LongIdentifierToken("--color")
            ],
            "arguments" => [
                "when" => null
            ]
        ],
        "directory" => [
            "ids" => [
                "long" => new LongIdentifierToken("--directory"),
                "short" => new ShortIdentifierToken("-d")
            ]
        ],
    ],
    "arguments" => [
        "target" => null
    ]
];

$examples = [
    [
        "line" => ["ls", "."],
        "expected" => [
            "queue" => (function()
            {
                $queue = new TokenQueue;
                $queue->enqueue(new LiteralIdentifierToken("ls"));
                $queue->enqueue(new ArgumentToken("."));
                return $queue;
            })(),
            "syntax" => (function()
            {
                return (object) [
                    "name" => "ls",
                    "id" => "ls",
                    "options" => [],
                    "arguments" => (object) [
                        "target" => "."
                    ]
                ];
            })()
        ]
    ],
    
    [
        "line" => ["ls", "--all", "."],
        "expected" => [
            "queue" => (function()
            {
                $queue = new TokenQueue;
                $queue->enqueue(new LiteralIdentifierToken("ls"));
                $queue->enqueue(new LongIdentifierToken("all"));
                $queue->enqueue(new ArgumentToken("."));
                return $queue;
            })(),
            "syntax" => (function()
            {
                return (object) [
                    "name" => "ls",
                    "id" => "ls",
                    "options" => [
                        (object) [
                            "name" => "all",
                            "id" => "--all",
                            "arguments" => (object) []
                        ]
                    ],
                    "arguments" => (object) [
                        "target" => "."
                    ]
                ];
            })()
        ]
    ],
    
    [
        "line" => ["ls", "--color", "never", "/"],
        "expected" => [
            "queue" => (function()
            {
                $queue = new TokenQueue;
                $queue->enqueue(new LiteralIdentifierToken("ls"));
                $queue->enqueue(new LongIdentifierToken("color"));
                $queue->enqueue(new ArgumentToken("never"));
                $queue->enqueue(new ArgumentToken("/"));
                return $queue;
            })(),
            "syntax" => (function()
            {
                return (object) [
                    "name" => "ls",
                    "id" => "ls",
                    "options" => [
                        (object) [
                            "name" => "color",
                            "id" => "--color",
                            "arguments" => (object) [
                                "when" => "never"
                            ]
                        ]
                    ],
                    "arguments" => (object) [
                        "target" => "/"
                    ]
                ];
            })()
        ]
    ],
    
    [
        "line" => ["ls", "--color=always", "~/Documents"],
        "expected" => [
            "queue" => (function()
            {
                $queue = new TokenQueue;
                $queue->enqueue(new LiteralIdentifierToken("ls"));
                $queue->enqueue(new LongIdentifierToken("color"));
                $queue->enqueue(new ArgumentToken("always"));
                $queue->enqueue(new ArgumentToken("~/Documents"));
                return $queue;
            })(),
            "syntax" => (function()
            {
                return (object) [
                    "name" => "ls",
                    "id" => "ls",
                    "options" => [
                        (object) [
                            "name" => "color",
                            "id" => "--color",
                            "arguments" => (object) [
                                "when" => "always"
                            ]
                        ]
                    ],
                    "arguments" => (object) [
                        "target" => "~/Documents"
                    ]
                ];
            })()
        ]
    ],
    
    [
        "line" => ["ls", "--directory", "--color=always", "--all", "/var"],
        "expected" => [
            "queue" => (function()
            {
                $queue = new TokenQueue;
                $queue->enqueue(new LiteralIdentifierToken("ls"));
                $queue->enqueue(new LongIdentifierToken("directory"));
                $queue->enqueue(new LongIdentifierToken("color"));
                $queue->enqueue(new ArgumentToken("always"));
                $queue->enqueue(new LongIdentifierToken("all"));
                $queue->enqueue(new ArgumentToken("/var"));
                return $queue;
            })(),
            "syntax" => (function()
            {
                return (object) [
                    "name" => "ls",
                    "id" => "ls",
                    "options" => [
                        (object) [
                            "name" => "directory",
                            "id" => "--directory",
                            "arguments" => (object) []
                        ],
                        (object) [
                            "name" => "color",
                            "id" => "--color",
                            "arguments" => (object) [
                                "when" => "always"
                            ]
                        ],
                        (object) [
                            "name" => "all",
                            "id" => "--all",
                            "arguments" => (object) []
                        ],
                    ],
                    "arguments" => (object) [
                        "target" => "/var"
                    ]
                ];
            })()
        ]
    ],
    
];

/* Automation */

$normalizer = new Normalizer;
$normalizer->long = new LongIdentifierTokenizer;
$normalizer->short = new ShortIdentifierTokenizer;
$normalizer->literal = new LiteralIdentifierTokenizer;

$cmd["parser"] = new CommandParser($cmd["name"]);

foreach( $cmd["ids"] as $type => $id )
{
    $cmd["parser"]->id->append($id);
    $normalizer->{$type}->append($id);
}

foreach( $cmd["options"] as $name => $option )
{
    $parser = new OptionParser($name);
    
    foreach( $option["ids"] as $type => $id )
    {
        $parser->id->append($id);
        $normalizer->{$type}->append($id);
    }
    
    if( isset($option["arguments"]) )
    {
        foreach( $option["arguments"] as $name => $criterion )
        {
            $parser->arguments->append($name, $criterion);
        }
    }
    
    $cmd["parser"]->options->append($parser);
}

foreach( $cmd["arguments"] as $name => $criterion )
{
    $cmd["parser"]->arguments->append($name, $criterion);
}

/* Run tests */

foreach( $examples as $i => $example )
{
    $queue = $normalizer->normalize($example["line"]);
    TestCase::assertEquals($example["expected"]["queue"], $queue);
    
    $syntax = $cmd["parser"]->parse($queue);
    TestCase::assertEquals($example["expected"]["syntax"], $syntax);
}
