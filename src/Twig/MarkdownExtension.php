<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class MarkdownExtension extends AbstractExtension
{
    private \Parsedown $parser;
    private \HTMLPurifier $HTMLPurifier;

    /**
     * MarkdownExtension constructor.
     */
    public function __construct()
    {
        $this->parser = new \Parsedown();
        $purifierConfig = \HTMLPurifier_Config::create([
            'Cache.DefinitionImpl' => null,
        ]);
        $this->HTMLPurifier = new \HTMLPurifier($purifierConfig);
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('md2html', [$this, 'markdownToHtml'], ['is_safe' => ['html']]),
        ];
    }

    public function markdownToHtml(string $content): string
    {
        $content = $this->parser->parse($content);
        return $this->HTMLPurifier->purify($content);
    }
}