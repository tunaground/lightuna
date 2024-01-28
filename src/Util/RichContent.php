<?php

namespace Lightuna\Util;

class RichContent
{
    public static function applyAll(string $content, bool $rich = true, bool $coverAA = false): string
    {
        $content = self::newLineToBreak($content);
        if ($rich === true) {
            $content = self::applyAsciiArtTag($content);
            $content = self::applyHorizonTag($content);
            $content = self::applySpoilerTag($content);
            $content = self::applyColorTag($content);
            $content = self::applyRubyTag($content);
            $content = self::applyDiceTag($content);
        }
        if ($coverAA === true) {
            $content = self::applyAsciiArtTagAll($content);
        }
        return $content;
    }

    public static function newLineToBreak(string $content): string
    {
        return str_replace(["\r\n", "\r", "\n"], '<br/>', $content);
    }

    public static function applyAsciiArtTag(string $content): string
    {
        return preg_replace('/(aa\.)/', '</p>',
            preg_replace('/(\.aa)/', '<p class="mona">', $content));
    }

    public static function applyAsciiArtTagAll(string $content): string
    {
        return "<p class='mona'>{$content}</p>";
    }

    public static function applyHorizonTag(string $content): string
    {
        return preg_replace('/(\.hr\.)/', '<hr />', $content);
    }

    public static function applySpoilerTag(string $content): string
    {
        return preg_replace(
            '/&lt;spo&gt;(((?!&lt;\/spo&gt;)[\s\S])+)&lt;\/spo&gt;/',
            '<span class="spoiler">\\1</span>',
            $content,
            -1
        );
    }

    public static function applyColorTag(string $content): string
    {
        $content = preg_replace(
            '/&lt;clr (#?[a-z0-9]+)&gt;(((?!&lt;\/clr&gt;)[\s\S])+)&lt;\/clr&gt;/',
            '<span style="color: \\1">\\2</span>',
            $content,
            -1
        );
        $content = preg_replace(
            '/&lt;clr (#?[a-z0-9]+) (#?[a-z0-9]+)&gt;(((?!&lt;\/clr&gt;)[\s\S])+)&lt;\/clr&gt;/',
            '<span style="color: \\1; text-shadow: 0px 0px 6px \\2;">\\3</span>',
            $content,
            -1
        );
        return $content;
    }

    public static function applyRubyTag(string $content): string
    {
        return preg_replace(
            '/&lt;ruby ([a-zA-Z0-9가-힣一-龥\s]+)&gt;(((?!&lt;\/ruby&gt;)[\s\S])+)&lt;\/ruby&gt;/',
            '<ruby>\\2<rt>\\1</rt></ruby>',
            $content,
            -1
        );
    }

    public static function applyDiceTag(string $content): string
    {
        $temp_text = preg_split("/(\.dice )(0|-?[1-9][0-9]*)( )(0|-?[1-9][0-9]*)(\.)/", $content, -1);
        if (preg_match_all("/(\.dice )(0|-?[1-9][0-9]*)( )(0|-?[1-9][0-9]*)(\.)/", $content, $matches,
            PREG_SET_ORDER)) {
            for ($i = 0; $i < sizeof($matches); $i++) {
                $dice_result[$i] = mt_rand($matches[$i][2], $matches[$i][4]);
            }
            $content = $temp_text[0] . '<span class="dice">' . $matches[0][0] . ' = ' . $dice_result[0] . '</span>';
            for ($i = 1; $i < sizeof($dice_result); $i++) {
                $content .= $temp_text[$i] . '<span class="dice">' . $matches[$i][0] . ' = ' . $dice_result[$i] . '</span>';
            }
            $content .= $temp_text[$i];
        }
        return $content;
    }
}