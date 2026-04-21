---
title: "Getting Started with Phare"
date: "2024-02-01"
tags: [php, tutorial]
category: development
draft: false
---

Phare makes it easy to build a static blog with PHP, Twig, and Markdown.

## Setup

1. Install via Composer: `composer require kohld/phare`
2. Create a `config.yml` with your site settings
3. Write posts as Markdown files in your `posts/` directory
4. Run `php generate.php` to build your site

## Configuration

```yaml
title: "My Blog"
posts_dir: posts
output_dir: output
posts_per_page: 10
```

That's all you need to get started.
