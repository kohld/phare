<?php

declare(strict_types=1);

namespace Phare;

use DateTimeImmutable;
use DateTimeInterface;
use League\CommonMark\MarkdownConverter;
use Symfony\Component\Yaml\Yaml;

final class Post
{
    /**
     * @param string[] $tags
     */
    private function __construct(
        private readonly string $title,
        private readonly DateTimeImmutable $date,
        private readonly array $tags,
        private readonly ?string $category,
        private readonly bool $draft,
        private readonly string $slug,
        private readonly string $content,
        private readonly string $excerpt,
    ) {
    }

    public static function fromFile(string $filePath, MarkdownConverter $converter): self
    {
        $raw = (string) file_get_contents($filePath);
        [$frontmatter, $body] = self::splitFrontmatter($raw);

        $slug = basename($filePath, '.md');
        $content = $converter->convert($body)->getContent();

        $rawDate = $frontmatter['date'] ?? 'now';
        $date = $rawDate instanceof DateTimeInterface
            ? DateTimeImmutable::createFromInterface($rawDate)
            : new DateTimeImmutable(\is_scalar($rawDate) ? (string) $rawDate : 'now');

        return new self(
            title: \is_string($frontmatter['title'] ?? null) ? $frontmatter['title'] : $slug,
            date: $date,
            tags: array_values(array_filter(\is_array($frontmatter['tags'] ?? null) ? $frontmatter['tags'] : [], 'is_string')),
            category: \is_string($frontmatter['category'] ?? null) ? $frontmatter['category'] : null,
            draft: (bool) ($frontmatter['draft'] ?? false),
            slug: $slug,
            content: $content,
            excerpt: self::extractExcerpt($content),
        );
    }

    /** @return array{array<string, mixed>, string} */
    private static function splitFrontmatter(string $raw): array
    {
        if (!str_starts_with(ltrim($raw), '---')) {
            return [[], $raw];
        }

        $parts = preg_split('/^---\s*$/m', $raw, 3);

        if ($parts === false || \count($parts) < 3) {
            return [[], $raw];
        }

        $yaml = Yaml::parse(trim($parts[1]));

        return [\is_array($yaml) ? $yaml : [], trim($parts[2])];
    }

    private static function extractExcerpt(string $html, int $length = 200): string
    {
        $text = strip_tags($html);
        if (\strlen($text) <= $length) {
            return $text;
        }

        return substr($text, 0, $length) . '…';
    }

    public function getTitle(): string
    {
        return $this->title;
    }
    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }
    /** @return string[] */
    public function getTags(): array
    {
        return $this->tags;
    }
    public function getCategory(): ?string
    {
        return $this->category;
    }
    public function isDraft(): bool
    {
        return $this->draft;
    }
    public function getSlug(): string
    {
        return $this->slug;
    }
    public function getContent(): string
    {
        return $this->content;
    }
    public function getExcerpt(): string
    {
        return $this->excerpt;
    }
    public function getUrl(): string
    {
        return '/posts/' . $this->slug . '/';
    }
}
