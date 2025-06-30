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
    "name" => "sha256sum",
    "ids" => [
        "literal" => new LiteralIdToken("sha256sum")
    ],
    "options" => [
        "binary" => [
            "ids" => [
                "long" => new LongIdToken("binary"),
                "short" => new ShortIdToken("b")
            ]
        ],
        "check" => [
            "ids" => [
                "long" => new LongIdToken("check"),
                "short" => new ShortIdToken("c")
            ]
        ],
        "help" => [
            "ids" => [
                "long" => new LongIdToken("help"),
            ]
        ],
        "ignore-missing" => [
            "ids" => [
                "long" => new LongIdToken("ignore-missing"),
            ]
        ],
        "quiet" => [
            "ids" => [
                "long" => new LongIdToken("quiet"),
            ]
        ],
        "status" => [
            "ids" => [
                "long" => new LongIdToken("status"),
            ]
        ],
        "strict" => [
            "ids" => [
                "long" => new LongIdToken("strict"),
            ]
        ],
        "tag" => [
            "ids" => [
                "long" => new LongIdToken("tag"),
            ]
        ],
        "text" => [
            "ids" => [
                "long" => new LongIdToken("text"),
                "short" => new ShortIdToken("t")
            ]
        ],
        "version" => [
            "ids" => [
                "long" => new LongIdToken("version"),
            ]
        ],
        "warn" => [
            "ids" => [
                "long" => new LongIdToken("warn"),
                "short" => new ShortIdToken("w")
            ]
        ],
        "zero" => [
            "ids" => [
                "long" => new LongIdToken("zero"),
            ]
        ],
    ],
    "arguments" => [
        "FILE" => null
    ]
];

$examples = [
    [
        "line" => ["sha256sum", "--ignore-missing", "--check", "--quiet", "path/to/file.sha256"],
        "expected" => [
            "queue" => (function()
            {
                $queue = new TokenQueue;
                $queue->enqueue(new LiteralIdToken("sha256sum"));
                $queue->enqueue(new LongIdToken("ignore-missing"));
                $queue->enqueue(new LongIdToken("check"));
                $queue->enqueue(new LongIdToken("quiet"));
                $queue->enqueue(new ArgumentToken("path/to/file.sha256"));
                return $queue;
            })(),
            "syntax" => (function()
            {
                return (object) [
                    "name" => "sha256sum",
                    "id" => "sha256sum",
                    "options" => [
                        (object) [
                            "name" => "ignore-missing",
                            "id" => "--ignore-missing",
                            "arguments" => (object) []
                        ],
                        (object) [
                            "name" => "check",
                            "id" => "--check",
                            "arguments" => (object) []
                        ],
                        (object) [
                            "name" => "quiet",
                            "id" => "--quiet",
                            "arguments" => (object) []
                        ],
                    ],
                    "arguments" => (object) [
                        "FILE" => "path/to/file.sha256"
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
