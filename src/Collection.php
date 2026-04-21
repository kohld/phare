<?php

declare(strict_types=1);

namespace Phare;

final class Collection
{
    /** @param Post[] $posts */
    public function __construct(private array $posts)
    {
    }

    public function filterByTag(string $tag): self
    {
        return new self(array_values(array_filter(
            $this->posts,
            static fn (Post $p) => \in_array($tag, $p->getTags(), true),
        )));
    }

    public function filterByCategory(string $category): self
    {
        return new self(array_values(array_filter(
            $this->posts,
            static fn (Post $p) => $p->getCategory() === $category,
        )));
    }

    public function filterPublished(): self
    {
        return new self(array_values(array_filter(
            $this->posts,
            static fn (Post $p) => !$p->isDraft(),
        )));
    }

    public function sortByDate(bool $ascending = false): self
    {
        $posts = $this->posts;
        usort($posts, static function (Post $a, Post $b) use ($ascending): int {
            $cmp = $a->getDate() <=> $b->getDate();
            return $ascending ? $cmp : -$cmp;
        });

        return new self($posts);
    }

    /** @return Post[][] */
    public function paginate(int $perPage): array
    {
        return array_chunk($this->posts, max(1, $perPage));
    }

    /** @return string[] */
    public function getTags(): array
    {
        $counts = [];
        foreach ($this->posts as $post) {
            foreach ($post->getTags() as $tag) {
                $counts[$tag] = ($counts[$tag] ?? 0) + 1;
            }
        }
        arsort($counts);

        return array_keys($counts);
    }

    /** @return string[] */
    public function getCategories(): array
    {
        $seen = [];
        foreach ($this->posts as $post) {
            if ($post->getCategory() !== null) {
                $seen[$post->getCategory()] = true;
            }
        }

        return array_keys($seen);
    }

    /** @return Post[] */
    public function all(): array
    {
        return $this->posts;
    }

    public function count(): int
    {
        return \count($this->posts);
    }
}
