<?php
namespace App\Services;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkRenderer;
use Illuminate\Support\Str;

class MarkdownService
{
    protected CommonMarkConverter $converter;

    public function __construct()
    {
        $config = [
            'heading_permalink' => [
                'html_class' => 'heading-permalink',
                'insert' => 'before',
                'symbol' => '¶',
            ],
        ];

        $environment = new Environment($config);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new HeadingPermalinkExtension());

        $this->converter = new CommonMarkConverter([], $environment);
    }

    public function convertToHtml(string $markdown): string
    {
        $html = $this->converter->convert($markdown);

        // ショートコード [ad] を広告HTMLに置換
        $adHtml = view('ads.default')->render();
        $html = str_replace('[ad]', $adHtml, $html);
    
        return $html;
    }

    public function generateTOC(string $markdown): string
    {
        preg_match_all('/^(#{1,6})\s*(.+)$/m', $markdown, $matches, PREG_SET_ORDER);

        $toc = '<ul>';
        foreach ($matches as $match) {
            $level = strlen($match[1]);
            $text = htmlspecialchars($match[2]);
            $slug = Str::slug($text);
            $toc .= "<li class=\"toc-level-{$level}\"><a href=\"#{$slug}\">{$text}</a></li>";
        }
        $toc .= '</ul>';

        return $toc;
    }
}
