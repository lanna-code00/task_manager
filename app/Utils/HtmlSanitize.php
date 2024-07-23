<?php


namespace App\Utils;


class HtmlSanitize
{
    public static function sanitizeHtml($html)
    {
        // List of allowed HTML tags
        $allowedTags = '<p><a><strong><em><ul><ol><li><blockquote><code><pre><img><br><h1><h2><h3><h4><h5><h6><hr><table><tr><td><th><thead><tbody><tfoot><caption><div><span><br><hr><abbr><acronym><address><b><bdi><bdo><big><cite><code><del><dfn><font><i><ins><kbd><mark><output><q><s><samp><small><strike><strong><sub><sup><time><tt><u><var><center><article><aside><details><dialog><figcaption><figure><footer><header><main><menu><menuitem><nav><section><summary>';
        // Remove potentially harmful tags
        $html = preg_replace('#<(script|style)[^>]*>.*?</\\1>#si', '', $html);


        // Remove attributes except href from anchor tags
        $html = preg_replace_callback('/<a\s+([^>]*?)>/i', function ($matches) {
            $attrs = [];
            preg_match_all('/\s*([^=]+)="([^"]*)"/i', $matches[1], $attrs, PREG_SET_ORDER);
            $attrs = array_filter($attrs, function ($attr) {
                return strtolower($attr[1]) === 'href';
            });
            $attrs = array_map(function ($attr) {
                return $attr[0];
            }, $attrs);
            return '<a ' . implode(' ', $attrs) . '>';
        }, $html);


        // Remove disallowed tags and attributes
        $html = strip_tags($html, $allowedTags);


        return $html;
    }
}



