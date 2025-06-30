<?php

use Mohachi\CommandLine\Token\ArgumentToken;
use Mohachi\CommandLine\Token\Id\LiteralIdToken;
use Mohachi\CommandLine\Token\Id\LongIdToken;
use Mohachi\CommandLine\Token\Id\ShortIdToken;
use Mohachi\CommandLine\TokenQueue;

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
