<?php

declare(strict_types=1);

namespace Phare\Tests\Unit;

use League\CommonMark\CommonMarkConverter;
use Phare\Post;
use PHPUnit\Framework\TestCase;

class PostTest extends TestCase
{
    private string $tmpDir;

    protected function setUp(): void
    {
        $this->tmpDir = sys_get_temp_dir() . '/phare_test_' . uniqid();
        mkdir($this->tmpDir);
    }

    protected function tearDown(): void
    {
        foreach ((array) glob($this->tmpDir . '/*') as $file) {
            unlink((string) $file);
        }
        rmdir($this->tmpDir);
    }

    public function testFromFileWithFrontmatter(): void
    {
        $markdown = <<<MD
            ---
            title: "Test Post"
            date: "2024-01-15"
            tags: [php, testing]
            category: dev
            draft: false
            ---
            Hello world!
            MD;

        $file = $this->tmpDir . '/test-post.md';
        file_put_contents($file, $markdown);

        $post = Post::fromFile($file, new CommonMarkConverter());

        $this->assertSame('Test Post', $post->getTitle());
        $this->assertSame('test-post', $post->getSlug());
        $this->assertSame(['php', 'testing'], $post->getTags());
        $this->assertSame('dev', $post->getCategory());
        $this->assertFalse($post->isDraft());
        $this->assertStringContainsString('Hello world!', $post->getContent());
        $this->assertSame('2024-01-15', $post->getDate()->format('Y-m-d'));
    }

    public function testFromFileWithoutFrontmatter(): void
    {
        $file = $this->tmpDir . '/simple-post.md';
        file_put_contents($file, "# Hello\n\nSimple post content.");

        $post = Post::fromFile($file, new CommonMarkConverter());

        $this->assertSame('simple-post', $post->getSlug());
        $this->assertSame([], $post->getTags());
        $this->assertNull($post->getCategory());
    }

    public function testDraftPost(): void
    {
        $file = $this->tmpDir . '/draft-post.md';
        file_put_contents($file, "---\ntitle: Draft\ndraft: true\n---\nContent.");

        $post = Post::fromFile($file, new CommonMarkConverter());

        $this->assertTrue($post->isDraft());
    }

    public function testGetUrl(): void
    {
        $file = $this->tmpDir . '/my-slug.md';
        file_put_contents($file, 'Content.');

        $post = Post::fromFile($file, new CommonMarkConverter());

        $this->assertSame('/posts/my-slug/', $post->getUrl());
    }
}
