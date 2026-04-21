# Getting Started

## Prerequisites

- Docker + Docker Compose

## 1. Clone

```bash
git clone https://github.com/kohld/phare.git my-site
cd my-site
```

## 2. Install dependencies

```bash
docker compose run --rm phare composer install
```

## 3. Generate site

```bash
docker compose --profile generate up
```

Output is written to `examples/basic-blog/output/`.

## 4. Preview locally

```bash
docker compose --profile preview up
```

Open [http://localhost:8080](http://localhost:8080).

---

## Your own project

### Project structure

```
my-blog/
├── config.yml
├── generate.php
├── posts/
│   └── 2024-01-01-hello-world.md
└── templates/       # optional — only files you want to override
    └── base.html.twig
```

### config.yml

```yaml
title: "My Blog"
description: "My site description"
url: "https://example.com"
language: en
output_dir: output
posts_dir: posts
templates_dir: templates   # optional, overrides library defaults
date_format: "F j, Y"
posts_per_page: 10
```

### generate.php

```php
<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Phare\ConfigLoader;
use Phare\Generator;

$config = new ConfigLoader(__DIR__ . '/config.yml');
$generator = new Generator($config, __DIR__);
$generator->generate();
```

### Post format

Markdown files in `posts/` with YAML frontmatter:

```markdown
---
title: "Hello, World!"
date: "2024-01-15"
tags: [php, tutorial]
category: development
draft: false
---

Post content here. Full **Markdown** supported.
```

---

## Template overrides

Only override the templates you need — all others fall back to library defaults.

```
templates/
└── base.html.twig   # only this one is customized
```

Available templates:

| Template | Purpose |
|---|---|
| `base.html.twig` | Page layout, `<head>`, nav, footer |
| `index.html.twig` | Post list with pagination |
| `post.html.twig` | Single post |
| `tag.html.twig` | Posts filtered by tag |
| `category.html.twig` | Posts filtered by category |

---

## Docker Compose integration

Add to your own `docker-compose.yml`:

```yaml
services:
  phare:
    image: ghcr.io/kohld/phare:latest
    volumes:
      - .:/site
    working_dir: /site
    command: ["php", "generate.php"]
```

Mount your project to `/site`, run to generate.
