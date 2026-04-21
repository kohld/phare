# GitHub Pages Deployment

## Overview

1. Create a GitHub repo for your blog
2. Add Phare as a Composer dependency
3. Write posts in Markdown
4. Push — GitHub Actions generates and deploys automatically

---

## 1. Project setup

### composer.json

```json
{
    "name": "yourname/my-blog",
    "require": {
        "kohld/phare": "^1.0"
    },
    "config": {
        "allow-plugins": {
            "phpstan/extension-installer": false
        }
    }
}
```

### Directory structure

```
my-blog/
├── .github/
│   └── workflows/
│       └── deploy.yml
├── posts/
│   └── 2024-01-01-hello-world.md
├── templates/          # optional — only files you want to override
├── config.yml
└── generate.php
```

### config.yml

```yaml
title: "My Blog"
description: "My blog description"
url: "https://yourname.github.io/my-blog"
language: en
output_dir: output
posts_dir: posts
base_path: /my-blog    # Reponame ohne trailing slash — leer lassen für yourname.github.io
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

### .gitignore

```
vendor/
output/
composer.lock
```

---

## 2. GitHub Action

Create `.github/workflows/deploy.yml`:

```yaml
name: Deploy to GitHub Pages

on:
  push:
    branches: [main]
  workflow_dispatch:

permissions:
  contents: read
  pages: write
  id-token: write

concurrency:
  group: pages
  cancel-in-progress: false

jobs:
  build:
    runs-on: ubuntu-latest

    steps:
      - uses: actions/checkout@v4

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.4'
          tools: composer

      - name: Install dependencies
        run: composer install --no-interaction --prefer-dist --no-dev

      - name: Generate site
        run: php generate.php

      - name: Upload artifact
        uses: actions/upload-pages-artifact@v3
        with:
          path: output/

  deploy:
    needs: build
    runs-on: ubuntu-latest
    environment:
      name: github-pages
      url: ${{ steps.deployment.outputs.page_url }}

    steps:
      - name: Deploy to GitHub Pages
        id: deployment
        uses: actions/deploy-pages@v4
```

---

## 3. Enable GitHub Pages

In your GitHub repo:

1. **Settings → Pages**
2. Source: **GitHub Actions**
3. Push to `main` — the Action runs and deploys automatically

Your site will be live at `https://yourname.github.io/my-blog`.

---

## 4. Writing posts

Add Markdown files to `posts/` with frontmatter:

```markdown
---
title: "My First Post"
date: "2024-01-15"
tags: [php, blog]
category: general
draft: false
---

Post content here.
```

Push to `main` → Action triggers → site updates within ~1 minute.

---

## Tips

**Custom domain** — add a `CNAME` file to `posts/` root (Phare copies nothing outside `output/`), or better: place it in a `static/` directory and copy it in `generate.php`:

```php
// generate.php — after $generator->generate();
copy(__DIR__ . '/CNAME', __DIR__ . '/output/CNAME');
```

**Draft posts** — set `draft: true` in frontmatter. Excluded from generation.

**Preview locally** before pushing:

```bash
docker compose run --rm phare php generate.php
docker compose --profile preview up
```

Open [http://localhost:8080](http://localhost:8080).
