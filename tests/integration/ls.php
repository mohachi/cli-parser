<?php

use Mohachi\CommandLine\IdTokenizer\LiteralIdTokenizer;
use Mohachi\CommandLine\IdTokenizer\LongIdTokenizer;
use Mohachi\CommandLine\IdTokenizer\ShortIdTokenizer;
use Mohachi\CommandLine\Lexer;
use Mohachi\CommandLine\Parser\CommandParser;
use Mohachi\CommandLine\Parser\OptionParser;
use Mohachi\CommandLine\Token\ArgumentToken;
use Mohachi\CommandLine\Token\Id\LiteralIdToken;
use Mohachi\CommandLine\Token\Id\LongIdToken;
use Mohachi\CommandLine\Token\Id\ShortIdToken;
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
        "literal" => new LiteralIdToken("ls")
    ],
    "options" => [
        "all" => [
            "ids" => [
                "long" => new LongIdToken("--all"),
                "short" => new ShortIdToken("-a")
            ]
        ],
        "color" => [
            "ids" => [
                "long" => new LongIdToken("--color")
            ],
            "arguments" => [
                "when" => null
            ]
        ],
        "directory" => [
            "ids" => [
                "long" => new LongIdToken("--directory"),
                "short" => new ShortIdToken("-d")
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
                $queue->enqueue(new LiteralIdToken("ls"));
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
                $queue->enqueue(new LiteralIdToken("ls"));
                $queue->enqueue(new LongIdToken("all"));
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
                $queue->enqueue(new LiteralIdToken("ls"));
                $queue->enqueue(new LongIdToken("color"));
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
                $queue->enqueue(new LiteralIdToken("ls"));
                $queue->enqueue(new LongIdToken("color"));
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
                $queue->enqueue(new LiteralIdToken("ls"));
                $queue->enqueue(new LongIdToken("directory"));
                $queue->enqueue(new LongIdToken("color"));
                $queue->enqueue(new ArgumentToken("always"));
                $queue->enqueue(new LongIdToken("all"));
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

$lexer = new Lexer;
$lexer->long = new LongIdTokenizer;
$lexer->short = new ShortIdTokenizer;
$lexer->literal = new LiteralIdTokenizer;

$cmd["parser"] = new CommandParser($cmd["name"]);

foreach( $cmd["ids"] as $type => $id )
{
    $cmd["parser"]->id->append($id);
    $lexer->{$type}->append($id);
}

foreach( $cmd["options"] as $name => $option )
{
    $parser = new OptionParser($name);
    
    foreach( $option["ids"] as $type => $id )
    {
        $parser->id->append($id);
        $lexer->{$type}->append($id);
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
    $queue = $lexer->lex($example["line"]);
    TestCase::assertEquals($example["expected"]["queue"], $queue);
    
    $syntax = $cmd["parser"]->parse($queue);
    TestCase::assertEquals($example["expected"]["syntax"], $syntax);
}
