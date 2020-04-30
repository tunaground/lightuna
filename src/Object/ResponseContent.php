<?php
namespace Lightuna\Object;

/**
 * Class ResponseContent
 * @package Lightuna\Object
 */
class ResponseContent
{
    /** @var string */
    private $content;

    /**
     * ResponseContent constructor.
     * @param string $content
     */
    public function __construct(string $content)
    {
        $this->content = $content;
    }

    public function newLineToBreak()
    {
        $this->content = str_replace(['\r\n', '\r', '\n'], '<br />', $this->content);
    }

    public function applyAsciiArtTag()
    {
        $this->content = preg_replace('/(\.aa)/', '<p class="mona">', $this->content);
        $this->content = preg_replace('/(aa\.)/', '</p>', $this->content);
    }

    public function applyAsciiArtTagAll()
    {
        $this->content = '<p class="mona">' . $this->content . '</p>';
    }

    public function applyHorizonTag()
    {
        $this->content = preg_replace('/(\.hr\.)/', '<hr />', $this->content);
    }

    public function applySpoilerTag()
    {
        $this->content = preg_replace(
            '/&lt;spo&gt;(((?!&lt;\/spo&gt;)[\s\S])+)&lt;\/spo&gt;/',
            '<span class="spoiler">\\1</span>',
            $this->content,
            -1
        );
    }

    public function applyColorTag()
    {
        $this->content = preg_replace(
            '/&lt;clr (#?[a-z0-9]+)&gt;(((?!&lt;\/clr&gt;)[\s\S])+)&lt;\/clr&gt;/',
            '<span style="color: \\1">\\2</span>',
            $this->content,
            -1
        );
        $this->content = preg_replace(
            '/&lt;clr (#?[a-z0-9]+) (#?[a-z0-9]+)&gt;(((?!&lt;\/clr&gt;)[\s\S])+)&lt;\/clr&gt;/',
            '<span style="color: \\1; text-shadow: 0px 0px 6px \\2;">\\3</span>',
            $this->content,
            -1
        );
    }

    public function applyRubyTag()
    {
        $this->content = preg_replace(
            '/&lt;ruby ([a-zA-Z0-9가-힣一-龥\s]+)&gt;(((?!&lt;\/ruby&gt;)[\s\S])+)&lt;\/ruby&gt;/',
            '<ruby>\\2<rt>\\1</rt></ruby>',
            $this->content,
            -1
        );
    }

    public function applyDiceTag()
    {
        $temp_text = preg_split("/(\.dice )(0|-?[1-9][0-9]*)( )(0|-?[1-9][0-9]*)(\.)/", $this->content, -1);
        if (preg_match_all("/(\.dice )(0|-?[1-9][0-9]*)( )(0|-?[1-9][0-9]*)(\.)/", $this->content, $matches,
            PREG_SET_ORDER)) {
            for ($i = 0; $i < sizeof($matches); $i++) {
                $dice_result[$i] = mt_rand($matches[$i][2], $matches[$i][4]);
            }
            $this->content = $temp_text[0] . '<span style="color:red; font-weight:600;">' . $matches[0][0] . ' = ' . $dice_result[0] . '</span>';
            for ($i = 1; $i < sizeof($dice_result); $i++) {
                $this->content .= $temp_text[$i] . '<span style="color:red; font-weight:600;">' . $matches[$i][0] . ' = ' . $dice_result[$i] . '</span>';
            }
            $this->content .= $temp_text[$i];
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->content;
    }
}
