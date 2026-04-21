<?php

declare(strict_types=1);

namespace Phare\Tests\Unit;

use Phare\ConfigLoader;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class ConfigLoaderTest extends TestCase
{
    private string $tmpDir;

    protected function setUp(): void
    {
        $this->tmpDir = sys_get_temp_dir() . '/phare_config_' . uniqid();
        mkdir($this->tmpDir);
    }

    protected function tearDown(): void
    {
        foreach ((array) glob($this->tmpDir . '/*') as $file) {
            unlink((string) $file);
        }
        rmdir($this->tmpDir);
    }

    public function testLoadsConfig(): void
    {
        $configPath = $this->tmpDir . '/config.yml';
        file_put_contents($configPath, "title: My Blog\nposts_per_page: 5\n");

        $loader = new ConfigLoader($configPath);

        $this->assertSame('My Blog', $loader->get('title'));
        $this->assertSame(5, $loader->get('posts_per_page'));
    }

    public function testGetDefault(): void
    {
        $configPath = $this->tmpDir . '/config.yml';
        file_put_contents($configPath, "title: Test\n");

        $loader = new ConfigLoader($configPath);

        $this->assertSame('default', $loader->get('missing_key', 'default'));
    }

    public function testThrowsOnMissingFile(): void
    {
        $this->expectException(RuntimeException::class);
        new ConfigLoader('/nonexistent/config.yml');
    }
}
