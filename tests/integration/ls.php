<?php

use Mohachi\CliParser\IdTokenizer\LiteralIdTokenizer;
use Mohachi\CliParser\IdTokenizer\LongIdTokenizer;
use Mohachi\CliParser\IdTokenizer\ShortIdTokenizer;
use Mohachi\CliParser\Token\ArgumentToken;
use Mohachi\CliParser\Token\IdToken;
use Mohachi\CliParser\TokenQueue;

$cmd = [
    "name" => "ls",
    "ids" => [
        LiteralIdTokenizer::class => "ls",
    ],
    "options" => [
        "all" => [
            "ids" => [
                LongIdTokenizer::class => "--all",
                ShortIdTokenizer::class => "-a",
            ],
        ],
        "color" => [
            "ids" => [
                LongIdTokenizer::class => "--color",
            ],
            "arguments" => [
                "when" => null,
            ],
        ],
        "directory" => [
            "ids" => [
                LongIdTokenizer::class => "--directory",
                ShortIdTokenizer::class => "-d",
            ],
        ],
    ],
    "arguments" => [
        "target" => null,
    ],
];

$examples = [
    [
        "line" => ["ls", "."],
        "expected" => [
            "queue" => (function()
            {
                $queue = new TokenQueue;
                $queue->enqueue(new IdToken("ls"));
                $queue->enqueue(new ArgumentToken("."));
                return $queue;
            })(),
            "syntax" => (object) [
                "name" => "ls",
                "id" => "ls",
                "options" => [],
                "arguments" => (object) [
                    "target" => ".",
                ],
            ],
        ],
    ],
    
    [
        "line" => ["ls", "--all", "."],
        "expected" => [
            "queue" => (function()
            {
                $queue = new TokenQueue;
                $queue->enqueue(new IdToken("ls"));
                $queue->enqueue(new IdToken("--all"));
                $queue->enqueue(new ArgumentToken("."));
                return $queue;
            })(),
            "syntax" => (object) [
                "name" => "ls",
                "id" => "ls",
                "options" => [
                    (object) [
                        "name" => "all",
                        "id" => "--all",
                        "arguments" => (object) [],
                    ],
                ],
                "arguments" => (object) [
                    "target" => ".",
                ],
            ],
        ],
    ],
    
    [
        "line" => ["ls", "--color", "never", "/"],
        "expected" => [
            "queue" => (function()
            {
                $queue = new TokenQueue;
                $queue->enqueue(new IdToken("ls"));
                $queue->enqueue(new IdToken("--color"));
                $queue->enqueue(new ArgumentToken("never"));
                $queue->enqueue(new ArgumentToken("/"));
                return $queue;
            })(),
            "syntax" => (object) [
                "name" => "ls",
                "id" => "ls",
                "options" => [
                    (object) [
                        "name" => "color",
                        "id" => "--color",
                        "arguments" => (object) [
                            "when" => "never",
                        ],
                    ],
                ],
                "arguments" => (object) [
                    "target" => "/",
                ],
            ],
        ],
    ],
    
    [
        "line" => ["ls", "--color=always", "~/Documents"],
        "expected" => [
            "queue" => (function()
            {
                $queue = new TokenQueue;
                $queue->enqueue(new IdToken("ls"));
                $queue->enqueue(new IdToken("--color"));
                $queue->enqueue(new ArgumentToken("always"));
                $queue->enqueue(new ArgumentToken("~/Documents"));
                return $queue;
            })(),
            "syntax" => (object) [
                "name" => "ls",
                "id" => "ls",
                "options" => [
                    (object) [
                        "name" => "color",
                        "id" => "--color",
                        "arguments" => (object) [
                            "when" => "always",
                        ],
                    ],
                ],
                "arguments" => (object) [
                    "target" => "~/Documents",
                ],
            ],
        ],
    ],
    
    [
        "line" => ["ls", "--directory", "--color=always", "--all", "/var"],
        "expected" => [
            "queue" => (function()
            {
                $queue = new TokenQueue;
                $queue->enqueue(new IdToken("ls"));
                $queue->enqueue(new IdToken("--directory"));
                $queue->enqueue(new IdToken("--color"));
                $queue->enqueue(new ArgumentToken("always"));
                $queue->enqueue(new IdToken("--all"));
                $queue->enqueue(new ArgumentToken("/var"));
                return $queue;
            })(),
            "syntax" => (object) [
                "name" => "ls",
                "id" => "ls",
                "options" => [
                    (object) [
                        "name" => "directory",
                        "id" => "--directory",
                        "arguments" => (object) [],
                    ],
                    (object) [
                        "name" => "color",
                        "id" => "--color",
                        "arguments" => (object) [
                            "when" => "always",
                        ],
                    ],
                    (object) [
                        "name" => "all",
                        "id" => "--all",
                        "arguments" => (object) [],
                    ],
                ],
                "arguments" => (object) [
                    "target" => "/var",
                ],
            ],
        ],
    ],
];
