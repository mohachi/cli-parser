<?php

use Mohachi\CliParser\IdTokenizer\LiteralIdTokenizer;
use Mohachi\CliParser\IdTokenizer\LongIdTokenizer;
use Mohachi\CliParser\IdTokenizer\ShortIdTokenizer;
use Mohachi\CliParser\Token\ArgumentToken;
use Mohachi\CliParser\Token\IdToken;
use Mohachi\CliParser\TokenQueue;

$cmd = [
    "name" => "sha256sum",
    "ids" => [
        LiteralIdTokenizer::class => "sha256sum"
    ],
    "options" => [
        "binary" => [
            "ids" => [
                LongIdTokenizer::class => "binary",
                ShortIdTokenizer::class => "b",
            ],
        ],
        "check" => [
            "ids" => [
                LongIdTokenizer::class => "check",
                ShortIdTokenizer::class => "c",
            ],
        ],
        "help" => [
            "ids" => [
                LongIdTokenizer::class => "help",
            ],
        ],
        "ignore-missing" => [
            "ids" => [
                LongIdTokenizer::class => "ignore-missing",
            ],
        ],
        "quiet" => [
            "ids" => [
                LongIdTokenizer::class => "quiet",
            ],
        ],
        "status" => [
            "ids" => [
                LongIdTokenizer::class => "status",
            ],
        ],
        "strict" => [
            "ids" => [
                LongIdTokenizer::class => "strict",
            ],
        ],
        "tag" => [
            "ids" => [
                LongIdTokenizer::class => "tag",
            ],
        ],
        "text" => [
            "ids" => [
                LongIdTokenizer::class => "text",
                ShortIdTokenizer::class => "t",
            ],
        ],
        "version" => [
            "ids" => [
                LongIdTokenizer::class => "version",
            ],
        ],
        "warn" => [
            "ids" => [
                LongIdTokenizer::class => "warn",
                ShortIdTokenizer::class => "w",
            ],
        ],
        "zero" => [
            "ids" => [
                LongIdTokenizer::class => "zero",
            ],
        ],
    ],
    "arguments" => [
        "FILE" => null,
    ],
];

$examples = [
    [
        "line" => ["sha256sum", "--ignore-missing", "--check", "--quiet", "path/to/file.sha256"],
        "expected" => [
            "queue" => (function()
            {
                $queue = new TokenQueue;
                $queue->enqueue(new IdToken("sha256sum"));
                $queue->enqueue(new IdToken("--ignore-missing"));
                $queue->enqueue(new IdToken("--check"));
                $queue->enqueue(new IdToken("--quiet"));
                $queue->enqueue(new ArgumentToken("path/to/file.sha256"));
                return $queue;
            })(),
            "syntax" => (object) [
                "name" => "sha256sum",
                "id" => "sha256sum",
                "options" => [
                    (object) [
                        "name" => "ignore-missing",
                        "id" => "--ignore-missing",
                        "arguments" => (object) [],
                    ],
                    (object) [
                        "name" => "check",
                        "id" => "--check",
                        "arguments" => (object) [],
                    ],
                    (object) [
                        "name" => "quiet",
                        "id" => "--quiet",
                        "arguments" => (object) [],
                    ],
                ],
                "arguments" => (object) [
                    "FILE" => "path/to/file.sha256",
                ],
            ],
        ],
    ],
];
