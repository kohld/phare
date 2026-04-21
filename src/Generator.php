<?php

declare(strict_types=1);

namespace Phare;

use League\CommonMark\CommonMarkConverter;

final class Generator
{
    /** @var array<string, mixed> */
    private array $config;
    private CommonMarkConverter $markdown;
    private TemplateRenderer $renderer;

    public function __construct(
        ConfigLoader $configLoader,
        private readonly string $baseDir,
    ) {
        $this->config = $configLoader->all();
        $this->markdown = $this->buildMarkdownConverter();
        $this->renderer = new TemplateRenderer(
            $this->resolveTemplatesDirs(),
            $this->config,
        );
    }

    public function generate(): void
    {
        $outputDir = $this->baseDir . '/' . ($this->config['output_dir'] ?? 'output');
        $this->ensureDir($outputDir);

        $collection = $this->loadPosts()->filterPublished()->sortByDate();

        $this->generatePosts($collection, $outputDir);
        $this->generateIndex($collection, $outputDir);
        $this->generateTagPages($collection, $outputDir);
        $this->generateCategoryPages($collection, $outputDir);

        echo "Generated {$collection->count()} posts → {$outputDir}\n";
    }

    private function loadPosts(): Collection
    {
        $postsDir = $this->baseDir . '/' . ($this->config['posts_dir'] ?? 'posts');

        if (!is_dir($postsDir)) {
            return new Collection([]);
        }

        $posts = [];
        foreach ((array) glob($postsDir . '/*.md') as $file) {
            $posts[] = Post::fromFile((string) $file, $this->markdown);
        }

        return new Collection($posts);
    }

    private function generatePosts(Collection $collection, string $outputDir): void
    {
        foreach ($collection->all() as $post) {
            $dir = $outputDir . '/posts/' . $post->getSlug();
            $this->ensureDir($dir);
            file_put_contents(
                $dir . '/index.html',
                $this->renderer->render('post.html.twig', [
                    'post' => $post,
                    'collection' => $collection,
                ]),
            );
        }
    }

    private function generateIndex(Collection $collection, string $outputDir): void
    {
        $perPage = \is_int($this->config['posts_per_page'] ?? null) ? $this->config['posts_per_page'] : 10;
        $pages = $collection->paginate($perPage);

        foreach ($pages as $i => $pagePosts) {
            $pageNum = $i + 1;
            $dir = $i === 0 ? $outputDir : $outputDir . '/page/' . $pageNum;
            $this->ensureDir($dir);
            file_put_contents(
                $dir . '/index.html',
                $this->renderer->render('index.html.twig', [
                    'posts' => $pagePosts,
                    'collection' => $collection,
                    'page' => $pageNum,
                    'total_pages' => \count($pages),
                ]),
            );
        }
    }

    private function generateTagPages(Collection $collection, string $outputDir): void
    {
        foreach ($collection->getTags() as $tag) {
            $tagCollection = $collection->filterByTag($tag)->sortByDate();
            $dir = $outputDir . '/tag/' . $this->slugify($tag);
            $this->ensureDir($dir);
            file_put_contents(
                $dir . '/index.html',
                $this->renderer->render('tag.html.twig', [
                    'tag' => $tag,
                    'posts' => $tagCollection->all(),
                    'collection' => $collection,
                ]),
            );
        }
    }

    private function generateCategoryPages(Collection $collection, string $outputDir): void
    {
        foreach ($collection->getCategories() as $category) {
            $catCollection = $collection->filterByCategory($category)->sortByDate();
            $dir = $outputDir . '/category/' . $this->slugify($category);
            $this->ensureDir($dir);
            file_put_contents(
                $dir . '/index.html',
                $this->renderer->render('category.html.twig', [
                    'category' => $category,
                    'posts' => $catCollection->all(),
                    'collection' => $collection,
                ]),
            );
        }
    }

    private function ensureDir(string $dir): void
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    private function slugify(string $text): string
    {
        return (string) preg_replace('/[^a-z0-9]+/', '-', strtolower($text));
    }

    /** @return string[] */
    private function resolveTemplatesDirs(): array
    {
        $dirs = [];

        if (isset($this->config['templates_dir'])) {
            $dirs[] = $this->baseDir . '/' . $this->config['templates_dir'];
        }

        $dirs[] = __DIR__ . '/../templates';

        return $dirs;
    }

    private function buildMarkdownConverter(): CommonMarkConverter
    {
        return new CommonMarkConverter(['html_input' => 'strip', 'allow_unsafe_links' => false]);
    }
}
