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
    "name" => "sha256sum",
    "ids" => [
        "literal" => new LiteralIdentifierToken("sha256sum")
    ],
    "options" => [
        "binary" => [
            "ids" => [
                "long" => new LongIdentifierToken("binary"),
                "short" => new ShortIdentifierToken("b")
            ]
        ],
        "check" => [
            "ids" => [
                "long" => new LongIdentifierToken("check"),
                "short" => new ShortIdentifierToken("c")
            ]
        ],
        "help" => [
            "ids" => [
                "long" => new LongIdentifierToken("help"),
            ]
        ],
        "ignore-missing" => [
            "ids" => [
                "long" => new LongIdentifierToken("ignore-missing"),
            ]
        ],
        "quiet" => [
            "ids" => [
                "long" => new LongIdentifierToken("quiet"),
            ]
        ],
        "status" => [
            "ids" => [
                "long" => new LongIdentifierToken("status"),
            ]
        ],
        "strict" => [
            "ids" => [
                "long" => new LongIdentifierToken("strict"),
            ]
        ],
        "tag" => [
            "ids" => [
                "long" => new LongIdentifierToken("tag"),
            ]
        ],
        "text" => [
            "ids" => [
                "long" => new LongIdentifierToken("text"),
                "short" => new ShortIdentifierToken("t")
            ]
        ],
        "version" => [
            "ids" => [
                "long" => new LongIdentifierToken("version"),
            ]
        ],
        "warn" => [
            "ids" => [
                "long" => new LongIdentifierToken("warn"),
                "short" => new ShortIdentifierToken("w")
            ]
        ],
        "zero" => [
            "ids" => [
                "long" => new LongIdentifierToken("zero"),
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
                $queue->enqueue(new LiteralIdentifierToken("sha256sum"));
                $queue->enqueue(new LongIdentifierToken("ignore-missing"));
                $queue->enqueue(new LongIdentifierToken("check"));
                $queue->enqueue(new LongIdentifierToken("quiet"));
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
