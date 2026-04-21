<?php

declare(strict_types=1);

namespace Phare;

use RuntimeException;
use Symfony\Component\Yaml\Yaml;

final class ConfigLoader
{
    /** @var array<string, mixed> */
    private array $config;

    public function __construct(string $configPath)
    {
        if (!file_exists($configPath)) {
            throw new RuntimeException("Config file not found: {$configPath}");
        }

        $parsed = Yaml::parseFile($configPath);
        $this->config = \is_array($parsed) ? $parsed : [];
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->config[$key] ?? $default;
    }

    /** @return array<string, mixed> */
    public function all(): array
    {
        return $this->config;
    }
}
