<?php

declare(strict_types=1);

namespace Phare;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

final class TemplateRenderer
{
    private Environment $twig;

    /**
     * @param string|string[] $templatesDirs First match wins — put user overrides before defaults.
     * @param array<string, mixed> $globalVars
     */
    public function __construct(string|array $templatesDirs, array $globalVars = [])
    {
        $dirs = array_filter((array) $templatesDirs, 'is_dir');
        $loader = new FilesystemLoader($dirs);
        $this->twig = new Environment($loader, ['autoescape' => 'html']);

        $this->twig->addGlobal('base_path', '');
        foreach ($globalVars as $key => $value) {
            $this->twig->addGlobal($key, $value);
        }
    }

    /** @param array<string, mixed> $context */
    public function render(string $template, array $context = []): string
    {
        return $this->twig->render($template, $context);
    }
}
