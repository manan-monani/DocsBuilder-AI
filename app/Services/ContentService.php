<?php

namespace App\Services;

class ContentService
{
    public function process(string $html): array
    {
        $toc = [];
        $processedHtml = preg_replace_callback('/<h([1-6])(.*?)>(.*?)<\/h\1>/i', function ($match) use (&$toc) {
            $level = (int) $match[1];
            $text = strip_tags($match[3]);
            $slug = \Illuminate\Support\Str::slug($text);
            $id = $slug;

            $toc[] = [
                'level' => $level,
                'text' => $text,
                'id' => $id,
            ];

            return "<h{$level}{$match[2]} id=\"{$id}\">{$match[3]}</h{$level}>";
        }, $html);

        return [
            'html' => $processedHtml,
            'toc' => $toc,
        ];
    }

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
}
