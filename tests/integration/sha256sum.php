<?php

use Mohachi\CliParser\Command;
use Mohachi\CliParser\IdTokenizer\LiteralIdTokenizer;
use Mohachi\CliParser\IdTokenizer\LongIdTokenizer;
use Mohachi\CliParser\IdTokenizer\ShortIdTokenizer;

$cmd = new Command("sha256sum");
$cmd->id(LiteralIdTokenizer::class, "sha256sum");
$cmd->opt("binary")
    ->id(LongIdTokenizer::class, "--binary")
    ->id(ShortIdTokenizer::class, "-b");
$cmd->opt("check")
    ->id(LongIdTokenizer::class, "--check")
    ->id(ShortIdTokenizer::class, "-c");
$cmd->opt("text")
    ->id(LongIdTokenizer::class, "--text")
    ->id(ShortIdTokenizer::class, "-t");
$cmd->opt("help")
    ->id(LongIdTokenizer::class, "--help");
$cmd->opt("ignore-missing")
    ->id(LongIdTokenizer::class, "--ignore-missing");
$cmd->opt("quiet")
    ->id(LongIdTokenizer::class, "--quiet");
$cmd->opt("status")
    ->id(LongIdTokenizer::class, "--status");
$cmd->opt("strict")
    ->id(LongIdTokenizer::class, "--strict");
$cmd->opt("tag")
    ->id(LongIdTokenizer::class, "--tag");
$cmd->opt("version")
    ->id(LongIdTokenizer::class, "--version");
$cmd->opt("zero")
    ->id(LongIdTokenizer::class, "--zero");
$cmd->arg("FILE");

$examples = [
    [
        "line" => ["sha256sum", "--ignore-missing", "--check", "--quiet", "path/to/file.sha256"],
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
];
