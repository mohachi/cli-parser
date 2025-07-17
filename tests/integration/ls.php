<?php

use Mohachi\CliParser\Command;
use Mohachi\CliParser\IdTokenizer\LiteralIdTokenizer;
use Mohachi\CliParser\IdTokenizer\LongIdTokenizer;
use Mohachi\CliParser\IdTokenizer\ShortIdTokenizer;

$cmd = new Command("ls");
$cmd->id(LiteralIdTokenizer::class, "ls");
$cmd->opt("all")
    ->id(LongIdTokenizer::class, "--all")
    ->id(ShortIdTokenizer::class, "-a");
$cmd->opt("directory")
    ->id(LongIdTokenizer::class, "--directory")
    ->id(ShortIdTokenizer::class, "-d");
$cmd->opt("color")
    ->id(LongIdTokenizer::class, "--color")
    ->arg("when");
$cmd->arg("target");

$examples = [
    [
        "line" => ["ls", "."],
        "syntax" => (object) [
            "name" => "ls",
            "id" => "ls",
            "options" => [],
            "arguments" => (object) [
                "target" => ".",
            ],
        ],
    ],
    
    [
        "line" => ["ls", "--all", "."],
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
    
    [
        "line" => ["ls", "--color", "never", "/"],
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
    
    [
        "line" => ["ls", "--color=always", "~/Documents"],
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
    
    [
        "line" => ["ls", "--directory", "--color=always", "--all", "/var"],
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
];
