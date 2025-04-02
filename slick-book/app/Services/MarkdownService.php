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
                'id_prefix' => '', // 例: 'heading-'
                'insert' => 'before', // 'before' or 'after'
                'symbol' => '¶', // 任意
                'title' => 'Link to this heading',
            ],
            'commonmark' => [
                'enable_em' => true,
                'enable_strong' => true,
                'enable_lax_line_breaks' => true, // ← ここが自動改行の有効化
            ],
        ];

        $environment = new Environment($config);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new HeadingPermalinkExtension());

        $this->converter = new CommonMarkConverter([], $environment);
    }

    public function convertToHtml(string $markdown): string
    {
        // 先に [code lang="xxx"] を HTML化（Markdown変換前）
        $markdown = preg_replace_callback('/\[code(?:\s+lang=["\']?([a-zA-Z0-9]+)["\']?)?](.*?)\[\/code]/s', function ($matches) {
            $language = $matches[1] ?? '';
            $content = trim($matches[2]);

            // 特殊文字をエスケープ
            $escaped = htmlspecialchars($content);
            $class = $language ? "language-{$language}" : '';

            return "<pre class=\"code\"><code class=\"{$class}\">{$escaped}</code></pre>";
        }, $markdown);

        // ショートコード [note]...[/note] を <div class="note">...</div> に変換
        $markdown = preg_replace_callback('/\[note](.*?)\[\/note]/s', function ($matches) {
            return '<div class="note">' . nl2br(e(trim($matches[1]))) . '</div>';
        }, $markdown);

        // ショートコード [code]...[/code] を <code>...</code> に変換
        $markdown = preg_replace_callback('/\[mark](.*?)\[\/mark]/s', function ($matches) {
            return '<code>' . nl2br(e(trim($matches[1]))) . '</code>';
        }, $markdown);

        // ショートコード [ad] を広告HTMLに置換
        $adHtml = view('ads.default')->render();
        $markdown = str_replace('[ad]', $adHtml, $markdown);

        // ショートコード [toc] を見出しHTMLに置換
        $tocHtml = $this->generateTOC($markdown);
        $markdown = str_replace('[toc]', $tocHtml, $markdown);

        $markdown = str_replace("[br]", "\n<br>\n", $markdown);
        $html = $this->converter->convert($markdown);

        // 見出しタグにIDを付与
        $html = preg_replace_callback('/<h([1-6])>(.*?)<\/h\1>/', function ($matches) {
            $level = $matches[1];
            $text = strip_tags($matches[2]);
            $id = Str::slug($text, '-', 'ja');
            return "<h{$level} id=\"{$id}\">{$matches[2]}</h{$level}>";
        }, $html);

        return $html;
    }

    public function generateTOC(string $markdown): string
    {
        preg_match_all('/^(#{1,6})\s*(.+)$/m', $markdown, $matches, PREG_SET_ORDER);

        $toc = '<div class="toc my-4 mx-auto border border-gray-200 bg-gray-100"><ul>';
        foreach ($matches as $match) {
            $level = strlen($match[1]);
            $text = htmlspecialchars($match[2]);
            $slug = Str::slug($text, '-', 'ja');
            $toc .= "<li class=\"toc-level-{$level}\"><a href=\"#{$slug}\">{$text}</a></li>";
        }
        $toc .= '</ul></div>';

        return $toc;
    }
}
