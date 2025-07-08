<?php

use Mohachi\CliParser\Token\ArgumentToken;
use Mohachi\CliParser\Token\IdToken;
use Mohachi\CliParser\TokenQueue;

$cmd = [
    "name" => "sha256sum",
    "ids" => [
        "literal" => "sha256sum"
    ],
    "options" => [
        "binary" => [
            "ids" => [
                "long" => "binary",
                "short" => "b",
            ],
        ],
        "check" => [
            "ids" => [
                "long" => "check",
                "short" => "c",
            ],
        ],
        "help" => [
            "ids" => [
                "long" => "help",
            ],
        ],
        "ignore-missing" => [
            "ids" => [
                "long" => "ignore-missing",
            ],
        ],
        "quiet" => [
            "ids" => [
                "long" => "quiet",
            ],
        ],
        "status" => [
            "ids" => [
                "long" => "status",
            ],
        ],
        "strict" => [
            "ids" => [
                "long" => "strict",
            ],
        ],
        "tag" => [
            "ids" => [
                "long" => "tag",
            ],
        ],
        "text" => [
            "ids" => [
                "long" => "text",
                "short" => "t",
            ],
        ],
        "version" => [
            "ids" => [
                "long" => "version",
            ],
        ],
        "warn" => [
            "ids" => [
                "long" => "warn",
                "short" => "w",
            ],
        ],
        "zero" => [
            "ids" => [
                "long" => "zero",
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
