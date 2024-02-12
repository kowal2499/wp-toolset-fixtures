<?php

namespace Erla\WpToolsetFixtures\Creator;

abstract class AbstractCreator
{
    abstract public function create(string $jsonFilePath): void;
    abstract public function greet(): void;

    protected function cliRun(string $command, array $args = ['return' => true]): string
    {
        return WP_CLI::runcommand($command, $args);
    }

    protected function cliSuccess(string $message): void
    {
        WP_CLI::success($message);
    }

    protected function cliFailure(string $message, bool $stopAfter = false): void
    {
        WP_CLI::error($message, $stopAfter);
    }

    protected function cliLine(string $message): void {
        WP_CLI::line($message);
    }

    protected function secureArg(string $item): string
    {
        return "\"" . addslashes($item) . "\"";
    }

    protected function buildCommand(string $cmd, array $args): string
    {
        return $cmd . ' ' . implode(' ', array_filter($args));
    }

    protected function getFixtures(string $jsonFilePath): array
    {
        $pages = file_get_contents($jsonFilePath);
        return json_decode($pages, true);
    }
}