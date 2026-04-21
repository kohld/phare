<?php

declare(strict_types=1);

namespace Phare\Tests\Unit;

use League\CommonMark\CommonMarkConverter;
use Phare\Collection;
use Phare\Post;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    private string $tmpDir;
    private CommonMarkConverter $converter;

    protected function setUp(): void
    {
        $this->tmpDir = sys_get_temp_dir() . '/phare_coll_' . uniqid();
        mkdir($this->tmpDir);
        $this->converter = new CommonMarkConverter();
    }

    protected function tearDown(): void
    {
        foreach ((array) glob($this->tmpDir . '/*') as $file) {
            unlink((string) $file);
        }
        rmdir($this->tmpDir);
    }

    private function makePost(string $slug, string $frontmatter = ''): Post
    {
        $file = $this->tmpDir . "/{$slug}.md";
        $content = $frontmatter ? "---\n{$frontmatter}\n---\nContent." : 'Content.';
        file_put_contents($file, $content);
        return Post::fromFile($file, $this->converter);
    }

    public function testFilterPublished(): void
    {
        $posts = [
            $this->makePost('published', "draft: false"),
            $this->makePost('draft', "draft: true"),
        ];

        $collection = new Collection($posts);
        $published = $collection->filterPublished();

        $this->assertCount(1, $published->all());
        $this->assertSame('published', $published->all()[0]->getSlug());
    }

    public function testFilterByTag(): void
    {
        $posts = [
            $this->makePost('post-a', "tags: [php, oss]"),
            $this->makePost('post-b', "tags: [oss]"),
            $this->makePost('post-c', "tags: [java]"),
        ];

        $collection = new Collection($posts);
        $result = $collection->filterByTag('oss');

        $this->assertCount(2, $result->all());
    }

    public function testFilterByCategory(): void
    {
        $posts = [
            $this->makePost('post-a', "category: dev"),
            $this->makePost('post-b', "category: life"),
        ];

        $collection = new Collection($posts);
        $result = $collection->filterByCategory('dev');

        $this->assertCount(1, $result->all());
    }

    public function testGetTags(): void
    {
        $posts = [
            $this->makePost('post-a', "tags: [php, oss]"),
            $this->makePost('post-b', "tags: [php]"),
        ];

        $tags = (new Collection($posts))->getTags();

        $this->assertContains('php', $tags);
        $this->assertSame('php', $tags[0]);
    }

    public function testPaginate(): void
    {
        $posts = array_map(
            fn (int $i) => $this->makePost("post-{$i}"),
            range(1, 5),
        );

        $pages = (new Collection($posts))->paginate(2);

        $this->assertCount(3, $pages);
        $this->assertCount(2, $pages[0]);
        $this->assertCount(1, $pages[2]);
    }
}
